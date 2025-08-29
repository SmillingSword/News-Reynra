<template>
  <div class="relative">
    <canvas ref="chartCanvas" :height="height"></canvas>
    <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-lg">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500"></div>
    </div>
    
    <!-- Center Text -->
    <div v-if="centerText" class="absolute inset-0 flex items-center justify-center pointer-events-none">
      <div class="text-center">
        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ centerText.value }}</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">{{ centerText.label }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue'
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend
} from 'chart.js'

// Register Chart.js components
ChartJS.register(ArcElement, Tooltip, Legend)

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
  },
  centerText: {
    type: Object,
    default: null
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
    type: 'doughnut',
    data: props.data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '70%',
      animation: props.animated ? {
        animateRotate: true,
        animateScale: true,
        duration: 2000,
        easing: 'easeInOutQuart'
      } : false,
      interaction: {
        intersect: false
      },
      plugins: {
        legend: {
          display: true,
          position: 'bottom',
          labels: {
            color: isDark ? '#D1D5DB' : '#374151',
            font: {
              size: 12
            },
            padding: 15,
            usePointStyle: true,
            pointStyle: 'circle'
          }
        },
        tooltip: {
          backgroundColor: isDark ? 'rgba(31, 41, 55, 0.9)' : 'rgba(255, 255, 255, 0.9)',
          titleColor: isDark ? '#F9FAFB' : '#111827',
          bodyColor: isDark ? '#D1D5DB' : '#374151',
          borderColor: isDark ? '#4B5563' : '#E5E7EB',
          borderWidth: 1,
          cornerRadius: 8,
          padding: 12,
          displayColors: true,
          callbacks: {
            label: function(context) {
              const label = context.label || ''
              const value = context.parsed
              const total = context.dataset.data.reduce((a, b) => a + b, 0)
              const percentage = ((value / total) * 100).toFixed(1)
              return `${label}: ${value} (${percentage}%)`
            }
          }
        }
      },
      elements: {
        arc: {
          borderWidth: 2,
          borderColor: isDark ? '#1F2937' : '#FFFFFF',
          hoverBorderWidth: 3
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
