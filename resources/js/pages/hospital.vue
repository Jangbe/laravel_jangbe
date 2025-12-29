<script lang="ts" setup>
import { useAuth } from '@/composables/auth';
import { useRouter } from 'vue-router';

const router = useRouter()
const auth = useAuth()

const logout = async () => {
    await auth.logout()
    router.replace('/login')
}
</script>

<template>
    <div class="bg-amber-900">
        ini halaman master rumah sakit

        <button class="px-2 py-1 bg-red-500" @click="logout">Logout</button>
    </div>
</template>