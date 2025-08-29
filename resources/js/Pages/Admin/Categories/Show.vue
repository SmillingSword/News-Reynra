<template>
  <ModernAdminLayout page-title="Category Details">
    <div class="max-w-6xl mx-auto space-y-8">
      <!-- Header -->
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div class="flex items-center space-x-4">
          <div 
            class="w-16 h-16 rounded-2xl flex items-center justify-center text-white font-bold text-xl shadow-lg"
            :style="{ 
              background: `linear-gradient(135deg, ${category.color || '#3B82F6'}, ${adjustColor(category.color || '#3B82F6', -20)})` 
            }"
          >
            {{ category.name.charAt(0).toUpperCase() }}
          </div>
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ category.name }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ category.description || 'No description provided' }}</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <span :class="getStatusClass(category.is_active)" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
            <div class="w-2 h-2 rounded-full mr-2" :class="category.is_active ? 'bg-green-500' : 'bg-gray-400'"></div>
            {{ category.is_active ? 'Active' : 'Inactive' }}
          </span>
          
          <Link 
            :href="route('admin.categories.edit', category.slug)" 
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors duration-200"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Category
          </Link>
          
          <Link 
            :href="route('admin.categories.index')" 
            class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition-colors duration-200"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Categories
          </Link>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <div>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.articles_count }}</p>
              <p class="text-sm text-gray-600 dark:text-gray-400">Total Articles</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
            <div>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.published_articles }}</p>
              <p class="text-sm text-gray-600 dark:text-gray-400">Published Articles</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
              </svg>
            </div>
            <div>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.children_count }}</p>
              <p class="text-sm text-gray-600 dark:text-gray-400">Subcategories</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Category Details -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Basic Information -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Category Information</h3>
            </div>
            <div class="p-6 space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Name</label>
                  <p class="text-gray-900 dark:text-white">{{ category.name }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Slug</label>
                  <p class="text-gray-900 dark:text-white font-mono text-sm">/{{ category.slug }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Color</label>
                  <div class="flex items-center space-x-2">
                    <div 
                      class="w-6 h-6 rounded border border-gray-300 dark:border-gray-600"
                      :style="{ backgroundColor: category.color }"
                    ></div>
                    <span class="text-gray-900 dark:text-white font-mono text-sm">{{ category.color }}</span>
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Sort Order</label>
                  <p class="text-gray-900 dark:text-white">{{ category.sort_order }}</p>
                </div>
              </div>
              
              <div v-if="category.description">
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Description</label>
                <p class="text-gray-900 dark:text-white">{{ category.description }}</p>
              </div>

              <div v-if="category.icon">
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Icon</label>
                <p class="text-gray-900 dark:text-white font-mono text-sm">{{ category.icon }}</p>
              </div>

              <div v-if="category.parent">
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Parent Category</label>
                <Link 
                  :href="route('admin.categories.show', category.parent.slug)"
                  class="inline-flex items-center space-x-2 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                >
                  <div 
                    class="w-4 h-4 rounded"
                    :style="{ backgroundColor: category.parent.color }"
                  ></div>
                  <span>{{ category.parent.name }}</span>
                </Link>
              </div>
            </div>
          </div>

          <!-- Recent Articles -->
          <div v-if="category.articles && category.articles.length > 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Articles</h3>
                <Link 
                  :href="route('admin.articles.index', { category: category.slug })"
                  class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                >
                  View All
                </Link>
              </div>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
              <div 
                v-for="article in category.articles" 
                :key="article.id"
                class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
              >
                <div class="flex items-center justify-between">
                  <div class="flex-1 min-w-0">
                    <Link 
                      :href="route('admin.articles.show', article.slug)"
                      class="text-lg font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200"
                    >
                      {{ article.title }}
                    </Link>
                    <p v-if="article.subtitle" class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ article.subtitle }}</p>
                    <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
                      <span>by {{ article.author?.name || 'Unknown' }}</span>
                      <span>{{ formatDate(article.created_at) }}</span>
                    </div>
                  </div>
                  <div class="ml-4">
                    <span :class="getArticleStatusClass(article.status)" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                      {{ getArticleStatusText(article.status) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- SEO Information -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">SEO Information</h3>
            </div>
            <div class="p-6 space-y-4">
              <div v-if="category.meta?.title">
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Meta Title</label>
                <p class="text-gray-900 dark:text-white text-sm">{{ category.meta.title }}</p>
              </div>
              <div v-if="category.meta?.description">
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Meta Description</label>
                <p class="text-gray-900 dark:text-white text-sm">{{ category.meta.description }}</p>
              </div>
              <div v-if="!category.meta?.title && !category.meta?.description">
                <p class="text-gray-500 dark:text-gray-400 text-sm italic">No SEO information provided</p>
              </div>
            </div>
          </div>

          <!-- Subcategories -->
          <div v-if="category.children && category.children.length > 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Subcategories</h3>
            </div>
            <div class="p-6 space-y-3">
              <Link 
                v-for="child in category.children" 
                :key="child.id"
                :href="route('admin.categories.show', child.slug)"
                class="flex items-center space-x-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200 group"
              >
                <div 
                  class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-medium text-sm"
                  :style="{ backgroundColor: child.color }"
                >
                  {{ child.name.charAt(0).toUpperCase() }}
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">
                    {{ child.name }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ child.articles_count || 0 }} articles</p>
                </div>
              </Link>
            </div>
          </div>

          <!-- Timestamps -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Timestamps</h3>
            </div>
            <div class="p-6 space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Created</label>
                <p class="text-gray-900 dark:text-white text-sm">{{ formatDate(category.created_at) }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Last Updated</label>
                <p class="text-gray-900 dark:text-white text-sm">{{ formatDate(category.updated_at) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </ModernAdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import ModernAdminLayout from '@/Layouts/ModernAdminLayout.vue'

const props = defineProps({
  category: {
    type: Object,
    required: true
  },
  stats: {
    type: Object,
    required: true
  }
})

// Methods
const getStatusClass = (isActive) => {
  return isActive 
    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
    : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
}

const getArticleStatusClass = (status) => {
  const classes = {
    'draft': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'published': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    'pending_review': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'
  }
  return classes[status] || classes.draft
}

const getArticleStatusText = (status) => {
  const texts = {
    'draft': 'Draft',
    'published': 'Published',
    'pending_review': 'Pending Review'
  }
  return texts[status] || 'Draft'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const adjustColor = (color, amount) => {
  const usePound = color[0] === '#'
  const col = usePound ? color.slice(1) : color
  const num = parseInt(col, 16)
  let r = (num >> 16) + amount
  let g = (num >> 8 & 0x00FF) + amount
  let b = (num & 0x0000FF) + amount
  r = r > 255 ? 255 : r < 0 ? 0 : r
  g = g > 255 ? 255 : g < 0 ? 0 : g
  b = b > 255 ? 255 : b < 0 ? 0 : b
  return (usePound ? '#' : '') + (r << 16 | g << 8 | b).toString(16).padStart(6, '0')
}
</script>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: rgba(156, 163, 175, 0.5);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(156, 163, 175, 0.7);
}
</style>
