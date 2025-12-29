<script lang="ts" setup>
    
import {
    faInfoCircle,
    faTimes
} from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useToast } from './composables/toast';

const {
    hideToast,
    toastMessage,
    toastTitle,
    toastType,
    toastVisible
} = useToast()

</script>

<template>
    <router-view />

    <!-- Toast Notification -->
    <transition enter-active-class="transition duration-300 ease-out transform"
        enter-from-class="translate-x-full opacity-0" enter-to-class="translate-x-0 opacity-100"
        leave-active-class="transition duration-300 ease-in transform" leave-from-class="translate-x-0 opacity-100"
        leave-to-class="translate-x-full opacity-0">
        <div v-if="toastVisible" class="fixed top-5 right-5 bg-white border-l-4 shadow-lg rounded-lg p-4 max-w-sm z-50"
            :class="toastType === 'error' ? 'border-red-500' : 'border-orange-500'">
            <div class="flex items-start">
                <div class="shrink-0">
                    <FontAwesomeIcon :icon="faInfoCircle" class="text-xl"
                        :class="toastType === 'error' ? 'text-red-500' : 'text-orange-500'" />
                </div>
                <div class="ml-3">
                    <p class="text-gray-800 font-medium">{{ toastTitle }}</p>
                    <p class="text-gray-600 text-sm mt-1">{{ toastMessage }}</p>
                </div>
                <button @click="hideToast" class="ml-4 text-gray-400 hover:text-gray-600 focus:outline-none"
                    aria-label="Tutup notifikasi">
                    <FontAwesomeIcon :icon="faTimes" />
                </button>
            </div>
        </div>
    </transition>
</template>