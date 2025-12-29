<script setup lang="ts">
import AnimatedNumber from '@/compontents/AnimatedNumber.vue';
import LineChart from '@/compontents/LineChart.vue';
import axios from '@axios';
import { faHospital, faUserInjured } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { ref } from 'vue';

const stats = ref([
    { name: 'Total Rumah Sakit', value: 0, icon: faHospital, color: 'text-blue-500', bg: 'bg-blue-50' },
    { name: 'Total Pasien', value: 0, icon: faUserInjured, color: 'text-orange-500', bg: 'bg-orange-50' },
])

const chart_labels = ref([])
const chart_data = ref([])

axios.get('stat').then(({ data }) => {
    stats.value[0]!.value = data.hospitals
    stats.value[1]!.value = data.patients

    chart_labels.value = data.stats.labels
    chart_data.value = data.stats.data
})
</script>

<template>
    <div class="space-y-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Ringkasan Data</h1>
            <p class="text-gray-500">Selamat datang kembali, Admin. Berikut statistik sistem hari ini.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
            <div v-for="stat in stats" :key="stat.name"
                class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5">
                <div :class="[stat.bg, stat.color]"
                    class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl">
                    <font-awesome-icon :icon="stat.icon" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">{{ stat.name }}</p>
                    <p class="text-2xl font-bold text-gray-800">
                        <animated-number :value="stat.value" />
                    </p>
                </div>
            </div>
        </div>

        <div
            class="bg-white p-8 rounded-3xl shadow-sm border border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-400 min-h-75">
            <h1 class="text-bold text-center text-2xl">Statistik Bulanan</h1>
            <line-chart :labels="chart_labels" :data="chart_data" class="w-full" />
        </div>
    </div>
</template>