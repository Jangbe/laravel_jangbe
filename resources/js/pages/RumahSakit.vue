<script setup>
import { faEnvelope, faPencil, faPhone, faTrash, faChevronLeft, faChevronRight } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { ref, shallowRef, onMounted } from 'vue'
import { useToast } from '@/composables/toast'
import { AxiosError } from 'axios'
import debounce from 'debounce'
import axios from '@axios'

const toast = useToast()

// State Modal & Form
const isModalOpen = ref(false)
const isEdit = ref(false)
const form = ref({ id: null, name: '', address: '', email: '', phone: '' })

const openModal = (data = null) => {
    if (data) {
        isEdit.value = true
        form.value = { ...data }
    } else {
        isEdit.value = false
        form.value = { id: null, name: '', address: '', email: '', phone: '' }
    }
    isModalOpen.value = true
}

const saveRS = async () => {
    try {
        if (isEdit.value) {
            const { data } = await axios.put('hospital/' + form.value.id, form.value)
            toast.showToast("Data Rumah Sakit", data.message)
        } else {
            const { data } = await axios.post('hospital', form.value)
            toast.showToast("Data Rumah Sakit", data.message)
        }
        fetchHospitals(1)
    } catch (e) {
        if (e instanceof AxiosError) {
            toast.showToast("Terdapat Kesalahan", e.response.data.message, "error")
        }
    }
    isModalOpen.value = false
}

const deleteRS = async (id) => {
    if (!confirm('Yakin ingin menghapus data ini?')) return

    try {
        const { data } = await axios.delete('hospital/' + id)
        toast.showToast("Data Rumah Sakit", data.message)
        fetchHospitals(1)
    } catch (e) {
        if (e instanceof AxiosError) {
            toast.showToast("Terdapat Kesalahan", e.response.data.message, "error")
        }
    }
}

// State untuk data dari Laravel
const search = ref('')
const paginationData = shallowRef({
    data: [],
    current_page: 1,
    last_page: 1,
    total: 0,
    prev_page_url: null,
    next_page_url: null,
    links: []
})

const fetchHospitals = async (page = 1) => {
    const response = await axios.get(`/hospital?page=${page}&search=${search.value}`)
    paginationData.value = response.data
}

const searchUpdated = debounce(() => fetchHospitals(1), 500)

onMounted(fetchHospitals)
</script>

<template>
    <div>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Rumah Sakit</h1>
                <p class="text-sm text-gray-500">Kelola daftar cabang rumah sakit terdaftar.</p>
            </div>
            <div class="flex gap-2">
                <div class="flex">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1"></label>
                    <input v-model="search" type="text" required placeholder="Cari Rumah Sakit..."
                        class="px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all"
                        @keyup="searchUpdated">
                </div>

                <button @click="openModal()"
                    class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-orange-500/30 transition-all flex items-center gap-2">
                    <span>+</span> Tambah RS
                </button>
            </div>
        </div>

        <div class="overflow-x-auto border border-gray-100 rounded-2xl shadow-sm">
            <table class="w-full text-left bg-white border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-xs uppercase tracking-widest border-b border-gray-100">
                        <th class="px-6 py-4 font-semibold">Nama Rumah Sakit</th>
                        <th class="px-6 py-4 font-semibold">Alamat</th>
                        <th class="px-6 py-4 font-semibold">Kontak</th>
                        <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="rs in paginationData.data" :key="rs.id" class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-700">{{ rs.name }}</td>
                        <td class="px-6 py-4 text-gray-500 text-sm">{{ rs.address }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div class="flex flex-col">
                                <span>
                                    <FontAwesomeIcon :icon="faEnvelope" class="mr-3" />
                                    {{ rs.email }}
                                </span>
                                <span>
                                    <FontAwesomeIcon :icon="faPhone" class="mr-3" />
                                    {{ rs.phone }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <button @click="openModal(rs)"
                                    class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                    title="Edit">
                                    <FontAwesomeIcon :icon="faPencil" />
                                </button>
                                <button @click="deleteRS(rs.id)"
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
                    <button @click="fetchHospitals(paginationData.current_page - 1)"
                        :disabled="paginationData.current_page === 1"
                        class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-400 hover:text-orange-500 hover:border-orange-500 disabled:opacity-50 disabled:hover:text-gray-400 disabled:hover:border-gray-200 transition-all">
                        <font-awesome-icon :icon="faChevronLeft" size="xs" />
                    </button>

                    <template v-for="(link, index) in paginationData.links" :key="index">
                        <button v-if="!isNaN(link.label)" @click="fetchHospitals(parseInt(link.label))" :class="[
                            'w-10 h-10 rounded-xl font-bold text-sm transition-all',
                            paginationData.current_page == link.label
                                ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/30'
                                : 'bg-white border border-gray-200 text-gray-500 hover:border-orange-500 hover:text-orange-500'
                        ]">
                            {{ link.label }}
                        </button>
                    </template>

                    <button @click="fetchHospitals(paginationData.current_page + 1)"
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
                        class="relative bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                        <div class="p-8">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-bold text-gray-800">
                                    {{ isEdit ? 'Edit Data RS' : 'Tambah Rumah Sakit' }}
                                </h3>
                                <button @click="isModalOpen = false"
                                    class="text-gray-400 hover:text-gray-600">✕</button>
                            </div>

                            <form @submit.prevent="saveRS" class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Nama Rumah
                                        Sakit</label>
                                    <input v-model="form.name" type="text" required placeholder="Contoh: RS Jangbe"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Alamat</label>
                                    <textarea v-model="form.address" required placeholder="Alamat lengkap..." rows="2"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 outline-none transition-all"></textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-400 uppercase mb-1">Email</label>
                                        <input v-model="form.email" type="email" required placeholder="rs@mail.com"
                                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 outline-none transition-all">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-400 uppercase mb-1">Telepon</label>
                                        <input v-model="form.phone" type="text" required placeholder="021-xxxx"
                                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 outline-none transition-all">
                                    </div>
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