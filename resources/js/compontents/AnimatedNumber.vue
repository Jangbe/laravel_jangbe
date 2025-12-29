<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps<{
    value: number
    duration?: number
    decimals?: number
}>()

const animated = ref(props.value)

watch(
    () => props.value,
    (newVal, oldVal) => {
        const start = performance.now()
        const from = oldVal ?? 0
        const duration = props.duration ?? 300

        function animate(time: number) {
            const progress = Math.min((time - start) / duration, 1)
            animated.value = from + (newVal - from) * progress

            if (progress < 1) {
                requestAnimationFrame(animate)
            }
        }

        requestAnimationFrame(animate)
    }
)
</script>

<template>
    <span>
        {{ animated.toFixed(decimals ?? 0) }}
    </span>
</template>
