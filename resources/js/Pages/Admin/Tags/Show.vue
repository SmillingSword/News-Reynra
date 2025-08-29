<template>
  <Head :title="`Tag: ${tag.name}`" />

  <ModernAdminLayout :page-title="`Tag: ${tag.name}`">
    <div class="space-y-8">
      <!-- Breadcrumb -->
      <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <Link :href="route('admin.dashboard')" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-9 9a1 1 0 001.414 1.414L9 4.414V17a1 1 0 102 0V4.414l7.293 7.293a1 1 0 001.414-1.414l-9-9z"/>
              </svg>
              Dashboard
            </Link>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
              </svg>
              <Link :href="route('admin.tags.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                Tags
              </Link>
            </div>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ tag.name }}</span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Header with Actions -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
          <div
            class="w-12 h-12 rounded-2xl border-4 border-white shadow-lg flex items-center justify-center"
            :style="{ backgroundColor: tag.color }"
          >
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
          </div>
          <div>
            <div class="flex items-center space-x-3">
              <h1 class="text-3xl font-bold text-gray-900">{{ tag.name }}</h1>
              <span v-if="tag.is_featured" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                Featured
              </span>
            </div>
            <p v-if="tag.description" class="text-gray-600 mt-1">{{ tag.description }}</p>
            <p class="text-sm text-gray-500 mt-1">Created {{ formatDate(tag.created_at) }}</p>
          </div>
        </div>

        <div class="flex items-center space-x-3">
          <button
            @click="toggleFeatured"
            :class="[
              'inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 shadow-sm',
              tag.is_featured 
                ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' 
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
            {{ tag.is_featured ? 'Remove Featured' : 'Make Featured' }}
          </button>

          <Link
            :href="route('admin.tags.edit', tag.slug)"
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Tag
          </Link>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Total Articles</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.articles_count }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Published</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.published_articles }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Recent Usage (30d)</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.recent_usage }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tag Details & Recent Articles -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Tag Details -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
              <h3 class="text-lg font-semibold text-gray-900">Tag Details</h3>
            </div>

            <div class="p-6 space-y-6">
              <!-- Basic Info -->
              <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Basic Information</h4>
                <dl class="mt-3 space-y-3">
                  <div>
                    <dt class="text-sm font-medium text-gray-900">Name</dt>
                    <dd class="text-sm text-gray-600">{{ tag.name }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-900">Slug</dt>
                    <dd class="text-sm text-gray-600 font-mono bg-gray-100 px-2 py-1 rounded">{{ tag.slug }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-900">Color</dt>
                    <dd class="flex items-center space-x-2">
                      <div
                        class="w-4 h-4 rounded-full border border-gray-300"
                        :style="{ backgroundColor: tag.color }"
                      ></div>
                      <span class="text-sm text-gray-600 font-mono">{{ tag.color }}</span>
                    </dd>
                  </div>
                  <div v-if="tag.description">
                    <dt class="text-sm font-medium text-gray-900">Description</dt>
                    <dd class="text-sm text-gray-600">{{ tag.description }}</dd>
                  </div>
                </dl>
              </div>

              <!-- Usage Stats -->
              <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Usage Statistics</h4>
                <dl class="mt-3 space-y-3">
                  <div>
                    <dt class="text-sm font-medium text-gray-900">Usage Count</dt>
                    <dd class="text-sm text-gray-600">{{ tag.usage_count }} times</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-900">Status</dt>
                    <dd>
                      <span :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        tag.is_featured ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'
                      ]">
                        {{ tag.is_featured ? 'Featured' : 'Regular' }}
                      </span>
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-900">Created</dt>
                    <dd class="text-sm text-gray-600">{{ formatDate(tag.created_at) }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-900">Last Updated</dt>
                    <dd class="text-sm text-gray-600">{{ formatDate(tag.updated_at) }}</dd>
                  </div>
                </dl>
              </div>

              <!-- SEO Info -->
              <div v-if="tag.meta && (tag.meta.title || tag.meta.description)">
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">SEO Information</h4>
                <dl class="mt-3 space-y-3">
                  <div v-if="tag.meta.title">
                    <dt class="text-sm font-medium text-gray-900">Meta Title</dt>
                    <dd class="text-sm text-gray-600">{{ tag.meta.title }}</dd>
                  </div>
                  <div v-if="tag.meta.description">
                    <dt class="text-sm font-medium text-gray-900">Meta Description</dt>
                    <dd class="text-sm text-gray-600">{{ tag.meta.description }}</dd>
                  </div>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Articles -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Recent Articles</h3>
                <span class="text-sm text-gray-500">{{ tag.articles.length }} of {{ stats.articles_count }}</span>
              </div>
            </div>

            <div v-if="tag.articles.length > 0" class="divide-y divide-gray-200">
              <div
                v-for="article in tag.articles"
                :key="article.id"
                class="p-6 hover:bg-gray-50 transition-colors duration-200"
              >
                <div class="flex items-start justify-between">
                  <div class="flex-1 min-w-0">
                    <Link
                      :href="route('admin.articles.show', article.id)"
                      class="text-lg font-semibold text-gray-900 hover:text-blue-600 transition-colors duration-200"
                    >
                      {{ article.title }}
                    </Link>
                    <p v-if="article.excerpt" class="text-gray-600 mt-1 line-clamp-2">
                      {{ article.excerpt }}
                    </p>
                    <div class="flex items-center space-x-4 mt-3 text-sm text-gray-500">
                      <span>By {{ article.author?.name || 'Unknown' }}</span>
                      <span>•</span>
                      <span>{{ formatDate(article.created_at) }}</span>
                      <span>•</span>
                      <span :class="[
                        'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                        article.status === 'published' ? 'bg-green-100 text-green-800' :
                        article.status === 'draft' ? 'bg-yellow-100 text-yellow-800' :
                        'bg-gray-100 text-gray-800'
                      ]">
                        {{ article.status }}
                      </span>
                    </div>
                  </div>
                  <div class="ml-4 flex-shrink-0">
                    <Link
                      :href="route('admin.articles.edit', article.id)"
                      class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded-lg transition-colors duration-200"
                    >
                      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                      Edit
                    </Link>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No articles found</h3>
              <p class="mt-1 text-sm text-gray-500">This tag hasn't been used in any articles yet.</p>
              <div class="mt-6">
                <Link
                  :href="route('admin.articles.create')"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                >
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                  </svg>
                  Create Article
                </Link>
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
  tag: Object,
  stats: Object,
})

const toggleFeatured = () => {
  router.patch(route('admin.tags.toggle-featured', props.tag.slug), {}, {
    preserveScroll: true,
  })
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'long',
    day: 'numeric',
    year: 'numeric'
  })
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
