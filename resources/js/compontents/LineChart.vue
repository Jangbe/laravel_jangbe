<script setup lang="ts">
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Tooltip,
    Legend,
} from 'chart.js'
import { Line } from 'vue-chartjs'

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Tooltip,
    Legend
)

defineProps<{
    labels: string[]
    data: number[]
}>()

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
}
</script>

<template>
    <div class="h-64 mb-3">
        <Line :data="{
            labels,
            datasets: [
                {
                    label: 'Pasien',
                    data: data,
                    borderColor: '#f97316',
                    tension: 0.4,
                }
            ],
        }" :options="chartOptions" />
    </div>
</template>
