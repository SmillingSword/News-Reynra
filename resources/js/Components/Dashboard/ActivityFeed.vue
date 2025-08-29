<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="p-2 bg-blue-500 rounded-lg">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Live updates from your CMS</p>
          </div>
        </div>
        
        <!-- Live Indicator -->
        <div class="flex items-center space-x-2">
          <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
          <span class="text-xs font-medium text-green-600 dark:text-green-400">LIVE</span>
        </div>
      </div>
    </div>

    <!-- Activity List -->
    <div class="max-h-96 overflow-y-auto">
      <div v-if="loading" class="p-6 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Loading activities...</p>
      </div>

      <div v-else-if="activities.length === 0" class="p-6 text-center">
        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-4.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7"/>
        </svg>
        <p class="text-gray-500 dark:text-gray-400">No recent activities</p>
      </div>

      <div v-else class="divide-y divide-gray-100 dark:divide-gray-700">
        <TransitionGroup
          name="activity"
          tag="div"
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="opacity-0 transform translate-x-4"
          enter-to-class="opacity-100 transform translate-x-0"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 transform translate-x-0"
          leave-to-class="opacity-0 transform -translate-x-4"
        >
          <div
            v-for="activity in activities"
            :key="activity.id"
            class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
          >
            <div class="flex items-start space-x-3">
              <!-- Activity Icon -->
              <div 
                class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                :class="getActivityIconClass(activity.type)"
              >
                <component 
                  :is="getActivityIcon(activity.type)" 
                  class="w-4 h-4"
                />
              </div>

              <!-- Activity Content -->
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ activity.user?.name || 'System' }}
                  </p>
                  <div class="flex items-center space-x-2">
                    <span 
                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                      :class="getActivityBadgeClass(activity.type)"
                    >
                      {{ getActivityTypeLabel(activity.type) }}
                    </span>
                    <time class="text-xs text-gray-500 dark:text-gray-400">
                      {{ formatTime(activity.created_at) }}
                    </time>
                  </div>
                </div>
                
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                  {{ activity.description }}
                </p>

                <!-- Activity Meta -->
                <div v-if="activity.meta" class="mt-2 flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                  <span v-if="activity.meta.ip">IP: {{ activity.meta.ip }}</span>
                  <span v-if="activity.meta.user_agent">{{ getBrowserInfo(activity.meta.user_agent) }}</span>
                </div>
              </div>
            </div>
          </div>
        </TransitionGroup>
      </div>
    </div>

    <!-- Footer -->
    <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
      <button 
        @click="loadMore"
        :disabled="loadingMore"
        class="w-full px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors duration-200 disabled:opacity-50"
      >
        <span v-if="loadingMore">Loading more...</span>
        <span v-else>Load more activities</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import {
  PlusIcon,
  PencilIcon,
  TrashIcon,
  EyeIcon,
  UserIcon,
  Cog6ToothIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  initialActivities: {
    type: Array,
    default: () => []
  },
  autoRefresh: {
    type: Boolean,
    default: true
  },
  refreshInterval: {
    type: Number,
    default: 30000 // 30 seconds
  }
})

const activities = ref(props.initialActivities)
const loading = ref(false)
const loadingMore = ref(false)
let refreshTimer = null

const activityIcons = {
  'article.created': PlusIcon,
  'article.updated': PencilIcon,
  'article.deleted': TrashIcon,
  'article.viewed': EyeIcon,
  'user.login': UserIcon,
  'user.logout': UserIcon,
  'system.backup': Cog6ToothIcon,
  'system.error': ExclamationTriangleIcon
}

const getActivityIcon = (type) => {
  return activityIcons[type] || PlusIcon
}

const getActivityIconClass = (type) => {
  const classes = {
    'article.created': 'bg-green-100 text-green-600 dark:bg-green-900/20 dark:text-green-400',
    'article.updated': 'bg-blue-100 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400',
    'article.deleted': 'bg-red-100 text-red-600 dark:bg-red-900/20 dark:text-red-400',
    'article.viewed': 'bg-purple-100 text-purple-600 dark:bg-purple-900/20 dark:text-purple-400',
    'user.login': 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400',
    'user.logout': 'bg-gray-100 text-gray-600 dark:bg-gray-900/20 dark:text-gray-400',
    'system.backup': 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/20 dark:text-yellow-400',
    'system.error': 'bg-red-100 text-red-600 dark:bg-red-900/20 dark:text-red-400'
  }
  return classes[type] || 'bg-gray-100 text-gray-600 dark:bg-gray-900/20 dark:text-gray-400'
}

const getActivityBadgeClass = (type) => {
  const classes = {
    'article.created': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    'article.updated': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    'article.deleted': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    'article.viewed': 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
    'user.login': 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-400',
    'user.logout': 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
    'system.backup': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    'system.error': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
  }
  return classes[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
}

const getActivityTypeLabel = (type) => {
  const labels = {
    'article.created': 'Created',
    'article.updated': 'Updated',
    'article.deleted': 'Deleted',
    'article.viewed': 'Viewed',
    'user.login': 'Login',
    'user.logout': 'Logout',
    'system.backup': 'Backup',
    'system.error': 'Error'
  }
  return labels[type] || 'Activity'
}

const formatTime = (timestamp) => {
  const date = new Date(timestamp)
  const now = new Date()
  const diff = now - date
  
  if (diff < 60000) return 'Just now'
  if (diff < 3600000) return `${Math.floor(diff / 60000)}m ago`
  if (diff < 86400000) return `${Math.floor(diff / 3600000)}h ago`
  return date.toLocaleDateString()
}

const getBrowserInfo = (userAgent) => {
  if (!userAgent) return 'Unknown'
  
  if (userAgent.includes('Chrome')) return 'Chrome'
  if (userAgent.includes('Firefox')) return 'Firefox'
  if (userAgent.includes('Safari')) return 'Safari'
  if (userAgent.includes('Edge')) return 'Edge'
  return 'Unknown Browser'
}

const loadMore = async () => {
  loadingMore.value = true
  
  // Simulate API call
  setTimeout(() => {
    // Add some mock activities
    const mockActivities = [
      {
        id: Date.now() + Math.random(),
        type: 'article.viewed',
        description: 'Article "Sample Post" was viewed by a visitor',
        user: { name: 'Anonymous' },
        created_at: new Date().toISOString(),
        meta: { ip: '192.168.1.1' }
      }
    ]
    
    activities.value.push(...mockActivities)
    loadingMore.value = false
  }, 1000)
}

const refreshActivities = async () => {
  // Simulate fetching new activities
  const mockActivity = {
    id: Date.now() + Math.random(),
    type: ['article.created', 'article.updated', 'user.login'][Math.floor(Math.random() * 3)],
    description: 'New activity detected',
    user: { name: 'John Doe' },
    created_at: new Date().toISOString(),
    meta: { ip: '192.168.1.100' }
  }
  
  activities.value.unshift(mockActivity)
  
  // Keep only last 20 activities
  if (activities.value.length > 20) {
    activities.value = activities.value.slice(0, 20)
  }
}

onMounted(() => {
  if (props.autoRefresh) {
    refreshTimer = setInterval(refreshActivities, props.refreshInterval)
  }
})

onUnmounted(() => {
  if (refreshTimer) {
    clearInterval(refreshTimer)
  }
})
</script>

<style scoped>
.activity-enter-active,
.activity-leave-active {
  transition: all 0.3s ease;
}

.activity-enter-from {
  opacity: 0;
  transform: translateX(30px);
}

.activity-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}
</style>
