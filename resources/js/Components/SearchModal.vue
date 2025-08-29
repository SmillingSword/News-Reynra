<template>
  <div class="fixed inset-0 z-50 overflow-y-auto" @click="$emit('close')">
    <div class="flex min-h-screen items-start justify-center px-4 pt-16">
      <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
      
      <div 
        class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-xl shadow-2xl"
        @click.stop
      >
        <!-- Search Input -->
        <div class="flex items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input
            ref="searchInput"
            v-model="query"
            type="text"
            placeholder="Cari berita, review, game..."
            class="flex-1 bg-transparent border-0 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-0 focus:outline-none text-lg"
            @keydown.escape="$emit('close')"
            @keydown.enter="performSearch"
          />
          <button 
            @click="$emit('close')"
            class="ml-3 p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Search Results -->
        <div class="max-h-96 overflow-y-auto">
          <div v-if="loading" class="px-6 py-8 text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Mencari...</p>
          </div>

          <div v-else-if="query && results.length === 0" class="px-6 py-8 text-center">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.007-5.824-2.562M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400">Tidak ada hasil untuk "<span class="font-medium">{{ query }}</span>"</p>
          </div>

          <div v-else-if="results.length > 0" class="py-2">
            <div class="px-6 py-2">
              <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ results.length }} hasil ditemukan
              </p>
            </div>
            
            <div class="space-y-1">
              <Link
                v-for="result in results"
                :key="result.id"
                :href="route('articles.show', result.slug)"
                class="flex items-start px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                @click="$emit('close')"
              >
                <div class="flex-shrink-0 w-16 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg mr-4">
                  <img 
                    v-if="result.featured_image"
                    :src="result.featured_image"
                    :alt="result.title"
                    class="w-full h-full object-cover rounded-lg"
                  />
                </div>
                <div class="flex-1 min-w-0">
                  <h3 class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2">
                    {{ result.title }}
                  </h3>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ result.category?.name }} • {{ formatDate(result.published_at) }}
                  </p>
                </div>
              </Link>
            </div>

            <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
              <Link
                :href="route('search', { q: query })"
                class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium"
                @click="$emit('close')"
              >
                Lihat semua hasil untuk "{{ query }}" →
              </Link>
            </div>
          </div>

          <!-- Quick Links when no query -->
          <div v-else class="py-4">
            <div class="px-6 py-2">
              <p class="text-sm font-medium text-gray-900 dark:text-white mb-3">Quick Links</p>
            </div>
            <div class="space-y-1">
              <Link
                :href="route('articles.index', { category: 'review' })"
                class="flex items-center px-6 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                @click="$emit('close')"
              >
                <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <span class="text-sm text-gray-700 dark:text-gray-300">Review Game</span>
              </Link>
              <Link
                :href="route('articles.index', { category: 'esports' })"
                class="flex items-center px-6 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                @click="$emit('close')"
              >
                <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span class="text-sm text-gray-700 dark:text-gray-300">Esports</span>
              </Link>
              <Link
                :href="route('articles.index', { platform: 'pc' })"
                class="flex items-center px-6 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                @click="$emit('close')"
              >
                <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h2a2 2 0 002-2z"/>
                </svg>
                <span class="text-sm text-gray-700 dark:text-gray-300">PC Gaming</span>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { debounce } from 'lodash'

const emit = defineEmits(['close'])

const searchInput = ref(null)
const query = ref('')
const results = ref([])
const loading = ref(false)

// Focus input when modal opens
onMounted(() => {
  nextTick(() => {
    searchInput.value?.focus()
  })
})

// Debounced search function
const debouncedSearch = debounce(async (searchQuery) => {
  if (!searchQuery.trim()) {
    results.value = []
    loading.value = false
    return
  }

  loading.value = true
  
  try {
    // Mock search results - replace with actual API call
    await new Promise(resolve => setTimeout(resolve, 300)) // Simulate API delay
    
    results.value = [
      {
        id: 1,
        title: 'Review: Cyberpunk 2077 Phantom Liberty - Ekspansi yang Menawan',
        slug: 'review-cyberpunk-2077-phantom-liberty',
        category: { name: 'Review' },
        published_at: '2024-01-15T10:00:00Z',
        featured_image: null
      },
      {
        id: 2,
        title: 'Esports: Team Indonesia Juara di Mobile Legends World Championship',
        slug: 'team-indonesia-juara-mlbb-world-championship',
        category: { name: 'Esports' },
        published_at: '2024-01-14T15:30:00Z',
        featured_image: null
      }
    ].filter(item => 
      item.title.toLowerCase().includes(searchQuery.toLowerCase())
    )
  } catch (error) {
    console.error('Search error:', error)
    results.value = []
  } finally {
    loading.value = false
  }
}, 300)

// Watch query changes
watch(query, (newQuery) => {
  debouncedSearch(newQuery)
})

// Perform search on enter
const performSearch = () => {
  if (query.value.trim()) {
    window.location.href = route('search', { q: query.value })
  }
}

// Format date helper
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
</style>
