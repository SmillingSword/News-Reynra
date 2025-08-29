<template>
  <ModernAdminLayout :page-title="`View Article: ${article.title}`">
    <div class="space-y-8">
      <!-- Header Actions -->
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div class="flex items-center space-x-4">
          <Link 
            :href="route('admin.articles.index')"
            class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition-all duration-200"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Articles
          </Link>
          
          <!-- Status Badge -->
          <span :class="getStatusClass(article.status)" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium">
            <div class="w-2 h-2 rounded-full mr-2" :class="getStatusDotClass(article.status)"></div>
            {{ getStatusText(article.status) }}
          </span>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Article Options -->
          <div class="flex items-center space-x-2">
            <span v-if="article.is_featured" class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 text-xs font-medium rounded-lg">
              <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
              Featured
            </span>
            <span v-if="article.is_sponsored" class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400 text-xs font-medium rounded-lg">
              Sponsored
            </span>
            <span v-if="article.is_breaking" class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 text-xs font-medium rounded-lg">
              Breaking News
            </span>
          </div>
          
          <Link 
            :href="route('admin.articles.edit', article.slug)"
            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Article
          </Link>
        </div>
      </div>

      <!-- Article Header -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Featured Image -->
        <div v-if="article.featured_image" class="relative h-64 lg:h-80">
          <img 
            :src="article.featured_image" 
            :alt="article.title"
            class="w-full h-full object-cover"
          >
          <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
        </div>
        
        <div class="p-8">
          <!-- Title and Subtitle -->
          <div class="mb-6">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4 leading-tight">
              {{ article.title }}
            </h1>
            <p v-if="article.subtitle" class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed">
              {{ article.subtitle }}
            </p>
          </div>

          <!-- Article Meta -->
          <div class="flex flex-wrap items-center gap-6 text-sm text-gray-500 dark:text-gray-400 mb-6">
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                <span class="text-xs font-medium text-white">
                  {{ (article.author?.name || 'Unknown').charAt(0).toUpperCase() }}
                </span>
              </div>
              <span class="font-medium">{{ article.author?.name || 'Unknown Author' }}</span>
            </div>
            
            <div class="flex items-center space-x-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <span>{{ formatDate(article.created_at) }}</span>
            </div>
            
            <div v-if="article.published_at" class="flex items-center space-x-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>Published {{ formatDate(article.published_at) }}</span>
            </div>
            
            <div v-if="article.reading_time" class="flex items-center space-x-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
              <span>{{ article.reading_time }} min read</span>
            </div>
          </div>

          <!-- Categories and Tags -->
          <div class="flex flex-wrap gap-4 mb-6">
            <div v-if="article.categories && article.categories.length > 0" class="flex flex-wrap gap-2">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Categories:</span>
              <span 
                v-for="category in article.categories" 
                :key="category.id"
                class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium"
                :style="{ 
                  backgroundColor: category.color + '20', 
                  color: category.color 
                }"
              >
                {{ category.name }}
              </span>
            </div>
            
            <div v-if="article.tags && article.tags.length > 0" class="flex flex-wrap gap-2">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Tags:</span>
              <span 
                v-for="tag in article.tags" 
                :key="tag.id"
                class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm"
              >
                #{{ tag.name }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Article Content -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Excerpt -->
          <div v-if="article.excerpt" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
              </svg>
              Excerpt
            </h3>
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 p-4 rounded-xl">
              <p class="text-gray-700 dark:text-gray-300 leading-relaxed italic">{{ article.excerpt }}</p>
            </div>
          </div>

          <!-- Content -->
          <div class="bg-white/70 dark:bg-gray-900/60 backdrop-blur rounded-2xl ring-1 ring-black/5 dark:ring-white/10 p-6 lg:p-8 shadow-lg">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
              <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              Article Content
            </h3>
            <div class="prose prose-lg dark:prose-invert max-w-none" v-html="article.content || '<p class=\'text-gray-500 italic\'>No content available</p>'"></div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Article Statistics -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
              Statistics
            </h3>
            <div class="space-y-4">
              <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="text-sm text-gray-600 dark:text-gray-400">Article ID</span>
                <span class="font-medium text-gray-900 dark:text-white">#{{ article.id }}</span>
              </div>
              <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="text-sm text-gray-600 dark:text-gray-400">Word Count</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ getWordCount(article.content) }}</span>
              </div>
              <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="text-sm text-gray-600 dark:text-gray-400">Last Updated</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ formatRelativeDate(article.updated_at) }}</span>
              </div>
            </div>
          </div>

          <!-- SEO Information -->
          <div v-if="article.meta_title || article.meta_description" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              SEO Meta
            </h3>
            <div class="space-y-4">
              <div v-if="article.meta_title">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meta Title</label>
                <p class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">{{ article.meta_title }}</p>
              </div>
              <div v-if="article.meta_description">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meta Description</label>
                <p class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">{{ article.meta_description }}</p>
              </div>
            </div>
          </div>

          <!-- Article Settings -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              Settings
            </h3>
            <div class="space-y-3">
              <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="text-sm text-gray-600 dark:text-gray-400">Featured Article</span>
                <span :class="article.is_featured ? 'text-green-600 dark:text-green-400' : 'text-gray-500'">
                  {{ article.is_featured ? 'Yes' : 'No' }}
                </span>
              </div>
              <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="text-sm text-gray-600 dark:text-gray-400">Sponsored Content</span>
                <span :class="article.is_sponsored ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500'">
                  {{ article.is_sponsored ? 'Yes' : 'No' }}
                </span>
              </div>
              <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="text-sm text-gray-600 dark:text-gray-400">Breaking News</span>
                <span :class="article.is_breaking ? 'text-red-600 dark:text-red-400' : 'text-gray-500'">
                  {{ article.is_breaking ? 'Yes' : 'No' }}
                </span>
              </div>
              <div v-if="article.language" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="text-sm text-gray-600 dark:text-gray-400">Language</span>
                <span class="text-gray-900 dark:text-white">{{ article.language }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </ModernAdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import ModernAdminLayout from '@/Layouts/ModernAdminLayout.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  article: Object
})

// Methods
const getStatusClass = (status) => {
  const classes = {
    'draft': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'published': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    'pending_review': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    'scheduled': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'
  }
  return classes[status] || classes.draft
}

const getStatusDotClass = (status) => {
  const classes = {
    'draft': 'bg-gray-500',
    'published': 'bg-green-500',
    'pending_review': 'bg-yellow-500',
    'scheduled': 'bg-blue-500'
  }
  return classes[status] || classes.draft
}

const getStatusText = (status) => {
  const texts = {
    'draft': 'Draft',
    'published': 'Published',
    'pending_review': 'Pending Review',
    'scheduled': 'Scheduled'
  }
  return texts[status] || 'Draft'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatRelativeDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now - date)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  if (diffDays === 1) return 'Yesterday'
  if (diffDays < 7) return `${diffDays} days ago`
  if (diffDays < 30) return `${Math.ceil(diffDays / 7)} weeks ago`
  return formatDate(dateString)
}

const getWordCount = (content) => {
  if (!content) return 0
  const text = content.replace(/<[^>]*>/g, '') // Remove HTML tags
  return text.split(/\s+/).filter(word => word.length > 0).length
}
</script>

<style scoped>
/* Typography tweaks - same as Article.vue */
.prose :deep(h2){ @apply text-2xl font-bold text-gray-900 dark:text-white mt-8 mb-4; }
.prose :deep(h3){ @apply text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3; }
.prose :deep(p){ @apply text-gray-700 dark:text-gray-300 leading-relaxed mb-4; }
.prose :deep(img){ @apply rounded-lg shadow-lg my-6 mx-auto; }
.prose :deep(blockquote){ @apply border-l-4 border-blue-500 pl-4 italic text-gray-600 dark:text-gray-400 my-4; }
.prose :deep(code){ @apply bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-sm; }
.prose :deep(pre){ @apply bg-gray-900 text-white p-4 rounded-lg overflow-x-auto my-4; }
.prose :deep(ul), .prose :deep(ol){ @apply my-4 ml-6; }
.prose :deep(li){ @apply mb-2; }

.prose {
  max-width: none;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
  color: inherit;
}
</style>
