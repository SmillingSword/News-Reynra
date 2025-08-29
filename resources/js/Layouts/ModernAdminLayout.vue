<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors">
    <!-- Sidebar -->
    <div 
      :class="[
        'fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 shadow-lg transform transition-transform duration-300 ease-in-out',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      ]"
    >
      <!-- Logo -->
      <div class="flex items-center justify-center h-16 px-4 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
          <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-white/20 to-transparent"></div>
          <div class="absolute -top-4 -right-4 w-8 h-8 bg-white/10 rounded-full"></div>
          <div class="absolute -bottom-2 -left-2 w-6 h-6 bg-white/10 rounded-full"></div>
        </div>
        
        <Link :href="route('admin.dashboard')" class="flex items-center space-x-3 relative z-10 group">
          <!-- Enhanced Logo Icon -->
          <div class="relative">
            <div class="w-10 h-10 bg-gradient-to-br from-white to-gray-100 rounded-xl shadow-lg flex items-center justify-center transform group-hover:scale-110 transition-all duration-300 group-hover:rotate-3">
              <!-- NR Logo -->
              <div class="font-black text-lg tracking-tighter leading-none">
                <span class="text-blue-600">N</span><span class="text-purple-600">R</span>
              </div>
              <!-- Shine Effect -->
              <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
            </div>
            <!-- Glow Effect -->
            <div class="absolute inset-0 w-10 h-10 bg-white/20 rounded-xl blur-md group-hover:bg-white/30 transition-all duration-300"></div>
          </div>
          
          <!-- Enhanced Text Logo -->
          <div class="flex flex-col">
            <span class="text-xl font-bold text-white tracking-tight group-hover:text-gray-100 transition-colors duration-300">
              News
              <span class="bg-gradient-to-r from-yellow-300 to-orange-300 bg-clip-text text-transparent font-extrabold">
                Reynra
              </span>
            </span>
            <span class="text-xs text-white/70 font-medium tracking-wider uppercase">
              Admin Panel
            </span>
          </div>
          
          <!-- Animated Dots -->
          <div class="flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <div class="w-1 h-1 bg-white/60 rounded-full animate-pulse"></div>
            <div class="w-1 h-1 bg-white/60 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
            <div class="w-1 h-1 bg-white/60 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
          </div>
        </Link>
      </div>

      <!-- Navigation -->
      <nav class="mt-8 px-4">
        <div class="space-y-2">
          <!-- Dashboard -->
          <SidebarLink 
            :href="route('admin.dashboard')" 
            :active="route().current('admin.dashboard')"
            icon="home"
          >
            Dashboard
          </SidebarLink>

          <!-- Articles Section -->
          <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Content</h3>
            <div class="mt-2 space-y-1">
              <SidebarLink 
                :href="route('admin.articles.index')" 
                :active="route().current('admin.articles.*')"
                icon="document-text"
                :badge="articleStats?.total"
              >
                Articles
              </SidebarLink>
              
              <SidebarSubLink 
                :href="route('admin.articles.create')" 
                :active="route().current('admin.articles.create')"
              >
                Add New Article
              </SidebarSubLink>
              
              <SidebarLink 
                :href="route('admin.categories.index')" 
                :active="route().current('admin.categories.*')"
                icon="tag"
                :badge="categoryStats?.total"
              >
                Categories
              </SidebarLink>

              <SidebarLink 
                :href="route('admin.games.index')" 
                :active="route().current('admin.games.*')"
                icon="game-controller"
                :badge="gameStats?.total"
              >
                Games
              </SidebarLink>

              <SidebarLink 
                :href="route('admin.tags.index')" 
                :active="route().current('admin.tags.*')"
                icon="hashtag"
              >
                Tags
              </SidebarLink>
            </div>
          </div>

          <!-- Users Section -->
          <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Users</h3>
            <div class="mt-2 space-y-1">
              <SidebarLink 
                :href="route('admin.users.index')" 
                :active="route().current('admin.users.*')"
                icon="users"
                :badge="userStats?.total"
              >
                All Users
              </SidebarLink>
              
              <SidebarSubLink 
                :href="route('admin.users.create')" 
                :active="route().current('admin.users.create')"
              >
                Add New User
              </SidebarSubLink>
            </div>
          </div>

          <!-- Comments Section -->
          <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Engagement</h3>
            <div class="mt-2 space-y-1">
              <SidebarLink 
                :href="route('admin.comments.index')" 
                :active="route().current('admin.comments.*')"
                icon="chat-alt"
              >
                Comments
              </SidebarLink>
            </div>
          </div>

          <!-- Media Section -->
          <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Media</h3>
            <div class="mt-2 space-y-1">
              <SidebarLink 
                :href="route('admin.media.index')" 
                :active="route().current('admin.media.*')"
                icon="photograph"
              >
                Media Library
              </SidebarLink>
            </div>
          </div>

          <!-- Settings Section -->
          <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Settings</h3>
            <div class="mt-2 space-y-1">
              <SidebarLink 
                :href="route('admin.settings.index')" 
                :active="route().current('admin.settings.*')"
                icon="cog"
              >
                General Settings
              </SidebarLink>
              
              <SidebarLink 
                :href="route('admin.roles.index')" 
                :active="route().current('admin.roles.*')"
                icon="shield-check"
              >
                Roles & Permissions
              </SidebarLink>
            </div>
          </div>
        </div>
      </nav>

      <!-- User Profile -->
      <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-3">
          <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
            <span class="text-sm font-medium text-white">
              {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
            </span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
              {{ $page.props.auth.user.name }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
              {{ $page.props.auth.user.email }}
            </p>
          </div>
          <Dropdown align="right" width="48">
            <template #trigger>
              <button class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                </svg>
              </button>
            </template>
            <template #content>
              <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
              <DropdownLink :href="route('dashboard')">User Dashboard</DropdownLink>
              <DropdownLink :href="route('logout')" method="post" as="button">
                Log Out
              </DropdownLink>
            </template>
          </Dropdown>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="lg:pl-64">
      <!-- Top Bar -->
      <div class="sticky top-0 z-40 bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 transition-colors">
        <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
          <!-- Mobile menu button -->
          <button
            @click="sidebarOpen = !sidebarOpen"
            class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>

          <!-- Page Title -->
          <div class="flex-1 min-w-0">
            <h1 v-if="pageTitle" class="text-2xl font-bold text-gray-900 dark:text-white truncate">
              {{ pageTitle }}
            </h1>
            <slot name="header" v-else />
          </div>

          <!-- Top Bar Actions -->
          <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <div class="relative notifications-container">
              <button 
                @click="showNotifications = !showNotifications"
                class="p-2 text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors relative"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM11 19H6a2 2 0 01-2-2V7a2 2 0 012-2h5m5 0v5"/>
                </svg>
                <span v-if="unreadNotifications > 0" class="absolute -top-1 -right-1 block h-5 w-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center font-medium">
                  {{ unreadNotifications > 9 ? '9+' : unreadNotifications }}
                </span>
              </button>

              <!-- Notifications Dropdown -->
              <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
              >
                <div v-if="showNotifications" class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                  <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notifications</h3>
                      <button 
                        @click="markAllAsRead"
                        class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                      >
                        Mark all read
                      </button>
                    </div>
                  </div>
                  <div class="max-h-96 overflow-y-auto">
                    <div v-if="notifications.length === 0" class="p-4 text-center text-gray-500 dark:text-gray-400">
                      No notifications
                    </div>
                    <div v-else>
                      <div 
                        v-for="notification in notifications" 
                        :key="notification.id"
                        class="p-4 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': !notification.read }"
                        @click="markAsRead(notification.id)"
                      >
                        <div class="flex items-start space-x-3">
                          <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                              <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                              </svg>
                            </div>
                          </div>
                          <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ notification.title }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ notification.message }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ formatNotificationTime(notification.created_at) }}</p>
                          </div>
                          <div v-if="!notification.read" class="flex-shrink-0">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </Transition>
            </div>

            <!-- Quick Actions -->
            <div class="hidden sm:flex items-center space-x-2">
              <Link 
                :href="route('admin.articles.create')"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Article
              </Link>
            </div>

            <!-- Theme Toggle -->
            <button 
              @click="toggleTheme"
              class="p-2 text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
              :title="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
            >
              <svg v-if="isDark" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
              </svg>
              <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Page Content -->
      <main class="flex-1">
        <!-- Flash Messages -->
        <Transition
          enter-active-class="transition ease-out duration-300"
          enter-from-class="opacity-0 transform translate-y-2"
          enter-to-class="opacity-100 transform translate-y-0"
          leave-active-class="transition ease-in duration-200"
          leave-from-class="opacity-100 transform translate-y-0"
          leave-to-class="opacity-0 transform translate-y-2"
        >
          <div v-if="$page.props.flash?.success" class="mx-4 sm:mx-6 lg:mx-8 mt-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
              <div class="flex">
                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div class="ml-3">
                  <p class="text-sm font-medium text-green-800">
                    {{ $page.props.flash.success }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </Transition>

        <Transition
          enter-active-class="transition ease-out duration-300"
          enter-from-class="opacity-0 transform translate-y-2"
          enter-to-class="opacity-100 transform translate-y-0"
          leave-active-class="transition ease-in duration-200"
          leave-from-class="opacity-100 transform translate-y-0"
          leave-to-class="opacity-0 transform translate-y-2"
        >
          <div v-if="$page.props.flash?.error" class="mx-4 sm:mx-6 lg:mx-8 mt-4">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
              <div class="flex">
                <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="ml-3">
                  <p class="text-sm font-medium text-red-800">
                    {{ $page.props.flash.error }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </Transition>

        <!-- Main Content Area -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
          <slot />
        </div>
      </main>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <Transition
      enter-active-class="transition-opacity ease-linear duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity ease-linear duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="sidebarOpen" 
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
      ></div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import SidebarLink from '@/Components/SidebarLink.vue'
import SidebarSubLink from '@/Components/SidebarSubLink.vue'

defineProps({
  pageTitle: String,
  articleStats: Object,
  categoryStats: Object,
  gameStats: Object,
  userStats: Object
})

const sidebarOpen = ref(false)
const isDark = ref(false)
const showNotifications = ref(false)
const notifications = ref([
  {
    id: 1,
    title: 'New Comment',
    message: 'John Doe commented on "Latest Gaming News"',
    created_at: new Date().toISOString(),
    read: false
  },
  {
    id: 2,
    title: 'Article Published',
    message: 'Your article "Gaming Trends 2024" has been published',
    created_at: new Date(Date.now() - 3600000).toISOString(),
    read: false
  },
  {
    id: 3,
    title: 'New User Registration',
    message: 'A new user has registered: jane.smith@example.com',
    created_at: new Date(Date.now() - 7200000).toISOString(),
    read: true
  }
])

const unreadNotifications = computed(() => {
  return notifications.value.filter(n => !n.read).length
})

const toggleTheme = () => {
  isDark.value = !isDark.value
  if (isDark.value) {
    document.documentElement.classList.add('dark')
    localStorage.setItem('theme', 'dark')
  } else {
    document.documentElement.classList.remove('dark')
    localStorage.setItem('theme', 'light')
  }
}

const markAsRead = (notificationId) => {
  const notification = notifications.value.find(n => n.id === notificationId)
  if (notification) {
    notification.read = true
  }
}

const markAllAsRead = () => {
  notifications.value.forEach(notification => {
    notification.read = true
  })
}

const formatNotificationTime = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffInMinutes = Math.floor((now - date) / (1000 * 60))
  
  if (diffInMinutes < 1) return 'Just now'
  if (diffInMinutes < 60) return `${diffInMinutes}m ago`
  
  const diffInHours = Math.floor(diffInMinutes / 60)
  if (diffInHours < 24) return `${diffInHours}h ago`
  
  const diffInDays = Math.floor(diffInHours / 24)
  if (diffInDays < 7) return `${diffInDays}d ago`
  
  return date.toLocaleDateString()
}

// Close notifications when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.notifications-container')) {
    showNotifications.value = false
  }
}

onMounted(() => {
  // Initialize theme
  const savedTheme = localStorage.getItem('theme')
  if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    isDark.value = true
    document.documentElement.classList.add('dark')
  }
  
  // Add click outside listener
  document.addEventListener('click', handleClickOutside)
  
  // Simulate real-time notifications (in real app, this would be WebSocket or polling)
  const interval = setInterval(() => {
    // Randomly add new comment notifications
    if (Math.random() > 0.95) { // 5% chance every interval
      const newNotification = {
        id: Date.now(),
        title: 'New Comment',
        message: `Someone commented on an article`,
        created_at: new Date().toISOString(),
        read: false
      }
      notifications.value.unshift(newNotification)
      
      // Keep only last 10 notifications
      if (notifications.value.length > 10) {
        notifications.value = notifications.value.slice(0, 10)
      }
    }
  }, 10000) // Check every 10 seconds
  
  // Cleanup on unmount
  return () => {
    document.removeEventListener('click', handleClickOutside)
    clearInterval(interval)
  }
})
</script>

<style scoped>
/* Custom scrollbar for sidebar */
nav {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e0 transparent;
}

nav::-webkit-scrollbar {
  width: 4px;
}

nav::-webkit-scrollbar-track {
  background: transparent;
}

nav::-webkit-scrollbar-thumb {
  background-color: #cbd5e0;
  border-radius: 2px;
}

nav::-webkit-scrollbar-thumb:hover {
  background-color: #a0aec0;
}
</style>
