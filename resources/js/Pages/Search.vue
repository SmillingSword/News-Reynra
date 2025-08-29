<template>
  <Head>
    <title>{{ searchTitle }} | News Reynra</title>
    <meta name="description" :content="searchDescription" />
  </Head>

  <PublicLayout>
    <!-- Search Header -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <h1 class="text-4xl font-bold mb-4">{{ searchTitle }}</h1>
          <p class="text-blue-100 text-lg">{{ searchDescription }}</p>
        </div>
      </div>
    </section>

    <!-- Search Form -->
    <section class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 sticky top-16 z-40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <form @submit.prevent="performSearch" class="max-w-4xl mx-auto">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari berita, review, game, atau topik gaming lainnya..."
              class="w-full pl-12 pr-32 py-4 text-lg border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm"
              autofocus
            />
            <svg class="absolute left-4 top-4 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <button 
              type="submit"
              class="absolute right-2 top-2 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
            >
              Search
            </button>
          </div>
        </form>

        <!-- Search Suggestions -->
        <div v-if="!query && popularSearches?.length" class="max-w-4xl mx-auto mt-6">
          <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Popular searches:</p>
          <div class="flex flex-wrap gap-2">
            <button 
              v-for="search in popularSearches" 
              :key="search"
              @click="searchQuery = search; performSearch()"
              class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
            >
              {{ search }}
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Search Results -->
    <section class="py-12 bg-gray-50 dark:bg-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-16">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <p class="text-gray-600 dark:text-gray-400">Searching...</p>
        </div>

        <!-- No Query State -->
        <div v-else-if="!query" class="text-center py-16">
          <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Start Your Search</h3>
          <p class="text-gray-600 dark:text-gray-400 mb-8">Enter keywords to find articles, reviews, and gaming content</p>
          
          <!-- Quick Categories -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-2xl mx-auto">
            <Link 
              :href="route('articles.index', { category: 'review' })"
              class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 text-center group"
            >
              <svg class="w-8 h-8 text-blue-600 mx-auto mb-2 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
              </svg>
              <h4 class="font-semibold text-gray-900 dark:text-white">Reviews</h4>
            </Link>
            
            <Link 
              :href="route('articles.index', { category: 'esports' })"
              class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 text-center group"
            >
              <svg class="w-8 h-8 text-purple-600 mx-auto mb-2 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>
              </svg>
              <h4 class="font-semibold text-gray-900 dark:text-white">Esports</h4>
            </Link>
            
            <Link 
              :href="route('articles.index', { category: 'guide' })"
              class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 text-center group"
            >
              <svg class="w-8 h-8 text-green-600 mx-auto mb-2 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
              </svg>
              <h4 class="font-semibold text-gray-900 dark:text-white">Guides</h4>
            </Link>
            
            <Link 
              :href="route('articles.index', { platform: 'pc' })"
              class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 text-center group"
            >
              <svg class="w-8 h-8 text-orange-600 mx-auto mb-2 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"/>
              </svg>
              <h4 class="font-semibold text-gray-900 dark:text-white">PC Gaming</h4>
            </Link>
          </div>
        </div>

        <!-- No Results -->
        <div v-else-if="results.articles?.length === 0 && results.games?.length === 0" class="text-center py-16">
          <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.007-5.824-2.562M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
          </svg>
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No results found</h3>
          <p class="text-gray-600 dark:text-gray-400 mb-6">
            We couldn't find anything for "<span class="font-medium">{{ query }}</span>". Try different keywords or browse our categories.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button @click="searchQuery = ''; query = ''" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
              Clear Search
            </button>
            <Link :href="route('articles.index')" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
              Browse All Articles
            </Link>
          </div>
        </div>

        <!-- Search Results -->
        <div v-else>
          <!-- Results Summary -->
          <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
              Search Results for "{{ query }}"
            </h2>
            <p class="text-gray-600 dark:text-gray-400">
              Found {{ totalResults }} results
            </p>
          </div>

          <!-- Filter Tabs -->
          <div class="flex space-x-1 mb-8 bg-gray-100 dark:bg-gray-700 p-1 rounded-lg w-fit">
            <button 
              @click="activeTab = 'all'"
              :class="[
                'px-4 py-2 rounded-md text-sm font-medium transition-colors',
                activeTab === 'all' 
                  ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' 
                  : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
              ]"
            >
              All ({{ totalResults }})
            </button>
            <button 
              @click="activeTab = 'articles'"
              :class="[
                'px-4 py-2 rounded-md text-sm font-medium transition-colors',
                activeTab === 'articles' 
                  ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' 
                  : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
              ]"
            >
              Articles ({{ results.articles?.length || 0 }})
            </button>
            <button 
              @click="activeTab = 'games'"
              :class="[
                'px-4 py-2 rounded-md text-sm font-medium transition-colors',
                activeTab === 'games' 
                  ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' 
                  : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
              ]"
            >
              Games ({{ results.games?.length || 0 }})
            </button>
          </div>

          <!-- Articles Results -->
          <div v-if="(activeTab === 'all' || activeTab === 'articles') && results.articles?.length" class="mb-12">
            <h3 v-if="activeTab === 'all'" class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Articles</h3>
            
            <div class="space-y-6">
              <article 
                v-for="article in results.articles" 
                :key="article.id"
                class="group cursor-pointer bg-white dark:bg-gray-700 rounded-xl shadow-sm hover:shadow-lg transition-all duration-200 p-6"
              >
                <Link :href="route('articles.show', article.slug)" class="flex gap-6">
                  <div class="flex-shrink-0 w-48 h-32 bg-gray-200 dark:bg-gray-600 rounded-lg overflow-hidden">
                    <img 
                      v-if="article.featured_image"
                      :src="article.featured_image" 
                      :alt="article.title"
                      class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                      <span class="bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 px-2 py-1 rounded text-xs font-medium">
                        {{ article.category?.name }}
                      </span>
                      <span v-if="article.is_featured" class="bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 px-2 py-1 rounded text-xs font-medium">
                        Featured
                      </span>
                    </div>
                    <h3 class="text-gray-900 dark:text-white font-bold text-xl line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-2">
                      {{ article.title }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 line-clamp-3 mb-4">
                      {{ article.excerpt }}
                    </p>
                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                      <div class="flex items-center">
                        <span>{{ article.author?.name }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ formatDate(article.published_at) }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ article.reading_time }} min read</span>
                      </div>
                      <div class="flex items-center space-x-4">
                        <span>{{ article.views || 0 }} views</span>
                      </div>
                    </div>
                  </div>
                </Link>
              </article>
            </div>
          </div>

          <!-- Games Results -->
          <div v-if="(activeTab === 'all' || activeTab === 'games') && results.games?.length" class="mb-12">
            <h3 v-if="activeTab === 'all'" class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Games</h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
              <Link 
                v-for="game in results.games" 
                :key="game.id"
                :href="route('games.show', game.slug)"
                class="group cursor-pointer"
              >
                <div class="relative overflow-hidden rounded-xl bg-gray-200 dark:bg-gray-700 aspect-[3/4] mb-3">
                  <img 
                    v-if="game.cover_image"
                    :src="game.cover_image" 
                    :alt="game.title"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                  />
                </div>
                <h3 class="text-gray-900 dark:text-white font-semibold text-sm line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-1">
                  {{ game.title }}
                </h3>
                <p class="text-gray-500 dark:text-gray-400 text-xs">{{ game.developer }}</p>
                <div v-if="game.genres?.length" class="flex flex-wrap gap-1 mt-2">
                  <span v-for="genre in game.genres.slice(0, 2)" :key="genre" class="px-1 py-0.5 bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-400 rounded text-xs">
                    {{ genre }}
                  </span>
                </div>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, watch, onMounted } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
  query: String,
  results: Object,
  popularSearches: Array
})

// Reactive states
const searchQuery = ref(props.query || '')
const loading = ref(false)
const activeTab = ref('all')

// Computed properties
const searchTitle = computed(() => {
  return props.query ? `Search results for "${props.query}"` : 'Search'
})

const searchDescription = computed(() => {
  if (props.query) {
    return `Found ${totalResults.value} results for "${props.query}"`
  }
  return 'Search for gaming news, reviews, guides, and more on News Reynra'
})

const totalResults = computed(() => {
  return (props.results?.articles?.length || 0) + (props.results?.games?.length || 0)
})

// Methods
const performSearch = () => {
  if (!searchQuery.value.trim()) return
  
  loading.value = true
  
  router.get(route('search'), { q: searchQuery.value }, {
    preserveState: true,
    preserveScroll: false,
    onFinish: () => {
      loading.value = false
    }
  })
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Watch for query changes from URL
watch(() => props.query, (newQuery) => {
  searchQuery.value = newQuery || ''
})

// Focus search input on mount
onMounted(() => {
  const searchInput = document.querySelector('input[type="text"]')
  if (searchInput && !props.query) {
    searchInput.focus()
  }
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
