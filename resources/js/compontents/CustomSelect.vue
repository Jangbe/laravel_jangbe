<script lang="ts" setup>
import { ref, computed, onMounted, onUnmounted, useTemplateRef, watch } from 'vue'
import { faSearch, faUserInjured, faChevronDown, faCheck, faSpinner } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import debounce from 'debounce'

export type Opt = {
    id: number
    name: string
}

const props = defineProps<{
    options: Array<Opt>
    loading?: boolean
    hasMore?: boolean // Dari Laravel: current_page < last_page
}>()

const emit = defineEmits<{
    (e: 'search', query: string): void
    (e: 'loadMore'): void
}>()

const modelValue = defineModel<number | null>()

const isDropdownOpen = ref(false)
const searchQuery = ref('')
const containerRef = useTemplateRef('containerRef')
const searchRef = useTemplateRef('searchRef')
const listRef = useTemplateRef('listRef')

// Fungsi Debounce Search agar tidak hit API setiap huruf
const debouncedSearch = debounce((val: string) => {
    emit('search', val)
}, 500)

watch(searchQuery, (newVal) => {
    if (!newVal) return

    debouncedSearch(newVal)
})

const selectedLabel = computed(() => {
    const selected = props.options.find(opt => opt.id === modelValue.value)
    return selected ? selected.name : 'Pilih Rumah Sakit...'
})

const selectOption = (option: Opt) => {
    modelValue.value = option.id
    isDropdownOpen.value = false
    searchQuery.value = ''
}

// DETEKSI SCROLL (Infinite Scroll)
const handleScroll = (e: Event) => {
    const el = e.target as HTMLElement
    // Jika scroll sudah dibawah dan tidak sedang loading dan masih ada data
    if (el.scrollHeight - el.scrollTop <= el.clientHeight) {
        if (!props.loading && props.hasMore) {
            emit('loadMore')
        }
    }
}

const handleClickOutside = (event: PointerEvent) => {
    if (containerRef.value && !containerRef.value.contains(event.target as Node)) {
        isDropdownOpen.value = false
    }
}

const optIndex = ref(0)
const handleArrowKey = (event: KeyboardEvent) => {
    switch (event.key) {
        case "ArrowUp":
            event.preventDefault()
            event.stopPropagation()

            optIndex.value = (optIndex.value - 1 + props.options.length) % props.options.length
            break
        case "ArrowDown":
            event.preventDefault()
            event.stopPropagation()

            optIndex.value = (optIndex.value + 1) % props.options.length
            break
        case "Enter":
            event.preventDefault()
            event.stopPropagation()

            const target = props.options[optIndex.value]
            if (target) selectOption(target)
            return
        default:
            return
    }

    if (optIndex.value == (props.options.length - 1) && props.hasMore) {
        setTimeout(() => {
            listRef.value?.children.item(optIndex.value + 1)?.scrollIntoView({
                block: 'nearest',
                behavior: 'smooth'
            })
        }, 50)
        return emit("loadMore")
    }

    listRef.value?.children.item(optIndex.value)?.scrollIntoView({
        block: 'nearest',
        behavior: 'smooth'
    })
}
watch(searchRef, (current, before) => {
    if (current == null) {
        before?.removeEventListener('keydown', handleArrowKey)
    } else {
        current.addEventListener('keydown', handleArrowKey)
    }
})

onMounted(() => window.addEventListener('click', handleClickOutside))
onUnmounted(() => window.removeEventListener('click', handleClickOutside))

const handleClickBtn = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
    setTimeout(() => searchRef.value?.focus(), 100)
}
</script>

<template>
    <!-- class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 border border-transparent rounded-2xl transition-all outline-none" -->
    <div class="relative" ref="containerRef">
        <button type="button" @click="handleClickBtn"
            class="w-full flex items-center justify-between px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 outline-none transition-all"
            :class="isDropdownOpen ? 'ring-2 ring-orange-500 bg-white border-orange-500' : 'hover:bg-gray-100'">
            <div class="flex items-center gap-3 text-left">
                <span :class="modelValue ? 'text-gray-800 font-medium' : 'text-gray-400'" class="truncate w-48 sm:w-64">
                    {{ selectedLabel }}
                </span>
            </div>
            <font-awesome-icon :icon="faChevronDown" class="text-gray-300 transition-transform"
                :class="{ 'rotate-180': isDropdownOpen }" />
        </button>

        <Transition name="dropdown">
            <div v-if="isDropdownOpen"
                class="absolute box-border z-50 mt-2 w-full bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">

                <div class="p-3 border-b border-gray-50 text-gray-900">
                    <div class="relative">
                        <font-awesome-icon :icon="faSearch"
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-sm" />
                        <input v-model="searchQuery" type="text" placeholder="Cari rumah sakit..."
                            class="w-full pl-9 pr-4 py-2 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-orange-500 outline-none"
                            @click.stop ref="searchRef" />
                    </div>
                </div>

                <ul ref="listRef" @scroll="handleScroll" class="max-h-60 overflow-y-auto py-2 custom-scrollbar">
                    <li v-for="(opt, idx) in options" :key="opt.id" @click="selectOption(opt)"
                        class="px-4 py-3 flex items-center justify-between cursor-pointer transition-colors hover:bg-orange-50 text-gray-900"
                        :class="idx == optIndex ? 'bg-gray-300' : ''">
                        <span :class="modelValue === opt.id ? 'text-orange-600 font-bold' : 'text-gray-600'">
                            {{ opt.name }}
                        </span>
                        <font-awesome-icon v-if="modelValue === opt.id" :icon="faCheck"
                            class="text-orange-500 text-sm" />
                    </li>

                    <li v-if="loading" class="px-4 py-3 text-center text-orange-500">
                        <font-awesome-icon :icon="faSpinner" spin />
                    </li>

                    <li v-if="options.length === 0 && !loading" class="px-4 py-8 text-center text-gray-400 text-sm">
                        Data tidak ditemukan...
                    </li>
                </ul>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #fb923c;
}

.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 0.2s ease-out;
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>