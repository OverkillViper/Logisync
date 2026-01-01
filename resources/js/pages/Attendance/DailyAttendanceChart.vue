<template>
    <canvas ref="chartCanvas"></canvas>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import {
    Chart,
    LineController,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Title,
    Tooltip,
    Legend
} from 'chart.js';
import annotationPlugin from 'chartjs-plugin-annotation';

Chart.register(
    LineController,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Title,
    Tooltip,
    Legend,
    annotationPlugin
);

const props = defineProps({
    labels: Array,
    punchIn: Array,
    punchOut: Array
});

const chartCanvas = ref(null);
let chartInstance = null;

// Convert "08:30 AM" / "05:12 PM" to decimal hours
function toHours(time) {
    if (!time) return null;
    const [clock, modifier] = time.split(' ');
    let [hours, minutes] = clock.split(':').map(Number);
    if (modifier === 'PM' && hours !== 12) hours += 12;
    if (modifier === 'AM' && hours === 12) hours = 0;
    return hours + minutes / 60;
}

// Convert decimal hours back to 12-hour time string for labels
function formatHours(hoursDecimal) {
    if (hoursDecimal === null) return '';
    const hours = Math.floor(hoursDecimal);
    const minutes = Math.round((hoursDecimal - hours) * 60);
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const h12 = hours % 12 === 0 ? 12 : hours % 12;
    const mStr = minutes.toString().padStart(2, '0');
    return `${h12}:${mStr} ${ampm}`;
}

const chartData = computed(() => ({
    labels: props.labels,
    datasets: [
        {
            label: 'Punch In',
            data: props.punchIn.map(toHours),
            borderColor: '#252525',
            backgroundColor: '#252525',
            tension: 0.3,
            fill: false,
            pointRadius: 4,
            spanGaps: true
        },
        {
            label: 'Punch Out',
            data: props.punchOut.map(toHours),
            borderColor: '#888888',
            backgroundColor: '#888888',
            tension: 0.3,
            fill: false,
            pointRadius: 4,
            spanGaps: true
        }
    ]
}));

const chartOptions = {
    responsive: true,
    animation: false,
    scales: {
        y: {
            min: Math.min(7, Math.min(...props.punchIn.map(toHours).filter(v => v !== null)) - 1 || 0),
            max: Math.max(23, Math.max(...props.punchOut.map(toHours).filter(v => v !== null)) + 1 || 24),
            ticks: {
                stepSize: 1,
                callback: function(value) {
                    return formatHours(value);
                }
            },
            grid: { display: true } // remove y grid lines
        },
        x: {
            grid: { display: false },
            ticks: {
                callback: function(value, index, ticks) {
                    // props.labels contains full dates like "14 October 2025"
                    const dateStr = this.getLabelForValue(value); 
                    const date = new Date(dateStr);
                    return date.getDate(); // show only day number
                }
            }
        } // remove x grid lines
    },
    plugins: {
        legend:  { display: true },
        tooltip: {
            callbacks: {
                label: function(context) {
                    return `${context.dataset.label}: ${formatHours(context.raw)}`;
                }
            }
        },
        annotation: {
            annotations: {
                punchInLine: {
                    type: 'line',
                    yMin: 8.5,
                    yMax: 8.5,
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
};

onMounted(() => {
    chartInstance = new Chart(chartCanvas.value, {
    type: 'line',
    data: chartData.value,
    options: chartOptions
    });
});

// Watch for data changes
watch([chartData], () => {
    if (chartInstance) {
    chartInstance.data = chartData.value;
    chartInstance.update();
    }
});
</script>
