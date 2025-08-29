<template>
  <ModernAdminLayout :page-title="`Edit Article: ${article.title}`">
    <div class="space-y-8">
      <!-- Header Actions -->
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div class="flex items-center space-x-4">
          <Link 
            :href="route('admin.articles.index')"
            class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition-all duration-200"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Articles
          </Link>
          
          <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <span>Auto-save:</span>
            <div class="flex items-center space-x-1">
              <div :class="autoSaveStatus === 'saving' ? 'animate-spin' : ''" class="w-3 h-3 rounded-full" :style="{ backgroundColor: autoSaveColor }"></div>
              <span>{{ autoSaveText }}</span>
            </div>
          </div>
        </div>

        <div class="flex items-center space-x-3">
          <button
            type="button"
            @click="previewMode = !previewMode"
            :class="[
              'inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl transition-colors duration-200',
              previewMode 
                ? 'bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400' 
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300'
            ]"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            {{ previewMode ? 'Edit' : 'Preview' }}
          </button>

          <button
            type="button"
            @click="focusMode = !focusMode"
            :class="[
              'inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl transition-colors duration-200',
              focusMode 
                ? 'bg-purple-100 text-purple-700 hover:bg-purple-200 dark:bg-purple-900/30 dark:text-purple-400' 
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300'
            ]"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            Focus Mode
          </button>

          <button
            type="button"
            @click="saveDraft"
            :disabled="form.processing"
            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-xl transition-colors duration-200 disabled:opacity-50"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            Save Draft
          </button>

          <button
            type="button"
            @click="updateArticle"
            :disabled="form.processing || !form.title || !form.content"
            class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            {{ form.processing ? 'Updating...' : 'Update Article' }}
          </button>
        </div>
      </div>

      <form @submit.prevent="updateArticle" :class="focusMode ? 'max-w-4xl mx-auto' : ''">
        <div :class="focusMode ? 'space-y-6' : 'grid grid-cols-1 lg:grid-cols-4 gap-8'">
          <!-- Main Content Area -->
          <div :class="focusMode ? '' : 'lg:col-span-3'" class="space-y-6">
            <!-- Title Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
              <div class="p-6 space-y-6">
                <!-- Title -->
                <div>
                  <input 
                    type="text" 
                    v-model="form.title"
                    placeholder="Enter article title here..."
                    class="w-full text-3xl font-bold border-0 focus:ring-0 placeholder-gray-400 bg-transparent resize-none dark:text-white"
                    :class="{ 'text-red-500': errors.title }"
                    @input="generateSlug"
                  >
                  <div v-if="errors.title" class="mt-2 text-sm text-red-600">{{ errors.title }}</div>
                </div>

                <!-- Subtitle -->
                <div>
                  <input 
                    type="text" 
                    v-model="form.subtitle"
                    placeholder="Add a subtitle (optional)..."
                    class="w-full text-lg text-gray-600 dark:text-gray-400 border-0 focus:ring-0 placeholder-gray-400 bg-transparent"
                    :class="{ 'text-red-500': errors.subtitle }"
                  >
                  <div v-if="errors.subtitle" class="mt-2 text-sm text-red-600">{{ errors.subtitle }}</div>
                </div>

                <!-- Slug Preview -->
                <div v-if="form.title" class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 px-3 py-2 rounded-lg">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                  </svg>
                  <span>URL: /articles/{{ slug }}</span>
                </div>
              </div>
            </div>

            <!-- Content Editor -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
              <div v-if="!previewMode" class="p-6">
                <RichTextEditor
                  v-model="form.content"
                  placeholder="Start writing your article content..."
                  :auto-save="true"
                  @save="handleAutoSave"
                />
                <div v-if="errors.content" class="mt-2 text-sm text-red-600">{{ errors.content }}</div>
              </div>

              <!-- Preview Mode -->
              <div v-else class="p-6">
                <div class="prose prose-lg max-w-none dark:prose-invert" v-html="form.content"></div>
              </div>
            </div>

            <!-- Excerpt -->
            <div v-if="!focusMode" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
              <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                  Excerpt
                  <span class="text-gray-500 dark:text-gray-400 font-normal">(Optional summary for article listings)</span>
                </label>
                <textarea 
                  v-model="form.excerpt"
                  rows="3"
                  placeholder="Write a brief excerpt that will appear in article listings..."
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none dark:bg-gray-700 dark:text-white"
                  :class="{ 'border-red-500': errors.excerpt }"
                ></textarea>
                <div class="flex justify-between items-center mt-2">
                  <div v-if="errors.excerpt" class="text-sm text-red-600">{{ errors.excerpt }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">{{ form.excerpt?.length || 0 }}/160 characters</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div v-if="!focusMode" class="space-y-6">
            <!-- Publish Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
              <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Publish Settings</h3>
              </div>
              <div class="p-4 space-y-4">
                <!-- Status -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                  <select 
                    v-model="form.status"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    :class="{ 'border-red-500': errors.status }"
                  >
                    <option value="draft">Draft</option>
                    <option value="pending_review">Pending Review</option>
                    <option value="published">Published</option>
                    <option value="scheduled">Scheduled</option>
                  </select>
                  <div v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status }}</div>
                </div>

                <!-- Publish Date -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Publish Date</label>
                  <input 
                    type="datetime-local" 
                    v-model="form.published_at"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    :class="{ 'border-red-500': errors.published_at }"
                  >
                  <div v-if="errors.published_at" class="mt-1 text-sm text-red-600">{{ errors.published_at }}</div>
                </div>

                <!-- Article Options -->
                <div class="space-y-3">
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      type="checkbox"
                      v-model="form.is_featured"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                    <span class="text-sm text-gray-700 dark:text-gray-300">Featured Article</span>
                  </label>

                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      type="checkbox"
                      v-model="form.is_sponsored"
                      class="rounded border-gray-300 text-orange-600 focus:ring-orange-500"
                    >
                    <span class="text-sm text-gray-700 dark:text-gray-300">Sponsored Content</span>
                  </label>

                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input
                      type="checkbox"
                      v-model="form.is_breaking"
                      class="rounded border-gray-300 text-red-600 focus:ring-red-500"
                    >
                    <span class="text-sm text-gray-700 dark:text-gray-300">Breaking News</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Categories -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
              <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Categories</h3>
              </div>
              <div class="p-4">
                <div class="space-y-2 max-h-48 overflow-y-auto">
                  <label 
                    v-for="category in categories" 
                    :key="category.id"
                    class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 p-2 rounded-lg transition-colors"
                  >
                    <input
                      type="checkbox"
                      :value="category.id"
                      v-model="form.categories"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                    <div class="flex items-center space-x-2">
                      <div 
                        class="w-3 h-3 rounded-full" 
                        :style="{ backgroundColor: category.color || '#3B82F6' }"
                      ></div>
                      <span class="text-sm text-gray-700 dark:text-gray-300">{{ category.name }}</span>
                    </div>
                  </label>
                </div>
                <div v-if="errors.categories" class="mt-2 text-sm text-red-600">{{ errors.categories }}</div>
              </div>
            </div>

            <!-- Tags -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
              <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Tags</h3>
              </div>
              <div class="p-4">
                <input
                  type="text"
                  v-model="tagInput"
                  @keydown.enter.prevent="addTag"
                  @keydown.comma.prevent="addTag"
                  placeholder="Type and press Enter to add tags"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                >
                <div v-if="form.tags.length > 0" class="flex flex-wrap gap-2 mt-3">
                  <span 
                    v-for="(tag, index) in form.tags" 
                    :key="index"
                    class="inline-flex items-center px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 text-sm rounded-full"
                  >
                    {{ tag }}
                    <button 
                      type="button"
                      @click="removeTag(index)"
                      class="ml-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                    >
                      ×
                    </button>
                  </span>
                </div>
                <div v-if="errors.tags" class="mt-2 text-sm text-red-600">{{ errors.tags }}</div>
              </div>
            </div>

            <!-- Featured Image -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
              <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Featured Image</h3>
              </div>
              <div class="p-4">
                <div v-if="form.featured_image" class="mb-4">
                  <img 
                    :src="form.featured_image" 
                    alt="Featured image preview"
                    class="w-full h-32 object-cover rounded-lg"
                  >
                </div>
                <input
                  type="url"
                  v-model="form.featured_image"
                  placeholder="https://example.com/image.jpg"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                  :class="{ 'border-red-500': errors.featured_image }"
                >
                <div v-if="errors.featured_image" class="mt-2 text-sm text-red-600">{{ errors.featured_image }}</div>
              </div>
            </div>

            <!-- SEO Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
              <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">SEO Settings</h3>
              </div>
              <div class="p-4 space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meta Title</label>
                  <input
                    type="text"
                    v-model="form.meta_title"
                    placeholder="SEO title for search engines"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                  >
                  <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ form.meta_title?.length || 0 }}/60 characters</div>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meta Description</label>
                  <textarea
                    v-model="form.meta_description"
                    rows="3"
                    placeholder="Brief description for search engines"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none dark:bg-gray-700 dark:text-white"
                  ></textarea>
                  <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ form.meta_description?.length || 0 }}/160 characters</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </ModernAdminLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import ModernAdminLayout from '@/Layouts/ModernAdminLayout.vue'
import RichTextEditor from '@/Components/RichTextEditor.vue'

const props = defineProps({
  article: Object,
  categories: Array,
  errors: Object
})

// State
const previewMode = ref(false)
const focusMode = ref(false)
const tagInput = ref('')
const autoSaveStatus = ref('saved')

// Form
const form = useForm({
  title: props.article.title || '',
  subtitle: props.article.subtitle || '',
  content: props.article.content || '',
  excerpt: props.article.excerpt || '',
  status: props.article.status || 'draft',
  categories: props.article.categories ? props.article.categories.map(cat => cat.id) : [],
  tags: props.article.tags ? props.article.tags.map(tag => tag.name) : [],
  featured_image: props.article.featured_image || '',
  published_at: props.article.published_at ? formatDateTimeLocal(props.article.published_at) : '',
  is_featured: props.article.is_featured || false,
  is_sponsored: props.article.is_sponsored || false,
  is_breaking: props.article.is_breaking || false,
  meta_title: props.article.meta_title || '',
  meta_description: props.article.meta_description || ''
})

// Computed
const slug = computed(() => {
  return form.title
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)/g, '')
})

const autoSaveColor = computed(() => {
  switch (autoSaveStatus.value) {
    case 'saving': return '#f59e0b'
    case 'saved': return '#10b981'
    case 'error': return '#ef4444'
    default: return '#6b7280'
  }
})

const autoSaveText = computed(() => {
  switch (autoSaveStatus.value) {
    case 'saving': return 'Saving...'
    case 'saved': return 'Saved'
    case 'error': return 'Error'
    default: return 'Not saved'
  }
})

// Methods
function formatDateTimeLocal(dateString) {
  if (!dateString) return ''
  const date = new Date(dateString)
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')
  return `${year}-${month}-${day}T${hours}:${minutes}`
}

function generateSlug() {
  // Slug is automatically generated via computed property
}

function addTag() {
  const tag = tagInput.value.trim()
  if (tag && !form.tags.includes(tag)) {
    form.tags.push(tag)
    tagInput.value = ''
  }
}

function removeTag(index) {
  form.tags.splice(index, 1)
}

function handleAutoSave() {
  autoSaveStatus.value = 'saving'
  
  // Simulate auto-save
  setTimeout(() => {
    autoSaveStatus.value = 'saved'
  }, 1000)
}

function saveDraft() {
  const originalStatus = form.status
  form.status = 'draft'
  
  form.put(route('admin.articles.update', props.article.slug), {
    onSuccess: () => {
      autoSaveStatus.value = 'saved'
    },
    onError: () => {
      form.status = originalStatus
      autoSaveStatus.value = 'error'
    }
  })
}

function updateArticle() {
  form.put(route('admin.articles.update', props.article.slug), {
    onSuccess: () => {
      autoSaveStatus.value = 'saved'
    },
    onError: () => {
      autoSaveStatus.value = 'error'
    }
  })
}

// Auto-save on content change
watch(() => form.content, () => {
  if (form.content) {
    handleAutoSave()
  }
}, { debounce: 2000 })
</script>

<style scoped>
.prose {
  max-width: none;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
  color: inherit;
}

.prose p {
  margin-bottom: 1rem;
}

.prose img {
  border-radius: 0.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
</style>
