<script setup lang="ts">
import { faExclamationTriangle, faArrowLeft } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-6">
        <div class="text-center">
            <div
                class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-orange-100 text-orange-500 text-4xl mb-8">
                <font-awesome-icon :icon="faExclamationTriangle" />
            </div>
            <h1 class="text-6xl font-black text-gray-900 mb-4">404</h1>
            <p class="text-xl text-gray-600 mb-8 font-medium">Waduh! Halaman yang kamu cari tidak ditemukan.</p>

            <router-link to="/"
                class="inline-flex items-center gap-2 laravel-gradient hover:bg-orange-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-orange-500/30 transition-all active:scale-95">
                <font-awesome-icon :icon="faArrowLeft" />
                Kembali ke Dashboard
            </router-link>
        </div>
    </div>
</template>