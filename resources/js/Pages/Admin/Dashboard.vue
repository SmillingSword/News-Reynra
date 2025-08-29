<template>
  <Head title="Admin Dashboard" />

  <ModernAdminLayout page-title="Dashboard">
    <div class="space-y-6">
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Articles Stats -->
        <StatCard
          title="Total Articles"
          :value="stats.articles.total"
          :growth="stats.articles.growth_rate"
          icon="document-text"
          color="blue"
        />

        <!-- Users Stats -->
        <StatCard
          title="Total Users"
          :value="stats.users.total"
          :growth="stats.users.growth_rate"
          icon="users"
          color="green"
        />

        <!-- Comments Stats -->
        <StatCard
          title="Total Comments"
          :value="stats.content.comments"
          icon="chat-alt"
          color="purple"
        />

        <!-- Views Stats -->
        <StatCard
          title="Total Views"
          :value="stats.engagement.total_views"
          icon="eye"
          color="yellow"
        />
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Line Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 transition-colors">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Content Growth</h3>
          <LineChart :data="chartData" />
        </div>

        <!-- Donut Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 transition-colors">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Content Distribution</h3>
          <DonutChart :data="contentDistribution" />
        </div>
      </div>

      <!-- Recent Activity & Popular Content -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Articles -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 transition-colors">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Articles</h3>
            <Link :href="route('admin.articles.index')" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
              View All
            </Link>
          </div>
          
          <div class="space-y-4">
            <div v-for="article in recentArticles" :key="article.id" class="flex items-start space-x-3">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ article.title }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">by {{ article.author.name }}</p>
                <div class="flex items-center space-x-2 mt-1">
                  <span :class="getStatusColor(article.status)" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">
                    {{ article.status }}
                  </span>
                  <span class="text-xs text-gray-400 dark:text-gray-500">{{ formatDate(article.created_at) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Comments -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 transition-colors">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Comments</h3>
            <Link :href="route('admin.comments.index')" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
              View All
            </Link>
          </div>
          
          <div class="space-y-4">
            <div v-for="comment in recentComments" :key="comment.id" class="flex items-start space-x-3">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center">
                  <span class="text-xs font-medium text-white">
                    {{ comment.user.name.charAt(0).toUpperCase() }}
                  </span>
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-900 dark:text-white line-clamp-2">{{ comment.content }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  by {{ comment.user.name }} on {{ comment.article.title }}
                </p>
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ formatDate(comment.created_at) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Popular Categories -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 transition-colors">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Popular Categories</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div v-for="category in popularCategories" :key="category.id" class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:shadow-md dark:hover:bg-gray-700 transition-all">
            <div class="flex items-center space-x-3">
              <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: category.color }"></div>
              <div class="flex-1">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ category.name }}</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ category.articles_count }} articles</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activities -->
      <ActivityFeed :activities="recentActivities" />
    </div>
  </ModernAdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import ModernAdminLayout from '@/Layouts/ModernAdminLayout.vue'
import StatCard from '@/Components/Dashboard/StatCard.vue'
import LineChart from '@/Components/Charts/LineChart.vue'
import DonutChart from '@/Components/Charts/DonutChart.vue'
import ActivityFeed from '@/Components/Dashboard/ActivityFeed.vue'

const props = defineProps({
  stats: Object,
  recentArticles: Array,
  recentComments: Array,
  popularCategories: Array,
  recentActivities: Array,
  chartData: Object,
})

const contentDistribution = {
  labels: ['Published', 'Draft', 'Pending Review'],
  datasets: [{
    data: [
      props.stats.articles.published,
      props.stats.articles.draft,
      props.stats.articles.pending_review
    ],
    backgroundColor: [
      '#10B981',
      '#F59E0B',
      '#EF4444'
    ]
  }]
}

const getStatusColor = (status) => {
  const colors = {
    published: 'bg-green-100 text-green-800',
    draft: 'bg-yellow-100 text-yellow-800',
    pending_review: 'bg-red-100 text-red-800',
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
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
