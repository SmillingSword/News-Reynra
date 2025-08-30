
<template>
  <Head>
    <title>News Reynra - Hub Berita Gaming Terpercaya</title>
    <meta name="description" content="Hub berita gaming terpercaya dengan kurasi berkualitas, pencarian cerdas, dan personalisasi untuk komunitas gamer Indonesia." />
  </Head>

  <PublicLayout>
    <!-- Breaking News Banner -->
    <section class="bg-red-600 text-white py-2 fade-in-up">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center">
          <span class="bg-white text-red-600 px-3 py-1 rounded text-sm font-bold mr-4 animate-pulse">BREAKING</span>
          <div class="flex-1 overflow-hidden">
            <div class="animate-marquee whitespace-nowrap">
              <span class="text-sm font-medium">
                🎮 Update terbaru dari dunia gaming • Turnamen esports internasional dimulai • Review game AAA terbaru sudah tersedia
              </span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Hero Section -->
    <section class="bg-white dark:bg-gray-900 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Brand -->
        <div class="text-center mb-8 fade-in-up">
          <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-2">
            News Reynra
          </h1>
          <p class="text-gray-600 dark:text-gray-400 text-lg">
            Portal Berita Gaming Terpercaya Indonesia
          </p>
          <div class="flex items-center justify-center mt-4 space-x-4 text-sm text-gray-500 dark:text-gray-400">
            <span>{{ new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
            <span>•</span>
            <span>Jakarta, Indonesia</span>
          </div>
        </div>

        <!-- Quick Navigation -->
        <div class="flex flex-wrap justify-center gap-2 mb-8 fade-in-up delay-200">
          <Link :href="route('articles.index')" class="px-4 py-2 bg-blue-600 text-white rounded-full text-sm font-medium hover:bg-blue-700 transition-colors">
            Semua Berita
          </Link>
          <Link :href="route('articles.index', { category: 'review' })" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            Review
          </Link>
          <Link :href="route('articles.index', { category: 'esports' })" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            Esports
          </Link>
          <Link :href="route('articles.index', { category: 'guide' })" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            Guide
          </Link>
        </div>
      </div>
    </section>

    <!-- Featured Articles -->
    <section class="py-16 bg-white dark:bg-gray-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8 fade-in-up">
          <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Berita Utama</h2>
          <Link 
            :href="route('articles.index')" 
            class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium interactive"
          >
            Lihat Semua →
          </Link>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Featured Article -->
          <div class="lg:col-span-2 fade-in-left">
            <article v-if="featuredArticles[0]" class="group cursor-pointer card-hover-intense">
              <Link :href="route('articles.show', featuredArticles[0].slug)">
                <div class="relative overflow-hidden rounded-2xl bg-gray-200 dark:bg-gray-700 aspect-video mb-4 shadow-2xl">
                  <img 
                    v-if="featuredArticles[0].featured_image"
                    :src="featuredArticles[0].featured_image" 
                    :alt="featuredArticles[0].title"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                  <div class="absolute bottom-4 left-4 right-4">
                    <span class="inline-block gradient-gaming text-white px-3 py-1 rounded-full text-sm font-medium mb-2 pulse-glow">
                      {{ featuredArticles[0].category?.name }}
                    </span>
                    <h3 class="text-white text-2xl font-bold mb-2 line-clamp-2 text-glow">
                      {{ featuredArticles[0].title }}
                    </h3>
                    <p class="text-gray-200 text-sm">
                      {{ formatDate(featuredArticles[0].published_at) }} • {{ featuredArticles[0].reading_time }} min read
                    </p>
                  </div>
                </div>
              </Link>
            </article>
          </div>

          <!-- Side Articles -->
          <div class="space-y-6 fade-in-right">
            <article 
              v-for="(article, index) in featuredArticles.slice(1, 4)" 
              :key="article.id"
              class="group cursor-pointer card-hover"
              :class="`delay-${(index + 1) * 100}`"
            >
              <Link :href="route('articles.show', article.slug)" class="flex gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 glass-dark">
                <div class="flex-shrink-0 w-24 h-18 bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
                  <img 
                    v-if="article.featured_image"
                    :src="article.featured_image" 
                    :alt="article.title"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                  />
                </div>
                <div class="flex-1 min-w-0">
                  <span class="inline-block bg-gradient-to-r from-blue-500 to-purple-500 text-white px-2 py-1 rounded text-xs font-medium mb-1">
                    {{ article.category?.name }}
                  </span>
                  <h3 class="text-gray-900 dark:text-white font-semibold text-sm line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                    {{ article.title }}
                  </h3>
                  <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                    {{ formatDate(article.published_at) }}
                  </p>
                </div>
              </Link>
            </article>
          </div>
        </div>
      </div>
    </section>

    <!-- Trending Section -->
    <section class="py-16 bg-gray-50 dark:bg-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8 fade-in-up">
          <h2 class="text-3xl font-bold text-gray-900 dark:text-white">🔥 Trending 24 Jam</h2>
          <Link 
            :href="route('articles.index', { sort: 'trending' })" 
            class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium interactive"
          >
            Lihat Semua →
          </Link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <article 
            v-for="(article, index) in trendingArticles" 
            :key="article.id"
            class="group cursor-pointer card-hover fade-in-up"
            :class="`delay-${(index + 1) * 100}`"
          >
            <Link :href="route('articles.show', article.slug)">
              <div class="relative overflow-hidden rounded-xl bg-gray-200 dark:bg-gray-700 aspect-video mb-3 shadow-gaming group-hover:shadow-gaming-hover transition-all duration-300">
                <img 
                  v-if="article.featured_image"
                  :src="article.featured_image" 
                  :alt="article.title"
                  class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                />
                <div class="absolute top-3 left-3">
                  <span class="gradient-gaming-alt text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                    #{{ index + 1 }}
                  </span>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </div>
              <div class="p-4 bg-white dark:bg-gray-700 rounded-b-xl">
                <span class="inline-block gradient-cyber text-white px-2 py-1 rounded text-xs font-medium mb-2">
                  {{ article.category?.name }}
                </span>
                <h3 class="text-gray-900 dark:text-white font-semibold text-sm line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-2">
                  {{ article.title }}
                </h3>
                <p class="text-gray-500 dark:text-gray-400 text-xs flex items-center">
                  <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                  </svg>
                  {{ formatDate(article.published_at) }} • {{ article.views }} views
                </p>
              </div>
            </Link>
          </article>
        </div>
      </div>
    </section>

    <!-- Latest Articles -->
    <section class="py-16 bg-white dark:bg-gray-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8 fade-in-up">
          <h2 class="text-3xl font-bold text-gray-900 dark:text-white">📰 Berita Terbaru</h2>
          <Link 
            :href="route('articles.index')" 
            class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium interactive"
          >
            Lihat Semua →
          </Link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <article 
            v-for="(article, index) in latestArticles" 
            :key="article.id"
            class="group cursor-pointer card-hover-intense fade-in-up"
            :class="`delay-${(index + 1) * 100}`"
          >
            <Link :href="route('articles.show', article.slug)">
              <div class="relative overflow-hidden rounded-xl bg-gray-200 dark:bg-gray-700 aspect-video mb-4 shadow-xl">
                <img 
                  v-if="article.featured_image"
                  :src="article.featured_image" 
                  :alt="article.title"
                  class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </div>
              <div class="p-6 bg-white dark:bg-gray-800 rounded-b-xl shadow-lg">
                <span class="inline-block gradient-gaming text-white px-3 py-1 rounded-full text-sm font-medium mb-3">
                  {{ article.category?.name }}
                </span>
                <h3 class="text-gray-900 dark:text-white font-bold text-lg line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-2">
                  {{ article.title }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2 mb-3">
                  {{ article.excerpt }}
                </p>
                <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm">
                  <div class="w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center mr-2">
                    <span class="text-white text-xs font-bold">{{ article.author?.name?.charAt(0) }}</span>
                  </div>
                  <span>{{ article.author?.name }}</span>
                  <span class="mx-2">•</span>
                  <span>{{ formatDate(article.published_at) }}</span>
                  <span class="mx-2">•</span>
                  <span>{{ article.reading_time }} min read</span>
                </div>
              </div>
            </Link>
          </article>
        </div>
      </div>
    </section>

    <!-- Categories Section -->
    <section class="py-16 bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 dark:from-gray-800 dark:via-gray-800 dark:to-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 fade-in-up">
          <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">🎮 Jelajahi Kategori</h2>
          <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
            Temukan berita gaming sesuai minat Anda dari berbagai kategori yang tersedia
          </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <Link 
            v-for="(category, index) in categories" 
            :key="category.id"
            :href="route('articles.index', { category: category.slug })"
            class="group p-6 bg-white dark:bg-gray-700 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 text-center card-hover-intense fade-in-up"
            :class="`delay-${(index + 1) * 100}`"
          >
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300"
                 :style="{ background: `linear-gradient(135deg, ${category.color || '#667eea'}, ${category.color || '#764ba2'})` }">
              <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="font-bold text-gray-900 dark:text-white mb-2 text-lg">{{ category.name }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ category.articles_count }} artikel</p>
            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
              <div class="h-2 rounded-full gradient-gaming" :style="{ width: Math.min(100, (category.articles_count / 50) * 100) + '%' }"></div>
            </div>
          </Link>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
  featuredArticles: Array,
  trendingArticles: Array,
  latestArticles: Array,
  categories: Array
})

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
