<template>
  <Head :title="`User: ${user.name}`" />

  <ModernAdminLayout :page-title="`User: ${user.name}`">
    <div class="max-w-6xl mx-auto space-y-8">
      <!-- Back Button -->
      <div class="mb-6">
        <Link
          :href="route('admin.users.index')"
          class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          Back to Users
        </Link>
      </div>

      <!-- User Profile Header -->
      <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 rounded-3xl shadow-2xl overflow-hidden">
        <div class="relative p-8">
          <!-- Background Pattern -->
          <div class="absolute inset-0 bg-black/20"></div>
          <div class="absolute top-0 left-0 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
          <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple-400/20 rounded-full blur-3xl"></div>
          
          <div class="relative flex items-center justify-between">
            <div class="flex items-center space-x-6">
              <!-- Avatar -->
              <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/20">
                <span class="text-3xl font-bold text-white">
                  {{ user.name.charAt(0).toUpperCase() }}
                </span>
              </div>
              
              <!-- User Info -->
              <div>
                <h1 class="text-3xl font-bold text-white mb-2">{{ user.name }}</h1>
                <p class="text-blue-100 text-lg mb-2">{{ user.email }}</p>
                <div class="flex items-center space-x-4">
                  <!-- Status Badge -->
                  <span
                    :class="user.email_verified_at ? 'bg-green-500/20 text-green-100 border-green-400/30' : 'bg-yellow-500/20 text-yellow-100 border-yellow-400/30'"
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border backdrop-blur-sm"
                  >
                    <svg
                      :class="user.email_verified_at ? 'text-green-300' : 'text-yellow-300'"
                      class="w-4 h-4 mr-2"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path v-if="user.email_verified_at" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                      <path v-else fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ user.email_verified_at ? 'Verified' : 'Unverified' }}
                  </span>
                  
                  <!-- Roles -->
                  <div class="flex flex-wrap gap-2">
                    <span
                      v-for="role in user.roles"
                      :key="role.id"
                      class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 text-white border border-white/30 backdrop-blur-sm"
                    >
                      {{ role.name }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="flex items-center space-x-3">
              <Link
                :href="route('admin.users.edit', user.id)"
                class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white border border-white/30 rounded-lg transition-all duration-200 backdrop-blur-sm"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit User
              </Link>
              <button
                @click="toggleVerification"
                class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white border border-white/30 rounded-lg transition-all duration-200 backdrop-blur-sm"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ user.email_verified_at ? 'Unverify' : 'Verify' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Articles</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.articles_count }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Comments</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.comments_count }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Bookmarks</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.bookmarks_count }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Member Since</p>
              <p class="text-lg font-bold text-gray-900 dark:text-white">{{ stats.join_date }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- User Details -->
        <div class="lg:col-span-1 space-y-6">
          <!-- Basic Info -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Information</h3>
            </div>
            <div class="p-6 space-y-4">
              <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</label>
                <p class="text-gray-900 dark:text-white">{{ user.name }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                <p class="text-gray-900 dark:text-white">{{ user.email }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                <p class="text-gray-900 dark:text-white">
                  {{ user.email_verified_at ? 'Verified' : 'Unverified' }}
                </p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Login</label>
                <p class="text-gray-900 dark:text-white">{{ stats.last_login }}</p>
              </div>
            </div>
          </div>

          <!-- Gaming Profile -->
          <div v-if="user.bio || (user.gaming_preferences && user.gaming_preferences.length) || (user.favorite_games && user.favorite_games.length)" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Gaming Profile</h3>
            </div>
            <div class="p-6 space-y-4">
              <div v-if="user.bio">
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Bio</label>
                <p class="text-gray-900 dark:text-white">{{ user.bio }}</p>
              </div>
              <div v-if="user.gaming_preferences && user.gaming_preferences.length">
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Gaming Preferences</label>
                <div class="flex flex-wrap gap-2 mt-2">
                  <span
                    v-for="preference in user.gaming_preferences"
                    :key="preference"
                    class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 text-xs rounded-full"
                  >
                    {{ preference }}
                  </span>
                </div>
              </div>
              <div v-if="user.favorite_games && user.favorite_games.length">
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Favorite Games</label>
                <div class="flex flex-wrap gap-2 mt-2">
                  <span
                    v-for="game in user.favorite_games"
                    :key="game"
                    class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 text-xs rounded-full"
                  >
                    {{ game }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Recent Articles -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Articles</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ recentArticles.length }} articles</span>
              </div>
            </div>
            <div class="p-6">
              <div v-if="recentArticles.length > 0" class="space-y-4">
                <div
                  v-for="article in recentArticles"
                  :key="article.id"
                  class="flex items-start space-x-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                >
                  <div class="flex-1">
                    <h4 class="font-medium text-gray-900 dark:text-white">{{ article.title }}</h4>
                    <div class="flex items-center space-x-2 mt-2">
                      <span
                        v-for="category in article.categories"
                        :key="category.id"
                        class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 text-xs rounded-full"
                      >
                        {{ category.name }}
                      </span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                      {{ formatDate(article.created_at) }}
                    </p>
                  </div>
                  <div class="flex items-center space-x-2">
                    <span
                      :class="getStatusColor(article.status)"
                      class="px-2 py-1 rounded-full text-xs font-medium"
                    >
                      {{ article.status }}
                    </span>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No articles yet</p>
              </div>
            </div>
          </div>

          <!-- Recent Comments -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Comments</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ recentComments.length }} comments</span>
              </div>
            </div>
            <div class="p-6">
              <div v-if="recentComments.length > 0" class="space-y-4">
                <div
                  v-for="comment in recentComments"
                  :key="comment.id"
                  class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                >
                  <p class="text-gray-900 dark:text-white">{{ comment.content }}</p>
                  <div class="flex items-center justify-between mt-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                      On: <span class="font-medium">{{ comment.article.title }}</span>
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                      {{ formatDate(comment.created_at) }}
                    </p>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No comments yet</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </ModernAdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import ModernAdminLayout from '@/Layouts/ModernAdminLayout.vue'

const props = defineProps({
  user: Object,
  recentArticles: Array,
  recentComments: Array,
  stats: Object,
})

const toggleVerification = () => {
  if (confirm(`Are you sure you want to ${props.user.email_verified_at ? 'unverify' : 'verify'} this user?`)) {
    router.patch(route('admin.users.toggle-verification', props.user.id))
  }
}

const getStatusColor = (status) => {
  const colors = {
    published: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    draft: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    archived: 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
  }
  return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>
