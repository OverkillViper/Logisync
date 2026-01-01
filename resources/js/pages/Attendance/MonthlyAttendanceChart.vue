<template>
    <div class="w-full">
        <canvas ref="canvas"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import {
    Chart,
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    Tooltip,
    Legend
} from 'chart.js'
import annotationPlugin from 'chartjs-plugin-annotation';

Chart.register(
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    Tooltip,
    Legend,
    annotationPlugin
)

const props = defineProps({
    labels: Array,    // ['Jan', 'Feb', ...]
    punchIn: Array,   // [8.5, 8.7, ...]
    punchOut: Array
})

const canvas = ref(null)
let chart

function formatHours(value) {
    if (value === null) return '—'
    const h = Math.floor(value)
    const m = Math.round((value - h) * 60)
    const ampm = h >= 12 ? 'PM' : 'AM'
    const h12 = h % 12 || 12
    return `${h12}:${String(m).padStart(2, '0')} ${ampm}`
}

function render() {
    chart = new Chart(canvas.value, {
        type: 'line',
        data: {
            labels: props.labels,
            datasets: [
                {
                    label: 'Avg Punch In',
                    data: props.punchIn,
                    borderColor: '#1f2937',
                    tension: 0.35,
                    spanGaps: true
                },
                {
                    label: 'Avg Punch Out',
                    data: props.punchOut,
                    borderColor: '#6b7280',
                    tension: 0.35,
                    spanGaps: true
                }
            ]
        },
        options: {
            animation: false,
            scales: {
                y: {
                    min: 7,
                    max: 22,
                    ticks: {
                        callback: formatHours
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: ctx =>
                            `${ctx.dataset.label}: ${formatHours(ctx.raw)}`
                    }
                },
                annotation: {
                    annotations: {
                        punchInLine: {
                            type: 'line',
                            yMin: 8,
                            yMax: 8,
                            borderColor: 'blue',
                            borderWidth: 1,
                            borderDash: [5, 5],
                            label: {
                            enabled: true,
                            content: '8:30 AM',
                            position: 'start',
                            color: 'red',
                            font: { size: 10 }
                            }
                        },
                        punchOutLine: {
                            type: 'line',
                            yMin: 17,
                            yMax: 17,
                            borderColor: 'blue',
                            borderWidth: 1,
                            borderDash: [5, 5],
                            label: {
                            enabled: true,
                            content: '5:00 PM',
                            position: 'start',
                            color: 'green',
                            font: { size: 10 }
                            }
                        }
                    }
                }
            }
        }
    })
}

onMounted(render)

watch(() => [props.labels, props.punchIn, props.punchOut], () => {
    chart.destroy()
    render()
})
</script>
