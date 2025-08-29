<template>
  <Head :title="game.name" />

  <ModernAdminLayout :page-title="game.name">
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <Link 
              :href="route('admin.games.index')"
              class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors duration-200"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
            </Link>
            <div>
              <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ game.name }}</h1>
              <p class="mt-2 text-gray-600 dark:text-gray-400">Game details and statistics</p>
            </div>
          </div>
          
          <div class="flex items-center space-x-3">
            <Link 
              :href="route('admin.games.edit', game.slug)"
              class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
              Edit Game
            </Link>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
              <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Articles</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.articles_count }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-xl">
              <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Published Articles</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.published_articles }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl">
              <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Average Rating</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ game.rating ? `${game.rating}/10` : 'N/A' }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Game Details -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Basic Information -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Game Information</h3>
            </div>
            <div class="p-6">
              <!-- Cover Image -->
              <div v-if="game.cover_image" class="mb-6">
                <img 
                  :src="game.cover_image" 
                  :alt="game.name"
                  class="w-full max-w-md mx-auto rounded-lg shadow-lg"
                />
              </div>

              <!-- Description -->
              <div v-if="game.description" class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</h4>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">{{ game.description }}</p>
              </div>

              <!-- Game Details Grid -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-if="game.genre">
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Genre</h4>
                  <p class="text-gray-900 dark:text-white">{{ game.genre }}</p>
                </div>

                <div v-if="game.developer">
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Developer</h4>
                  <p class="text-gray-900 dark:text-white">{{ game.developer }}</p>
                </div>

                <div v-if="game.publisher">
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Publisher</h4>
                  <p class="text-gray-900 dark:text-white">{{ game.publisher }}</p>
                </div>

                <div v-if="game.release_date">
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Release Date</h4>
                  <p class="text-gray-900 dark:text-white">{{ formatDate(game.release_date) }}</p>
                </div>

                <div v-if="game.rating">
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rating</h4>
                  <div class="flex items-center">
                    <span class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ game.rating }}</span>
                    <span class="text-gray-500 dark:text-gray-400 ml-1">/10</span>
                  </div>
                </div>
              </div>

              <!-- Platforms -->
              <div v-if="game.platforms && game.platforms.length > 0" class="mt-6">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Platforms</h4>
                <div class="flex flex-wrap gap-2">
                  <span 
                    v-for="platform in game.platforms" 
                    :key="platform"
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400"
                  >
                    {{ platform }}
                  </span>
                </div>
              </div>

              <!-- Links -->
              <div v-if="game.trailer_url || game.official_website" class="mt-6">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Links</h4>
                <div class="flex flex-wrap gap-3">
                  <a 
                    v-if="game.trailer_url"
                    :href="game.trailer_url" 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200"
                  >
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M8 5v14l11-7z"/>
                    </svg>
                    Watch Trailer
                  </a>
                  <a 
                    v-if="game.official_website"
                    :href="game.official_website" 
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Official Website
                  </a>
                </div>
              </div>

              <!-- Status Badges -->
              <div class="mt-6 flex items-center space-x-3">
                <span 
                  v-if="game.is_featured"
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400"
                >
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                  </svg>
                  Featured Game
                </span>
                <span 
                  :class="[
                    'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                    game.is_active 
                      ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' 
                      : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
                  ]"
                >
                  {{ game.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Recent Articles -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Articles</h3>
                <Link 
                  :href="route('admin.articles.index', { game: game.slug })"
                  class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300"
                >
                  View all articles
                </Link>
              </div>
            </div>

            <div v-if="game.articles && game.articles.length === 0" class="p-12 text-center">
              <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No articles yet</h3>
              <p class="text-gray-600 dark:text-gray-400 mb-6">No articles have been written about this game yet.</p>
              <Link 
                :href="route('admin.articles.create', { game: game.slug })"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Write First Article
              </Link>
            </div>

            <div v-else class="divide-y divide-gray-200 dark:divide-gray-700">
              <div 
                v-for="article in game.articles" 
                :key="article.id"
                class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
              >
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                      <Link 
                        :href="route('admin.articles.show', article.id)"
                        class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200"
                      >
                        {{ article.title }}
                      </Link>
                    </h4>
                    <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
                      <span>by {{ article.author.name }}</span>
                      <span>{{ formatDate(article.created_at) }}</span>
                      <span 
                        :class="[
                          'px-2 py-1 rounded-full text-xs font-medium',
                          article.status === 'published' 
                            ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                            : article.status === 'draft'
                            ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'
                            : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                        ]"
                      >
                        {{ article.status }}
                      </span>
                    </div>
                  </div>
                  <Link 
                    :href="route('admin.articles.edit', article.id)"
                    class="ml-4 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium"
                  >
                    Edit
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Quick Actions -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
              <Link 
                :href="route('admin.games.edit', game.slug)"
                class="flex items-center space-x-3 p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200 group"
              >
                <div class="p-2 bg-blue-500 rounded-lg group-hover:scale-110 transition-transform duration-200">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </div>
                <div>
                  <div class="font-medium text-gray-900 dark:text-white">Edit Game</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">Update game information</div>
                </div>
              </Link>

              <Link 
                :href="route('admin.articles.create', { game: game.slug })"
                class="flex items-center space-x-3 p-3 rounded-xl bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors duration-200 group"
              >
                <div class="p-2 bg-green-500 rounded-lg group-hover:scale-110 transition-transform duration-200">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                  </svg>
                </div>
                <div>
                  <div class="font-medium text-gray-900 dark:text-white">Write Article</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">Create new content</div>
                </div>
              </Link>

              <button
                @click="toggleFeatured"
                class="flex items-center space-x-3 p-3 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors duration-200 group w-full"
              >
                <div class="p-2 bg-yellow-500 rounded-lg group-hover:scale-110 transition-transform duration-200">
                  <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                  </svg>
                </div>
                <div>
                  <div class="font-medium text-gray-900 dark:text-white">
                    {{ game.is_featured ? 'Remove Featured' : 'Make Featured' }}
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">Toggle featured status</div>
                </div>
              </button>
            </div>
          </div>

          <!-- Game Meta -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Game Meta</h3>
            <div class="space-y-4 text-sm">
              <div>
                <span class="text-gray-600 dark:text-gray-400">Slug:</span>
                <span class="ml-2 font-mono text-gray-900 dark:text-white">{{ game.slug }}</span>
              </div>
              <div>
                <span class="text-gray-600 dark:text-gray-400">Created:</span>
                <span class="ml-2 text-gray-900 dark:text-white">{{ formatDate(game.created_at) }}</span>
              </div>
              <div>
                <span class="text-gray-600 dark:text-gray-400">Updated:</span>
                <span class="ml-2 text-gray-900 dark:text-white">{{ formatDate(game.updated_at) }}</span>
              </div>
            </div>
          </div>

          <!-- SEO Information -->
          <div v-if="game.meta && (game.meta.title || game.meta.description)" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">SEO Information</h3>
            <div class="space-y-4">
              <div v-if="game.meta.title">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meta Title</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ game.meta.title }}</p>
              </div>
              <div v-if="game.meta.description">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meta Description</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ game.meta.description }}</p>
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
  game: Object,
  stats: Object,
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const toggleFeatured = () => {
  router.patch(route('admin.games.toggle-featured', props.game.slug), {}, {
    preserveScroll: true,
  })
}
</script>
