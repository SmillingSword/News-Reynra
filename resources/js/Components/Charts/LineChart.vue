<template>
  <div class="relative">
    <canvas ref="chartCanvas" :height="height"></canvas>
    <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-lg">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js'

// Register Chart.js components
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
)

const props = defineProps({
  data: {
    type: Object,
    required: true
  },
  height: {
    type: Number,
    default: 200
  },
  loading: {
    type: Boolean,
    default: false
  },
  animated: {
    type: Boolean,
    default: true
  }
})

const chartCanvas = ref(null)
let chartInstance = null

const createChart = () => {
  if (!chartCanvas.value) return

  const ctx = chartCanvas.value.getContext('2d')
  
  // Destroy existing chart
  if (chartInstance) {
    chartInstance.destroy()
  }

  const isDark = document.documentElement.classList.contains('dark')
  
  chartInstance = new ChartJS(ctx, {
    type: 'line',
    data: props.data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: props.animated ? {
        duration: 2000,
        easing: 'easeInOutQuart'
      } : false,
      interaction: {
        intersect: false,
        mode: 'index'
      },
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: isDark ? 'rgba(31, 41, 55, 0.9)' : 'rgba(255, 255, 255, 0.9)',
          titleColor: isDark ? '#F9FAFB' : '#111827',
          bodyColor: isDark ? '#D1D5DB' : '#374151',
          borderColor: isDark ? '#4B5563' : '#E5E7EB',
          borderWidth: 1,
          cornerRadius: 8,
          padding: 12,
          displayColors: false,
          callbacks: {
            title: function(context) {
              return `${context[0].label}`
            },
            label: function(context) {
              return `${context.dataset.label}: ${context.parsed.y}`
            }
          }
        }
      },
      scales: {
        x: {
          grid: {
            display: false
          },
          border: {
            display: false
          },
          ticks: {
            color: isDark ? '#9CA3AF' : '#6B7280',
            font: {
              size: 12
            }
          }
        },
        y: {
          grid: {
            color: isDark ? 'rgba(75, 85, 99, 0.3)' : 'rgba(229, 231, 235, 0.8)',
            drawBorder: false
          },
          border: {
            display: false
          },
          ticks: {
            color: isDark ? '#9CA3AF' : '#6B7280',
            font: {
              size: 12
            },
            padding: 8
          }
        }
      },
      elements: {
        point: {
          radius: 4,
          hoverRadius: 6,
          backgroundColor: '#3B82F6',
          borderColor: '#FFFFFF',
          borderWidth: 2,
          hoverBorderWidth: 3
        },
        line: {
          tension: 0.4,
          borderWidth: 3,
          borderCapStyle: 'round',
          borderJoinStyle: 'round'
        }
      }
    }
  })
}

// Watch for data changes
watch(() => props.data, () => {
  nextTick(() => {
    createChart()
  })
}, { deep: true })

// Watch for theme changes
const observeThemeChanges = () => {
  const observer = new MutationObserver(() => {
    nextTick(() => {
      createChart()
    })
  })
  
  observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class']
  })
  
  return observer
}

let themeObserver = null

onMounted(() => {
  nextTick(() => {
    createChart()
    themeObserver = observeThemeChanges()
  })
})

onUnmounted(() => {
  if (chartInstance) {
    chartInstance.destroy()
  }
  if (themeObserver) {
    themeObserver.disconnect()
  }
})
</script>
