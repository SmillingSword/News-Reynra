<template>
  <ModernAdminLayout page-title="Articles Management">
    <div class="space-y-8">
      <!-- Header Section -->
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Articles</h1>
          <p class="text-gray-600 dark:text-gray-400 mt-1">Manage and organize your content</p>
        </div>
        
        <div class="flex items-center space-x-4">
          <!-- Quick Stats -->
          <div class="hidden lg:flex items-center space-x-6 text-sm">
            <div class="text-center">
              <div class="font-semibold text-gray-900 dark:text-white">{{ totalArticles }}</div>
              <div class="text-gray-500 dark:text-gray-400">Total</div>
            </div>
            <div class="text-center">
              <div class="font-semibold text-green-600">{{ publishedCount }}</div>
              <div class="text-gray-500 dark:text-gray-400">Published</div>
            </div>
            <div class="text-center">
              <div class="font-semibold text-yellow-600">{{ draftCount }}</div>
              <div class="text-gray-500 dark:text-gray-400">Drafts</div>
            </div>
          </div>
          
          <Link 
            :href="route('admin.articles.create')" 
            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Article
          </Link>
        </div>
      </div>

      <!-- Search and Filters -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col lg:flex-row gap-4">
          <!-- Search -->
          <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
            <input 
              type="text" 
              v-model="search"
              placeholder="Search articles by title, content, or author..."
              class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
            >
          </div>
          
          <!-- Filters -->
          <div class="flex gap-3">
            <select 
              v-model="statusFilter" 
              class="px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white min-w-[140px]"
            >
              <option value="">All Status</option>
              <option value="published">Published</option>
              <option value="draft">Draft</option>
              <option value="pending_review">Pending Review</option>
            </select>
            
            <select 
              v-model="categoryFilter" 
              class="px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white min-w-[140px]"
            >
              <option value="">All Categories</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
            
            <!-- View Toggle -->
            <div class="flex bg-gray-100 dark:bg-gray-700 rounded-xl p-1">
              <button
                @click="viewMode = 'grid'"
                :class="[
                  'px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                  viewMode === 'grid' 
                    ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' 
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                ]"
              >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
              </button>
              <button
                @click="viewMode = 'list'"
                :class="[
                  'px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                  viewMode === 'list' 
                    ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' 
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                ]"
              >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Articles Content -->
      <div v-if="filteredArticles.length === 0" class="text-center py-16">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-12">
          <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/20 dark:to-purple-900/20 rounded-full flex items-center justify-center">
            <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No articles found</h3>
          <p class="text-gray-600 dark:text-gray-400 mb-6">Get started by creating your first article or adjust your search filters.</p>
          <Link 
            :href="route('admin.articles.create')" 
            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create Your First Article
          </Link>
        </div>
      </div>

      <!-- Grid View -->
      <div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="(article, index) in filteredArticles" 
          :key="article.id"
          class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 animate-fade-in-up"
          :style="{ animationDelay: `${index * 0.1}s` }"
        >
          <!-- Article Image -->
          <div class="relative h-48 bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20">
            <img 
              v-if="getArticleImage(article)" 
              :src="getArticleImage(article)" 
              :alt="article.title"
              class="w-full h-full object-cover"
              @error="handleImageError"
            >
            <div v-else class="w-full h-full flex items-center justify-center">
              <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
            
            <!-- Status Badge -->
            <div class="absolute top-4 left-4">
              <span :class="getStatusClass(article.status)" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium backdrop-blur-sm">
                {{ getStatusText(article.status) }}
              </span>
            </div>
            
            <!-- Featured Badge -->
            <div v-if="article.is_featured" class="absolute top-4 right-4">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 backdrop-blur-sm">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                Featured
              </span>
            </div>
          </div>
          
          <!-- Article Content -->
          <div class="p-6">
            <div class="flex items-center space-x-2 mb-3">
              <div class="flex flex-wrap gap-1">
                <span 
                  v-for="category in article.categories?.slice(0, 2)" 
                  :key="category.id"
                  class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium"
                  :style="{ 
                    backgroundColor: category.color + '20', 
                    color: category.color 
                  }"
                >
                  {{ category.name }}
                </span>
                <span v-if="article.categories?.length > 2" class="text-xs text-gray-500 dark:text-gray-400">
                  +{{ article.categories.length - 2 }} more
                </span>
              </div>
            </div>
            
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
              {{ article.title }}
            </h3>
            
            <p v-if="article.subtitle" class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
              {{ article.subtitle }}
            </p>
            
            <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
              <div class="flex items-center space-x-2">
                <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                  <span class="text-xs font-medium text-white">
                    {{ (article.author?.name || 'Unknown').charAt(0).toUpperCase() }}
                  </span>
                </div>
                <span>{{ article.author?.name || 'Unknown' }}</span>
              </div>
              <span>{{ formatDate(article.created_at) }}</span>
            </div>
            
            <!-- Stats -->
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-4 pt-2 border-t border-gray-100 dark:border-gray-700">
              <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  <span>{{ formatNumber(article.views_count || 0) }} views</span>
                </div>
                <div class="flex items-center space-x-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                  </svg>
                  <span>{{ formatNumber(article.comments_count || 0) }} comments</span>
                </div>
                <div class="flex items-center space-x-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                  </svg>
                  <span>{{ formatNumber(article.likes_count || 0) }} likes</span>
                </div>
              </div>
              <div class="text-xs">
                {{ article.reading_time || 1 }} min read
              </div>
            </div>
            
            <!-- Actions -->
            <div class="flex items-center space-x-2">
              <Link 
                :href="route('admin.articles.show', article.slug)" 
                class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors duration-200"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View
              </Link>
              <Link 
                :href="route('admin.articles.edit', article.slug)" 
                class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-400 text-sm font-medium rounded-lg transition-colors duration-200"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
              </Link>
              <button 
                @click="deleteArticle(article.slug)" 
                class="px-3 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 text-sm font-medium rounded-lg transition-colors duration-200"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- List View -->
      <div v-else class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Article
                </th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Author
                </th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Views
                </th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Published
                </th>
                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              <tr 
                v-for="(article, index) in filteredArticles" 
                :key="article.id" 
                class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200 animate-fade-in-up"
                :style="{ animationDelay: `${index * 0.05}s` }"
              >
                <td class="px-6 py-4">
                  <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 w-12 h-12">
                      <img 
                        v-if="getArticleImage(article)" 
                        :src="getArticleImage(article)" 
                        :alt="article.title"
                        class="w-12 h-12 rounded-lg object-cover"
                        @error="handleImageError"
                      >
                      <div v-else class="w-12 h-12 bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/20 dark:to-purple-900/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                      </div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="flex items-center space-x-2 mb-1">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                          {{ article.title }}
                        </h3>
                        <span v-if="article.is_featured" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                          <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                          </svg>
                          Featured
                        </span>
                      </div>
                      <p v-if="article.subtitle" class="text-sm text-gray-500 dark:text-gray-400 truncate">
                        {{ article.subtitle }}
                      </p>
                      <div class="flex flex-wrap gap-1 mt-1">
                        <span 
                          v-for="category in article.categories?.slice(0, 3)" 
                          :key="category.id"
                          class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                          :style="{ 
                            backgroundColor: category.color + '20', 
                            color: category.color 
                          }"
                        >
                          {{ category.name }}
                        </span>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                      <span class="text-xs font-medium text-white">
                        {{ (article.author?.name || 'Unknown').charAt(0).toUpperCase() }}
                      </span>
                    </div>
                    <span class="text-sm text-gray-900 dark:text-white">{{ article.author?.name || 'Unknown' }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusClass(article.status)" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium">
                    {{ getStatusText(article.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  <div class="flex items-center space-x-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span class="font-medium text-gray-900 dark:text-white">{{ formatNumber(article.views_count || 0) }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ article.published_at ? formatDate(article.published_at) : '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-2">
                    <Link 
                      :href="route('admin.articles.show', article.slug)" 
                      class="inline-flex items-center px-3 py-1 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-lg transition-colors duration-200"
                    >
                      View
                    </Link>
                    <Link 
                      :href="route('admin.articles.edit', article.slug)" 
                      class="inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-400 text-xs font-medium rounded-lg transition-colors duration-200"
                    >
                      Edit
                    </Link>
                    <button 
                      @click="deleteArticle(article.slug)" 
                      class="inline-flex items-center px-3 py-1 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 text-xs font-medium rounded-lg transition-colors duration-200"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="articles.links && filteredArticles.length > 0" class="flex items-center justify-between">
        <div class="text-sm text-gray-700 dark:text-gray-300">
          Showing {{ articles.from || 0 }} to {{ articles.to || 0 }} of {{ articles.total || 0 }} results
        </div>
        <nav class="flex items-center space-x-2">
          <template v-for="link in articles.links" :key="link.label">
            <Link 
              v-if="link.url" 
              :href="link.url" 
              :class="[
                'px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200',
                link.active 
                  ? 'bg-blue-600 text-white shadow-lg' 
                  : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'
              ]"
              v-html="link.label"
            />
            <span 
              v-else 
              :class="[
                'px-3 py-2 text-sm font-medium rounded-lg',
                'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed'
              ]"
              v-html="link.label"
            />
          </template>
        </nav>
      </div>
    </div>
  </ModernAdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import ModernAdminLayout from '@/Layouts/ModernAdminLayout.vue'

const props = defineProps({
  articles: {
    type: Object,
    default: () => ({ data: [] })
  },
  categories: {
    type: Array,
    default: () => []
  }
})

// State
const search = ref('')
const statusFilter = ref('')
const categoryFilter = ref('')
const viewMode = ref('grid')

// Computed properties
const filteredArticles = computed(() => {
  let filtered = props.articles.data || []
  
  if (search.value) {
    filtered = filtered.filter(article => 
      article.title.toLowerCase().includes(search.value.toLowerCase()) ||
      article.subtitle?.toLowerCase().includes(search.value.toLowerCase()) ||
      article.author?.name.toLowerCase().includes(search.value.toLowerCase())
    )
  }
  
  if (statusFilter.value) {
    filtered = filtered.filter(article => article.status === statusFilter.value)
  }
  
  if (categoryFilter.value) {
    filtered = filtered.filter(article => 
      article.categories?.some(category => category.id == categoryFilter.value)
    )
  }
  
  return filtered
})

const totalArticles = computed(() => props.articles.data?.length || 0)
const publishedCount = computed(() => props.articles.data?.filter(a => a.status === 'published').length || 0)
const draftCount = computed(() => props.articles.data?.filter(a => a.status === 'draft').length || 0)

// Methods
const getStatusClass = (status) => {
  const classes = {
    'draft': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'published': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    'pending_review': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'
  }
  return classes[status] || classes.draft
}

const getStatusText = (status) => {
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
    month: 'short',
    day: 'numeric'
  })
}

const deleteArticle = (articleSlug) => {
  if (confirm('Are you sure you want to delete this article?')) {
    router.delete(route('admin.articles.destroy', articleSlug))
  }
}

// Helper function to get article image with fallback options
const getArticleImage = (article) => {
  // Try different image sources in order of preference
  return article.card_image_url || 
         article.featured_image_url || 
         article.thumbnail_url || 
         article.featured_image || 
         null
}

// Handle image loading errors
const handleImageError = (event) => {
  // Hide the broken image
  event.target.style.display = 'none'
  // Show the placeholder instead
  const placeholder = event.target.nextElementSibling
  if (placeholder) {
    placeholder.style.display = 'flex'
  }
}

// Format numbers with commas
const formatNumber = (num) => {
  if (num >= 1000000) {
    return (num / 1000000).toFixed(1) + 'M'
  } else if (num >= 1000) {
    return (num / 1000).toFixed(1) + 'K'
  }
  return num.toString()
}
</script>

<style scoped>
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.6s ease-out forwards;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

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
