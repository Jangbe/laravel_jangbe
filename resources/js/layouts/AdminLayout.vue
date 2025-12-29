<script setup>
import { ref } from 'vue'
import { faArrowRightFromBracket, faHeart, faHospital, faUserInjured } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useRouter } from 'vue-router';
import { useAuth } from '@/composables/auth';

const isSidebarOpen = ref(false)

const router = useRouter()
const auth = useAuth()
const logout = async () => {
    await auth.logout()
    router.replace('/login')
}
</script>

<template>
    <div class="flex h-screen w-full bg-gray-100 overflow-hidden font-sans text-gray-900">

        <aside :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-indigo-950 text-white transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0 shrink-0 shadow-xl">
            <div class="flex items-center justify-center h-20 border-b border-white/5">
                <h1 class="text-3xl font-bold tracking-tight">RS <span class="text-orange-400">Jangbe</span></h1>
            </div>

            <nav class="mt-6 px-4 space-y-2">
                <router-link to="/rumah-sakit"
                    class="flex items-center px-4 py-3 rounded-xl transition-all group [&:not(.router-link-exact-active)]:hover:bg-white/10"
                    active-class="laravel-gradient text-white shadow-lg shadow-orange-500/30">
                    <font-awesome-icon :icon="faHospital" class="mr-3" />
                    <span class="font-medium text-sm">Rumah Sakit</span>
                </router-link>

                <router-link to="/pasien"
                    class="flex items-center px-4 py-3 rounded-xl transition-all group [&:not(.router-link-exact-active)]:hover:bg-white/10"
                    active-class="laravel-gradient text-white shadow-lg shadow-orange-500/30">
                    <font-awesome-icon :icon="faUserInjured" class="mr-3" />
                    <span class="font-medium text-sm">Pasien</span>
                </router-link>

                <button @click="logout"
                    class="flex items-center px-4 py-3 rounded-xl transition-all group [&:not(.router-link-exact-active)]:hover:bg-white/10 w-full">
                    <font-awesome-icon :icon="faArrowRightFromBracket" class="mr-3" />
                    <span class="font-medium text-sm">Keluar</span>
                </button>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white h-20 flex items-center justify-between px-6 shadow-sm z-10">
                <button @click="isSidebarOpen = !isSidebarOpen"
                    class="p-2 rounded-lg lg:hidden [&:not(.router-link-exact-active)]hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>

                <div class="flex-1 px-4 lg:px-0">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-widest">Panel Manajemen</h2>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-md font-bold text-gray-800">{{ auth.user.value?.name }}</p>
                        <p class="text-sm text-gray-400">{{ auth.user.value?.username }}</p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-full laravel-gradient flex items-center justify-center text-white font-bold shadow-md text-2xl">
                        {{ auth.user.value?.name?.at(0) }}
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto md:p-6 p-2 bg-[#f8fafc]">
                <div class="max-w-7xl mx-auto">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 md:p-8 p-4 min-h-125">
                        <router-view />
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <p class="text-gray-500 text-sm">
                        &copy; {{ new Date().getFullYear() }} Devi Mulyana.
                    </p>
                    <p class="text-gray-400 text-xs mt-1">
                        Dibuat dengan
                        <FontAwesomeIcon :icon="faHeart" class="text-red-500 mx-1" /> menggunakan Tailwind CSS
                    </p>
                </div>
            </main>
        </div>

        <div v-if="isSidebarOpen" @click="isSidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/20 backdrop-blur-sm lg:hidden"></div>
    </div>
</template>
