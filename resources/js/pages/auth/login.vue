<script lang="ts" setup>
import { ref, computed } from 'vue';
import {
    faEye,
    faEyeSlash,
    faFire,
    faKey,
    faLock,
    faUser,
    faSignInAlt,
    faHeart,
    faIdCard
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { AxiosError } from 'axios';
import { useRouter } from 'vue-router';
import { useAuth } from '@/composables/auth';
import axios from '@axios';
import { useToast } from '@/composables/toast';

// State untuk form
const username = ref('superadmin');
const password = ref('password');
const rememberMe = ref(true);
const showPassword = ref(false);

// Computed untuk icon password
const passwordIcon = computed(() => showPassword.value ? faEyeSlash : faEye);
const passwordType = computed(() => showPassword.value ? 'text' : 'password');

// Methods
const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const router = useRouter()
const toast = useToast()
const auth = useAuth()

const handleSubmit = async (e: Event) => {
    e.preventDefault();

    // Validasi sederhana
    if (!username.value || !password.value) {
        toast.showToast('Login Gagal', 'Harap isi username dan kata sandi dengan benar.', 'error');
        return;
    }

    try {
        await axios.post('login', {
            username: username.value, password: password.value, remember: rememberMe.value
        })
        toast.showToast('Login Berhasil', 'Selamat datang! Anda akan diarahkan ke dashboard.');

        auth.checked.value = false
        router.replace('/')
    } catch (e) {
        if (e instanceof AxiosError)
            toast.showToast('Login Gagal', e.response?.data?.message ?? e.message, "error");
    }
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <div class="laravel-gradient w-16 h-16 rounded-full flex items-center justify-center shadow-lg">
                        <FontAwesomeIcon :icon="faFire" class="text-white text-2xl" />
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">RS <span class="text-orange-600">Jangbe</span></h1>
                <p class="text-gray-600 mt-2">Silakan login untuk melanjutkan</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 sm:p-8">
                    <form @submit="handleSubmit" class="space-y-6">
                        <!-- Email Input -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                <FontAwesomeIcon :icon="faIdCard" class="mr-2 text-orange-500" />Username
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <FontAwesomeIcon :icon="faUser" class="text-gray-400" />
                                </div>
                                <input type="text" id="username" v-model="username" required
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200"
                                    placeholder="nama@contoh.com">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Gunakan username terdaftar Anda</p>
                        </div>

                        <!-- Password Input -->
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    <FontAwesomeIcon :icon="faKey" class="mr-2 text-orange-500" />Kata Sandi
                                </label>
                                <!-- <a href="#" class="text-sm text-orange-600 hover:text-orange-800 hover:underline">Lupa
                                    kata sandi?</a> -->
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <FontAwesomeIcon :icon="faLock" class="text-gray-400" />
                                </div>
                                <input :type="passwordType" id="password" v-model="password" required
                                    class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200"
                                    placeholder="Masukkan kata sandi">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button type="button" @click="togglePasswordVisibility"
                                        class="text-gray-400 hover:text-gray-600 focus:outline-none"
                                        :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'">
                                        <FontAwesomeIcon :icon="passwordIcon" />
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter</p>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" v-model="rememberMe"
                                class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Ingat saya
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-200 flex items-center justify-center">
                            <FontAwesomeIcon :icon="faSignInAlt" class="mr-2" /> Masuk
                        </button>
                    </form>
                </div>

                <!-- Footer -->
                <!-- <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-600">
                        Belum punya akun?
                        <a href="#"
                            class="text-orange-600 font-medium hover:text-orange-800 hover:underline ml-1">Daftar
                            disini</a>
                    </p>
                </div> -->
            </div>

            <!-- Copyright -->
            <div class="mt-8 text-center">
                <p class="text-gray-500 text-sm">
                    &copy; {{ new Date().getFullYear() }} Devi Mulyana.
                </p>
                <p class="text-gray-400 text-xs mt-1">
                    Dibuat dengan
                    <FontAwesomeIcon :icon="faHeart" class="text-red-500 mx-1" /> menggunakan Tailwind CSS
                </p>
            </div>
        </div>

        
    </div>
</template>
