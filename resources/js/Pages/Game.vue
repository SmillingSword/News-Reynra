<template>
  <Head>
    <title>{{ game.title }} | News Reynra</title>
    <meta name="description" :content="gameDescription" />
    <meta name="keywords" :content="gameKeywords" />
    
    <!-- OpenGraph -->
    <meta property="og:title" :content="game.title" />
    <meta property="og:description" :content="gameDescription" />
    <meta property="og:image" :content="game.cover_image" />
    <meta property="og:type" content="website" />
  </Head>

  <PublicLayout>
    <!-- Breadcrumb -->
    <nav class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <ol class="flex items-center space-x-2 text-sm">
          <li>
            <Link :href="route('home')" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
              Beranda
            </Link>
          </li>
          <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
          </svg>
          <li>
            <span class="text-gray-500 dark:text-gray-400">Games</span>
          </li>
          <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
          </svg>
          <li class="text-gray-900 dark:text-white font-medium truncate">
            {{ game.title }}
          </li>
        </ol>
      </div>
    </nav>

    <!-- Game Header -->
    <section class="bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
          <!-- Game Cover -->
          <div class="lg:col-span-1">
            <div class="relative overflow-hidden rounded-2xl bg-gray-700 aspect-[3/4] max-w-sm mx-auto">
              <img 
                v-if="game.cover_image"
                :src="game.cover_image" 
                :alt="game.title"
                class="w-full h-full object-cover"
              />
              <div v-else class="flex items-center justify-center h-full">
                <svg class="w-16 h-16 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                </svg>
              </div>
            </div>
          </div>

          <!-- Game Info -->
          <div class="lg:col-span-2">
            <div class="flex flex-wrap gap-2 mb-4">
              <span v-for="genre in game.genres" :key="genre" class="px-3 py-1 bg-blue-600 text-white rounded-full text-sm font-medium">
                {{ genre }}
              </span>
            </div>

            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ game.title }}</h1>
            
            <p v-if="game.description" class="text-xl text-blue-100 mb-6 leading-relaxed">
              {{ game.description }}
            </p>

            <!-- Game Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
              <div class="space-y-4">
                <div v-if="game.developer">
                  <h3 class="text-sm font-semibold text-blue-200 uppercase tracking-wider">Developer</h3>
                  <p class="text-white">{{ game.developer }}</p>
                </div>
                
                <div v-if="game.publisher">
                  <h3 class="text-sm font-semibold text-blue-200 uppercase tracking-wider">Publisher</h3>
                  <p class="text-white">{{ game.publisher }}</p>
                </div>

                <div v-if="game.release_date">
                  <h3 class="text-sm font-semibold text-blue-200 uppercase tracking-wider">Release Date</h3>
                  <p class="text-white">{{ formatDate(game.release_date) }}</p>
                </div>
              </div>

              <div class="space-y-4">
                <div v-if="game.platforms?.length">
                  <h3 class="text-sm font-semibold text-blue-200 uppercase tracking-wider">Platforms</h3>
                  <div class="flex flex-wrap gap-2 mt-1">
                    <span v-for="platform in game.platforms" :key="platform" class="px-2 py-1 bg-white/20 text-white rounded text-sm">
                      {{ platform }}
                    </span>
                  </div>
                </div>

                <div v-if="game.esrb_rating">
                  <h3 class="text-sm font-semibold text-blue-200 uppercase tracking-wider">ESRB Rating</h3>
                  <p class="text-white">{{ game.esrb_rating }}</p>
                </div>

                <div v-if="game.metacritic_score">
                  <h3 class="text-sm font-semibold text-blue-200 uppercase tracking-wider">Metacritic Score</h3>
                  <div class="flex items-center space-x-2">
                    <span class="text-2xl font-bold text-white">{{ game.metacritic_score }}</span>
                    <div class="w-16 h-2 bg-white/20 rounded-full overflow-hidden">
                      <div 
                        class="h-full bg-gradient-to-r from-red-500 via-yellow-500 to-green-500 rounded-full"
                        :style="{ width: `${game.metacritic_score}%` }"
                      ></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-4">
              <a 
                v-if="game.official_website"
                :href="game.official_website" 
                target="_blank"
                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Official Website
              </a>
              
              <Link 
                :href="route('articles.index', { game: game.slug })"
                class="inline-flex items-center px-6 py-3 bg-white/20 hover:bg-white/30 text-white font-semibold rounded-lg transition-colors backdrop-blur-sm"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                View News ({{ articlesCount }})
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Screenshots & Media -->
    <section v-if="game.screenshots?.length || game.trailers?.length" class="py-16 bg-white dark:bg-gray-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Media</h2>
        
        <!-- Screenshots -->
        <div v-if="game.screenshots?.length" class="mb-12">
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Screenshots</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
              v-for="(screenshot, index) in game.screenshots" 
              :key="index"
              class="relative overflow-hidden rounded-xl bg-gray-200 dark:bg-gray-700 aspect-video cursor-pointer group"
              @click="openLightbox(screenshot)"
            >
              <img 
                :src="screenshot" 
                :alt="`${game.title} Screenshot ${index + 1}`"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300 flex items-center justify-center">
                <svg class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                  <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Trailers -->
        <div v-if="game.trailers?.length" class="mb-12">
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Trailers</h3>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div 
              v-for="(trailer, index) in game.trailers" 
              :key="index"
              class="relative overflow-hidden rounded-xl bg-gray-200 dark:bg-gray-700 aspect-video"
            >
              <!-- This would be replaced with actual video embed -->
              <div class="w-full h-full flex items-center justify-center bg-gray-800">
                <div class="text-center text-white">
                  <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                  </svg>
                  <p class="text-sm">Trailer {{ index + 1 }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Related Articles -->
    <section v-if="articles?.length" class="py-16 bg-gray-50 dark:bg-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
          <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Latest News & Reviews</h2>
          <Link 
            :href="route('articles.index', { game: game.slug })"
            class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium"
          >
            View All →
          </Link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <article 
            v-for="article in articles" 
            :key="article.id"
            class="group cursor-pointer bg-white dark:bg-gray-700 rounded-xl shadow-sm hover:shadow-lg transition-all duration-200"
          >
            <Link :href="route('articles.show', article.slug)">
              <div class="relative overflow-hidden rounded-t-xl bg-gray-200 dark:bg-gray-600 aspect-video">
                <img 
                  v-if="article.featured_image"
                  :src="article.featured_image" 
                  :alt="article.title"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
              </div>
              <div class="p-6">
                <span class="inline-block bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 px-2 py-1 rounded text-xs font-medium mb-2">
                  {{ article.category?.name }}
                </span>
                <h3 class="text-gray-900 dark:text-white font-bold text-lg line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-2">
                  {{ article.title }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2 mb-4">
                  {{ article.excerpt }}
                </p>
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                  <span>{{ article.author?.name }}</span>
                  <span class="mx-2">•</span>
                  <span>{{ formatDate(article.published_at) }}</span>
                </div>
              </div>
            </Link>
          </article>
        </div>
      </div>
    </section>

    <!-- Similar Games -->
    <section v-if="similarGames?.length" class="py-16 bg-white dark:bg-gray-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Similar Games</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
          <Link 
            v-for="similarGame in similarGames" 
            :key="similarGame.id"
            :href="route('games.show', similarGame.slug)"
            class="group cursor-pointer"
          >
            <div class="relative overflow-hidden rounded-xl bg-gray-200 dark:bg-gray-700 aspect-[3/4] mb-3">
              <img 
                v-if="similarGame.cover_image"
                :src="similarGame.cover_image" 
                :alt="similarGame.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
            </div>
            <h3 class="text-gray-900 dark:text-white font-semibold text-sm line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
              {{ similarGame.title }}
            </h3>
            <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">{{ similarGame.developer }}</p>
          </Link>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
  game: Object,
  articles: Array,
  similarGames: Array,
  articlesCount: Number
})

// Computed properties
const gameDescription = computed(() => {
  return props.game.description || `${props.game.title} - Game information, news, and reviews on News Reynra`
})

const gameKeywords = computed(() => {
  const keywords = [props.game.title]
  if (props.game.developer) keywords.push(props.game.developer)
  if (props.game.publisher) keywords.push(props.game.publisher)
  if (props.game.genres) keywords.push(...props.game.genres)
  if (props.game.platforms) keywords.push(...props.game.platforms)
  return keywords.join(', ')
})

// Methods
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const openLightbox = (imageUrl) => {
  // TODO: Implement lightbox functionality
  window.open(imageUrl, '_blank')
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
