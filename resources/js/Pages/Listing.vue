<template>
  <Head>
    <title>{{ pageTitle }} | News Reynra</title>
    <meta name="description" :content="pageDescription" />
  </Head>

  <PublicLayout>
    <!-- Header Section -->
    <section class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="fade-in-up">
          <nav class="text-sm breadcrumbs mb-4">
            <ol class="flex items-center space-x-2 text-gray-500 dark:text-gray-400">
              <li><Link :href="route('home')" class="hover:text-blue-600 dark:hover:text-blue-400">Beranda</Link></li>
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
              </svg>
              <li class="text-gray-900 dark:text-white font-medium">{{ pageTitle }}</li>
            </ol>
          </nav>
          <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ pageTitle }}</h1>
          <p class="text-gray-600 dark:text-gray-400 text-lg">{{ pageDescription }}</p>
        </div>
      </div>
    </section>

    <!-- Filters & Search -->
    <section class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 sticky top-16 z-40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
          <!-- Search -->
          <div class="flex-1 max-w-md">
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari artikel..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                @keydown.enter="applyFilters"
              />
              <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
          </div>

          <!-- Filters -->
          <div class="flex flex-wrap gap-3">
            <!-- Category Filter -->
            <select 
              v-model="selectedCategory"
              @change="applyFilters"
              class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Semua Kategori</option>
              <option v-for="category in categories" :key="category.id" :value="category.slug">
                {{ category.name }}
              </option>
            </select>

            <!-- Platform Filter -->
            <select 
              v-model="selectedPlatform"
              @change="applyFilters"
              class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Semua Platform</option>
              <option value="pc">PC</option>
              <option value="playstation">PlayStation</option>
              <option value="xbox">Xbox</option>
              <option value="nintendo">Nintendo Switch</option>
              <option value="mobile">Mobile</option>
            </select>

            <!-- Sort Filter -->
            <select 
              v-model="selectedSort"
              @change="applyFilters"
              class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
            >
              <option value="latest">Terbaru</option>
              <option value="trending">Trending</option>
              <option value="popular">Populer</option>
              <option value="oldest">Terlama</option>
            </select>
          </div>
        </div>

        <!-- Active Filters -->
        <div v-if="hasActiveFilters" class="flex flex-wrap gap-2 mt-4">
          <span class="text-sm text-gray-600 dark:text-gray-400 mr-2">Filter aktif:</span>
          
          <span v-if="filters.category" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200">
            {{ getCategoryName(filters.category) }}
            <button @click="clearFilter('category')" class="ml-2 hover:text-blue-600">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
            </button>
          </span>

          <span v-if="filters.platform" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200">
            {{ getPlatformName(filters.platform) }}
            <button @click="clearFilter('platform')" class="ml-2 hover:text-green-600">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
            </button>
          </span>

          <span v-if="filters.search" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-200">
            "{{ filters.search }}"
            <button @click="clearFilter('search')" class="ml-2 hover:text-purple-600">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
            </button>
          </span>

          <button @click="clearAllFilters" class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 underline">
            Hapus semua filter
          </button>
        </div>
      </div>
    </section>

    <!-- Articles Grid -->
    <section class="py-12 bg-gray-50 dark:bg-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Results Info -->
        <div class="flex items-center justify-between mb-8">
          <p class="text-gray-600 dark:text-gray-400">
            Menampilkan {{ articles.from }}-{{ articles.to }} dari {{ articles.total }} artikel
          </p>
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500 dark:text-gray-400">Tampilan:</span>
            <button 
              @click="viewMode = 'grid'"
              :class="[
                'p-2 rounded-lg transition-colors',
                viewMode === 'grid' 
                  ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' 
                  : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'
              ]"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
              </svg>
            </button>
            <button 
              @click="viewMode = 'list'"
              :class="[
                'p-2 rounded-lg transition-colors',
                viewMode === 'list' 
                  ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' 
                  : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'
              ]"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- No Results -->
        <div v-if="articles.data.length === 0" class="text-center py-16">
          <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.007-5.824-2.562M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
          </svg>
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Tidak ada artikel ditemukan</h3>
          <p class="text-gray-500 dark:text-gray-400 mb-6">Coba ubah filter atau kata kunci pencarian Anda</p>
          <button @click="clearAllFilters" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
            Hapus Semua Filter
          </button>
        </div>

        <!-- Articles Grid View -->
        <div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <article 
            v-for="article in articles.data" 
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
                <h3 class="text-gray-900 dark:text-white font-bold text-lg line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-2">
                  {{ article.title }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2 mb-4">
                  {{ article.excerpt }}
                </p>
                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                  <div class="flex items-center">
                    <span>{{ article.author?.name }}</span>
                    <span class="mx-2">•</span>
                    <span>{{ formatDate(article.published_at) }}</span>
                  </div>
                  <span>{{ article.reading_time }} min</span>
                </div>
              </div>
            </Link>
          </article>
        </div>

        <!-- Articles List View -->
        <div v-else class="space-y-6">
          <article 
            v-for="article in articles.data" 
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
                    <span>{{ article.comments_count || 0 }} comments</span>
                  </div>
                </div>
              </div>
            </Link>
          </article>
        </div>

        <!-- Pagination -->
        <div v-if="articles.last_page > 1" class="mt-12">
          <nav class="flex items-center justify-center space-x-2">
            <!-- Previous -->
            <Link 
              v-if="articles.prev_page_url"
              :href="articles.prev_page_url"
              class="px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              Previous
            </Link>

            <!-- Page Numbers -->
            <template v-for="page in paginationPages" :key="page">
              <Link 
                v-if="page !== '...'"
                :href="getPageUrl(page)"
                :class="[
                  'px-3 py-2 text-sm font-medium border rounded-lg transition-colors',
                  page === articles.current_page
                    ? 'bg-blue-600 text-white border-blue-600'
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'
                ]"
              >
                {{ page }}
              </Link>
              <span v-else class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">...</span>
            </template>

            <!-- Next -->
            <Link 
              v-if="articles.next_page_url"
              :href="articles.next_page_url"
              class="px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              Next
            </Link>
          </nav>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
  articles: Object,
  categories: Array,
  filters: Object,
  pageTitle: String,
  pageDescription: String
})

// Reactive filter states
const searchQuery = ref(props.filters.search || '')
const selectedCategory = ref(props.filters.category || '')
const selectedPlatform = ref(props.filters.platform || '')
const selectedSort = ref(props.filters.sort || 'latest')
const viewMode = ref('grid')

// Computed properties
const hasActiveFilters = computed(() => {
  return props.filters.category || props.filters.platform || props.filters.search
})

const paginationPages = computed(() => {
  const pages = []
  const current = props.articles.current_page
  const last = props.articles.last_page
  
  // Always show first page
  if (current > 3) {
    pages.push(1)
    if (current > 4) pages.push('...')
  }
  
  // Show pages around current
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i)
  }
  
  // Always show last page
  if (current < last - 2) {
    if (current < last - 3) pages.push('...')
    pages.push(last)
  }
  
  return pages
})

// Methods
const applyFilters = () => {
  const params = {}
  
  if (searchQuery.value) params.search = searchQuery.value
  if (selectedCategory.value) params.category = selectedCategory.value
  if (selectedPlatform.value) params.platform = selectedPlatform.value
  if (selectedSort.value !== 'latest') params.sort = selectedSort.value
  
  router.get(route('articles.index'), params, {
    preserveState: true,
    preserveScroll: true
  })
}

const clearFilter = (filterType) => {
  const params = { ...props.filters }
  delete params[filterType]
  
  if (filterType === 'search') searchQuery.value = ''
  if (filterType === 'category') selectedCategory.value = ''
  if (filterType === 'platform') selectedPlatform.value = ''
  
  router.get(route('articles.index'), params, {
    preserveState: true,
    preserveScroll: true
  })
}

const clearAllFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  selectedPlatform.value = ''
  selectedSort.value = 'latest'
  
  router.get(route('articles.index'), {}, {
    preserveState: true,
    preserveScroll: true
  })
}

const getCategoryName = (slug) => {
  const category = props.categories.find(cat => cat.slug === slug)
  return category ? category.name : slug
}

const getPlatformName = (slug) => {
  const platforms = {
    pc: 'PC',
    playstation: 'PlayStation',
    xbox: 'Xbox',
    nintendo: 'Nintendo Switch',
    mobile: 'Mobile'
  }
  return platforms[slug] || slug
}

const getPageUrl = (page) => {
  const params = { ...props.filters, page }
  return route('articles.index', params)
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
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

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
