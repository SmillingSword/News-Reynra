<template>
  <div 
    class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group"
    :class="[
      'border border-gray-200 dark:border-gray-700',
      gradient && 'bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900'
    ]"
  >
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
      <div class="absolute inset-0" :style="{ backgroundImage: `url('data:image/svg+xml,${encodeURIComponent(backgroundPattern)}')` }"></div>
    </div>

    <!-- Gradient Overlay -->
    <div 
      v-if="color" 
      class="absolute top-0 right-0 w-32 h-32 rounded-full blur-3xl opacity-10 group-hover:opacity-20 transition-opacity duration-300"
      :class="`bg-${color}-500`"
    ></div>

    <div class="relative p-6">
      <!-- Header -->
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-3">
          <div 
            class="p-3 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300"
            :class="[
              color ? `bg-gradient-to-br from-${color}-500 to-${color}-600` : 'bg-gradient-to-br from-blue-500 to-blue-600'
            ]"
          >
            <component 
              :is="iconComponent" 
              class="w-6 h-6 text-white"
            />
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ title }}</h3>
            <p v-if="subtitle" class="text-xs text-gray-500 dark:text-gray-500">{{ subtitle }}</p>
          </div>
        </div>
        
        <!-- Trend Indicator -->
        <div v-if="trend" class="flex items-center space-x-1">
          <component 
            :is="trendIcon" 
            class="w-4 h-4"
            :class="trendColor"
          />
          <span class="text-sm font-medium" :class="trendColor">
            {{ Math.abs(trend) }}%
          </span>
        </div>
      </div>

      <!-- Main Value -->
      <div class="mb-4">
        <div class="flex items-baseline space-x-2">
          <span 
            class="text-3xl font-bold text-gray-900 dark:text-white transition-all duration-300"
            :class="{ 'animate-pulse': loading }"
          >
            <AnimatedNumber :value="value" :duration="1000" />
          </span>
          <span v-if="unit" class="text-lg text-gray-500 dark:text-gray-400">{{ unit }}</span>
        </div>
        <p v-if="description" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
          {{ description }}
        </p>
      </div>

      <!-- Progress Bar -->
      <div v-if="progress !== undefined" class="mb-4">
        <div class="flex justify-between items-center mb-2">
          <span class="text-xs text-gray-500 dark:text-gray-400">Progress</span>
          <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ progress }}%</span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
          <div 
            class="h-2 rounded-full transition-all duration-1000 ease-out"
            :class="color ? `bg-gradient-to-r from-${color}-500 to-${color}-600` : 'bg-gradient-to-r from-blue-500 to-blue-600'"
            :style="{ width: `${Math.min(progress, 100)}%` }"
          ></div>
        </div>
      </div>

      <!-- Footer Stats -->
      <div v-if="footerStats && footerStats.length" class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-gray-700">
        <div 
          v-for="(stat, index) in footerStats" 
          :key="index"
          class="text-center"
        >
          <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ stat.value }}</div>
          <div class="text-xs text-gray-500 dark:text-gray-400">{{ stat.label }}</div>
        </div>
      </div>

      <!-- Action Button -->
      <div v-if="actionText" class="mt-4">
        <button 
          @click="$emit('action')"
          class="w-full px-4 py-2 text-sm font-medium text-center rounded-lg transition-all duration-200 hover:scale-105"
          :class="[
            color 
              ? `text-${color}-600 bg-${color}-50 hover:bg-${color}-100 dark:text-${color}-400 dark:bg-${color}-900/20 dark:hover:bg-${color}-900/30`
              : 'text-blue-600 bg-blue-50 hover:bg-blue-100 dark:text-blue-400 dark:bg-blue-900/20 dark:hover:bg-blue-900/30'
          ]"
        >
          {{ actionText }}
        </button>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div 
      v-if="loading" 
      class="absolute inset-0 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm flex items-center justify-center"
    >
      <div class="animate-spin rounded-full h-8 w-8 border-b-2" :class="color ? `border-${color}-500` : 'border-blue-500'"></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import AnimatedNumber from './AnimatedNumber.vue'

// Icons
import {
  ChartBarIcon,
  UsersIcon,
  DocumentTextIcon,
  ChatBubbleLeftRightIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  EyeIcon,
  HeartIcon,
  ShareIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: String,
  value: {
    type: [Number, String],
    required: true
  },
  unit: String,
  description: String,
  icon: {
    type: String,
    default: 'chart-bar'
  },
  color: {
    type: String,
    default: 'blue'
  },
  trend: Number,
  progress: Number,
  footerStats: Array,
  actionText: String,
  loading: {
    type: Boolean,
    default: false
  },
  gradient: {
    type: Boolean,
    default: true
  }
})

defineEmits(['action'])

const iconMap = {
  'chart-bar': ChartBarIcon,
  'users': UsersIcon,
  'document-text': DocumentTextIcon,
  'chat': ChatBubbleLeftRightIcon,
  'eye': EyeIcon,
  'heart': HeartIcon,
  'share': ShareIcon
}

const iconComponent = computed(() => iconMap[props.icon] || ChartBarIcon)

const trendIcon = computed(() => {
  return props.trend >= 0 ? ArrowTrendingUpIcon : ArrowTrendingDownIcon
})

const trendColor = computed(() => {
  return props.trend >= 0 
    ? 'text-green-600 dark:text-green-400' 
    : 'text-red-600 dark:text-red-400'
})

const backgroundPattern = computed(() => {
  return `<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23000000" fill-opacity="0.1"><circle cx="30" cy="30" r="2"/></g></svg>`
})
</script>
