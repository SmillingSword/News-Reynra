<template>
  <Head title="General Settings" />

  <ModernAdminLayout page-title="General Settings">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">General Settings</h2>
          <p class="text-gray-600">Configure your application settings and preferences</p>
        </div>
        
        <button
          @click="saveSettings"
          class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          Save Changes
        </button>
      </div>

      <!-- Settings Tabs -->
      <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="border-b border-gray-200">
          <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <button
              v-for="tab in tabs"
              :key="tab.key"
              @click="activeTab = tab.key"
              :class="[
                'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                activeTab === tab.key
                  ? 'border-blue-500 text-blue-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              ]"
            >
              {{ tab.label }}
            </button>
          </nav>
        </div>

        <div class="p-6">
          <!-- General Tab -->
          <div v-if="activeTab === 'general'" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Site Information -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900">Site Information</h3>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                  <input
                    v-model="settings.site_name"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="News Reynra"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Site Description</label>
                  <textarea
                    v-model="settings.site_description"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Your gaming news destination"
                  ></textarea>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Site URL</label>
                  <input
                    v-model="settings.site_url"
                    type="url"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="https://newsreynra.com"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Admin Email</label>
                  <input
                    v-model="settings.admin_email"
                    type="email"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="admin@newsreynra.com"
                  >
                </div>
              </div>

              <!-- Content Settings -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900">Content Settings</h3>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Articles per Page</label>
                  <select
                    v-model="settings.articles_per_page"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="50">50</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Default Article Status</label>
                  <select
                    v-model="settings.default_article_status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="draft">Draft</option>
                    <option value="pending_review">Pending Review</option>
                    <option value="published">Published</option>
                  </select>
                </div>

                <div class="flex items-center">
                  <input
                    v-model="settings.allow_comments"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <label class="ml-2 block text-sm text-gray-700">Allow Comments on Articles</label>
                </div>

                <div class="flex items-center">
                  <input
                    v-model="settings.moderate_comments"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <label class="ml-2 block text-sm text-gray-700">Moderate Comments Before Publishing</label>
                </div>
              </div>
            </div>
          </div>

          <!-- SEO Tab -->
          <div v-if="activeTab === 'seo'" class="space-y-6">
            <h3 class="text-lg font-semibold text-gray-900">SEO Settings</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                  <input
                    v-model="settings.meta_title"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="News Reynra - Gaming News"
                  >
                  <p class="text-xs text-gray-500 mt-1">Recommended: 50-60 characters</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                  <textarea
                    v-model="settings.meta_description"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Your ultimate destination for gaming news, reviews, and updates"
                  ></textarea>
                  <p class="text-xs text-gray-500 mt-1">Recommended: 150-160 characters</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                  <input
                    v-model="settings.meta_keywords"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="gaming, news, reviews, esports"
                  >
                </div>
              </div>

              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Google Analytics ID</label>
                  <input
                    v-model="settings.google_analytics_id"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="GA-XXXXXXXXX-X"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Google Search Console</label>
                  <input
                    v-model="settings.google_search_console"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Verification code"
                  >
                </div>

                <div class="flex items-center">
                  <input
                    v-model="settings.enable_sitemap"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <label class="ml-2 block text-sm text-gray-700">Enable XML Sitemap</label>
                </div>

                <div class="flex items-center">
                  <input
                    v-model="settings.enable_robots_txt"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <label class="ml-2 block text-sm text-gray-700">Enable Robots.txt</label>
                </div>
              </div>
            </div>
          </div>

          <!-- Social Media Tab -->
          <div v-if="activeTab === 'social'" class="space-y-6">
            <h3 class="text-lg font-semibold text-gray-900">Social Media Settings</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Facebook Page URL</label>
                  <input
                    v-model="settings.facebook_url"
                    type="url"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="https://facebook.com/newsreynra"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Twitter Handle</label>
                  <input
                    v-model="settings.twitter_url"
                    type="url"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="https://twitter.com/newsreynra"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Instagram URL</label>
                  <input
                    v-model="settings.instagram_url"
                    type="url"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="https://instagram.com/newsreynra"
                  >
                </div>
              </div>

              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">YouTube Channel</label>
                  <input
                    v-model="settings.youtube_url"
                    type="url"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="https://youtube.com/c/newsreynra"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Discord Server</label>
                  <input
                    v-model="settings.discord_url"
                    type="url"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="https://discord.gg/newsreynra"
                  >
                </div>

                <div class="flex items-center">
                  <input
                    v-model="settings.enable_social_sharing"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <label class="ml-2 block text-sm text-gray-700">Enable Social Sharing Buttons</label>
                </div>
              </div>
            </div>
          </div>

          <!-- Email Tab -->
          <div v-if="activeTab === 'email'" class="space-y-6">
            <h3 class="text-lg font-semibold text-gray-900">Email Settings</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                  <input
                    v-model="settings.smtp_host"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="smtp.gmail.com"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                  <input
                    v-model="settings.smtp_port"
                    type="number"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="587"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Username</label>
                  <input
                    v-model="settings.smtp_username"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="your-email@gmail.com"
                  >
                </div>
              </div>

              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">From Name</label>
                  <input
                    v-model="settings.mail_from_name"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="News Reynra"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">From Email</label>
                  <input
                    v-model="settings.mail_from_email"
                    type="email"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="noreply@newsreynra.com"
                  >
                </div>

                <div class="flex items-center">
                  <input
                    v-model="settings.enable_email_notifications"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <label class="ml-2 block text-sm text-gray-700">Enable Email Notifications</label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </ModernAdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, reactive } from 'vue'
import ModernAdminLayout from '@/Layouts/ModernAdminLayout.vue'

const activeTab = ref('general')

const tabs = [
  { key: 'general', label: 'General' },
  { key: 'seo', label: 'SEO' },
  { key: 'social', label: 'Social Media' },
  { key: 'email', label: 'Email' }
]

// Sample settings data - in real app this would come from props
const settings = reactive({
  // General
  site_name: 'News Reynra',
  site_description: 'Your ultimate destination for gaming news, reviews, and updates',
  site_url: 'https://newsreynra.com',
  admin_email: 'admin@newsreynra.com',
  articles_per_page: '20',
  default_article_status: 'draft',
  allow_comments: true,
  moderate_comments: true,

  // SEO
  meta_title: 'News Reynra - Gaming News',
  meta_description: 'Your ultimate destination for gaming news, reviews, and updates',
  meta_keywords: 'gaming, news, reviews, esports',
  google_analytics_id: '',
  google_search_console: '',
  enable_sitemap: true,
  enable_robots_txt: true,

  // Social Media
  facebook_url: '',
  twitter_url: '',
  instagram_url: '',
  youtube_url: '',
  discord_url: '',
  enable_social_sharing: true,

  // Email
  smtp_host: '',
  smtp_port: '587',
  smtp_username: '',
  mail_from_name: 'News Reynra',
  mail_from_email: 'noreply@newsreynra.com',
  enable_email_notifications: true
})

const saveSettings = () => {
  alert('Settings saved successfully! (This would save to backend in real implementation)')
}
</script>
