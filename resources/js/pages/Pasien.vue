<script lang="ts" setup>
import { faPencil, faPhone, faTrash, faChevronLeft, faChevronRight } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { ref, shallowRef, onMounted } from 'vue'
import { useToast } from '@/composables/toast'
import { AxiosError } from 'axios'
import CustomSelect, { type Opt } from '@/compontents/CustomSelect.vue'
import debounce from 'debounce'
import axios from '@axios'

const toast = useToast()

// State Modal & Form
type Patient = {
    id: number | null
    name: string
    address: string
    phone: string
    hospital_id: number | null
    hospital?: {
        id: number
        name: string
    }
}
const isModalOpen = ref(false)
const isEdit = ref(false)
const form = ref<Patient>({ id: null, name: '', address: '', phone: '', hospital_id: null })

const openModal = (data: Patient | null = null) => {
    if (data) {
        isEdit.value = true
        form.value = { ...data }
        hospitals.value = [data.hospital!]
    } else {
        isEdit.value = false
        form.value = { id: null, name: '', address: '', phone: '', hospital_id: null }
    }
    isModalOpen.value = true
}

const savePT = async () => {
    try {
        if (isEdit.value) {
            const { data } = await axios.put('patient/' + form.value.id, form.value)
            toast.showToast("Data Pasien", data.message)
        } else {
            const { data } = await axios.post('patient', form.value)
            toast.showToast("Data Pasien", data.message)
        }
        fetchPatients(1)
    } catch (e) {
        if (e instanceof AxiosError) {
            toast.showToast("Terdapat Kesalahan", e.response!.data.message, "error")
        }
    }
    isModalOpen.value = false
}

const deleteRS = async (id: number) => {
    if (!confirm('Yakin ingin menghapus data ini?')) return

    try {
        const { data } = await axios.delete('patient/' + id)
        toast.showToast("Data Pasien", data.message)
        fetchPatients(1)
    } catch (e) {
        if (e instanceof AxiosError) {
            toast.showToast("Terdapat Kesalahan", e.response!.data.message, "error")
        }
    }
}

// State untuk data dari Laravel
const search = ref('')
const paginationData = shallowRef({
    data: [] as Patient[],
    current_page: 1,
    last_page: 1,
    total: 0,
    prev_page_url: null,
    next_page_url: null,
    links: [] as { label: string, active: boolean, url: null | string }[]
})

const fetchPatients = async (page = 1) => {
    const response = await axios.get(`/patient?page=${page}&search=${search.value}`)
    paginationData.value = response.data
}

const searchUpdated = debounce(() => fetchPatients(1), 500)

onMounted(fetchPatients)

const hospitals = ref<Opt[]>([])
const loading = ref(false)
const page = ref(1)
const hasMore = ref(true)
const searchKey = ref('')

const loadData = async (isNewSearch = false) => {
    if (isNewSearch) {
        page.value = 1
        hospitals.value = []
        hasMore.value = true
    }

    loading.value = true
    try {
        const res = await axios.get(`/hospital?page=${page.value}&search=${searchKey.value}`)

        hospitals.value.push(...res.data.data) // Laravel data array
        hasMore.value = res.data.current_page < res.data.last_page
        page.value++
    } finally {
        loading.value = false
    }
}

// Dipicu saat user ngetik di komponen
const handleSearch = (query: string) => {
    searchKey.value = query
    loadData(true)
}

// Dipicu saat user scroll mentok bawah
const handleLoadMore = () => {
    loadData()
}
</script>

<template>
    <div>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Pasien</h1>
                <p class="text-sm text-gray-500">Kelola daftar pasien.</p>
            </div>
            <div class="flex gap-2">
                <div class="flex">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1"></label>
                    <input v-model="search" type="text" required placeholder="Cari Pasien..."
                        class="px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all"
                        @keyup="searchUpdated">
                </div>

                <button @click="openModal()"
                    class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-orange-500/30 transition-all flex items-center gap-2">
                    <span>+</span> Tambah Pasien
                </button>
            </div>
        </div>

        <div class="overflow-x-auto border border-gray-100 rounded-2xl shadow-sm">
            <table class="w-full text-left bg-white border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-xs uppercase tracking-widest border-b border-gray-100">
                        <th class="px-6 py-4 font-semibold">Nama Pasien</th>
                        <th class="px-6 py-4 font-semibold">Alamat</th>
                        <th class="px-6 py-4 font-semibold">No Telepon</th>
                        <th class="px-6 py-4 font-semibold">Rumah Sakit</th>
                        <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="pt in paginationData.data" :key="pt.id || ''"
                        class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-700">{{ pt.name }}</td>
                        <td class="px-6 py-4 text-gray-500 text-sm">{{ pt.address }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <span>
                                <FontAwesomeIcon :icon="faPhone" class="mr-3" />
                                {{ pt.phone }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-sm">{{ pt.hospital!.name }}</td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <button @click="openModal(pt)"
                                    class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                    title="Edit">
                                    <FontAwesomeIcon :icon="faPencil" />
                                </button>
                                <button @click="deleteRS(pt.id!)"
                                    class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                    <FontAwesomeIcon :icon="faTrash" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div
                class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-sm text-gray-500">
                    Menampilkan <span class="font-bold text-gray-800">{{ paginationData.data.length }}</span> dari <span
                        class="font-bold text-gray-800">{{ paginationData.total }}</span> data
                </p>

                <nav class="flex items-center gap-1">
                    <button @click="fetchPatients(paginationData.current_page - 1)"
                        :disabled="paginationData.current_page === 1"
                        class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-400 hover:text-orange-500 hover:border-orange-500 disabled:opacity-50 disabled:hover:text-gray-400 disabled:hover:border-gray-200 transition-all">
                        <font-awesome-icon :icon="faChevronLeft" size="xs" />
                    </button>

                    <template v-for="(link, index) in paginationData.links" :key="index">
                        <button v-if="!isNaN(parseInt(link.label))" @click="fetchPatients(parseInt(link.label))" :class="[
                            'w-10 h-10 rounded-xl font-bold text-sm transition-all',
                            paginationData.current_page == parseInt(link.label)
                                ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/30'
                                : 'bg-white border border-gray-200 text-gray-500 hover:border-orange-500 hover:text-orange-500'
                        ]">
                            {{ link.label }}
                        </button>
                    </template>

                    <button @click="fetchPatients(paginationData.current_page + 1)"
                        :disabled="paginationData.current_page === paginationData.last_page"
                        class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-400 hover:text-orange-500 hover:border-orange-500 disabled:opacity-50 transition-all">
                        <font-awesome-icon :icon="faChevronRight" size="xs" />
                    </button>
                </nav>
            </div>
        </div>

        <Teleport to="body">
            <Transition name="modal-fade">
                <div v-if="isModalOpen" class="fixed inset-0 z-60 flex items-center justify-center p-4">
                    <div @click="isModalOpen = false" class="absolute inset-0 bg-gray-900/40 backdrop-blur-xs"></div>

                    <div
                        class="relative bg-white w-full max-w-md rounded-3xl shadow-2xl animate-in fade-in zoom-in duration-200">
                        <div class="p-8">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-bold text-gray-800">
                                    {{ isEdit ? 'Edit Data Pasien' : 'Tambah Data Pasien' }}
                                </h3>
                                <button @click="isModalOpen = false"
                                    class="text-gray-400 hover:text-gray-600">✕</button>
                            </div>

                            <form @submit.prevent="savePT" class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Nama
                                        Pasien</label>
                                    <input v-model="form.name" type="text" required placeholder="Contoh: John Doe"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Alamat</label>
                                    <textarea v-model="form.address" required placeholder="Alamat lengkap..." rows="2"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 outline-none transition-all"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Telepon</label>
                                    <input v-model="form.phone" type="text" required placeholder="021-xxxx"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Rumah
                                        Sakit</label>
                                    <custom-select v-model="form.hospital_id" @load-more="handleLoadMore"
                                        @search="handleSearch" :has-more="hasMore" :options="hospitals"
                                        :loading="loading" />
                                </div>

                                <div class="pt-4 flex gap-3">
                                    <button type="button" @click="isModalOpen = false"
                                        class="flex-1 px-4 py-3 border border-gray-200 text-gray-500 rounded-xl font-semibold hover:bg-gray-50 transition-all">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="flex-1 px-4 py-3 bg-orange-500 text-white rounded-xl font-semibold hover:bg-orange-600 shadow-lg shadow-orange-500/30 transition-all">
                                        Simpan Data
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

.modal-fade-enter-active .relative {
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal-fade-leave-active .relative {
    transition: all 0.2s ease-in;
}

.modal-fade-enter-from .relative {
    transform: scale(0.9) translateY(20px);
}

.modal-fade-leave-to .relative {
    transform: scale(0.95);
}
</style>