<template>
  <span>{{ displayValue }}</span>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'

const props = defineProps({
  value: {
    type: [Number, String],
    required: true
  },
  duration: {
    type: Number,
    default: 1000
  },
  format: {
    type: String,
    default: 'number' // 'number', 'currency', 'percentage'
  },
  decimals: {
    type: Number,
    default: 0
  }
})

const displayValue = ref('0')
let animationId = null

const formatNumber = (num) => {
  const numValue = typeof num === 'string' ? parseFloat(num) || 0 : num

  switch (props.format) {
    case 'currency':
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: props.decimals
      }).format(numValue)
    
    case 'percentage':
      return `${numValue.toFixed(props.decimals)}%`
    
    default:
      return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: props.decimals,
        maximumFractionDigits: props.decimals
      }).format(numValue)
  }
}

const animateValue = (start, end, duration) => {
  if (animationId) {
    cancelAnimationFrame(animationId)
  }

  const startTime = performance.now()
  const startValue = typeof start === 'string' ? parseFloat(start) || 0 : start
  const endValue = typeof end === 'string' ? parseFloat(end) || 0 : end
  const difference = endValue - startValue

  const animate = (currentTime) => {
    const elapsed = currentTime - startTime
    const progress = Math.min(elapsed / duration, 1)
    
    // Easing function (ease-out)
    const easeOut = 1 - Math.pow(1 - progress, 3)
    
    const currentValue = startValue + (difference * easeOut)
    displayValue.value = formatNumber(currentValue)
    
    if (progress < 1) {
      animationId = requestAnimationFrame(animate)
    } else {
      displayValue.value = formatNumber(endValue)
    }
  }
  
  animationId = requestAnimationFrame(animate)
}

watch(() => props.value, (newValue, oldValue) => {
  const oldNum = typeof oldValue === 'string' ? parseFloat(oldValue) || 0 : oldValue || 0
  animateValue(oldNum, newValue, props.duration)
}, { immediate: false })

onMounted(() => {
  // Initial animation from 0
  animateValue(0, props.value, props.duration)
})
</script>
