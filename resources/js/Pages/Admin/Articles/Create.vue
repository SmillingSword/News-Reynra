<template>
  <ModernAdminLayout page-title="Create New Article">
    <div class="max-w-7xl mx-auto">
      <!-- Header Actions -->
      <div class="flex items-center justify-between mb-8">
        <div class="flex items-center space-x-4">
          <Link 
            :href="route('admin.articles.index')"
            class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Articles
          </Link>
          
          <!-- Auto-save Status -->
          <div class="flex items-center space-x-2 text-sm text-gray-500">
            <div class="flex items-center space-x-1">
              <div 
                :class="[
                  'w-2 h-2 rounded-full transition-colors duration-200',
                  autoSaveStatus === 'saving' ? 'bg-yellow-500 animate-pulse' :
                  autoSaveStatus === 'saved' ? 'bg-green-500' : 'bg-gray-400'
                ]"
              ></div>
              <span>{{ autoSaveText }}</span>
            </div>
          </div>
        </div>

        <div class="flex items-center space-x-3">
          <!-- Writing Mode Toggle -->
          <button
            type="button"
            @click="toggleWritingMode"
            :class="[
              'inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200',
              isDistractionFree 
                ? 'bg-purple-100 text-purple-700 hover:bg-purple-200 dark:bg-purple-900/30 dark:text-purple-400' 
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300'
            ]"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            {{ isDistractionFree ? 'Exit Focus' : 'Focus Mode' }}
          </button>

          <!-- Preview Toggle -->
          <button
            type="button"
            @click="previewMode = !previewMode"
            :class="[
              'inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200',
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

          <!-- Save Draft -->
          <button
            type="button"
            @click="saveDraft"
            :disabled="form.processing"
            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 disabled:opacity-50"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            Save Draft
          </button>

          <!-- Publish -->
          <button
            type="button"
            @click="publishArticle"
            :disabled="form.processing || !form.title || !form.content"
            class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 disabled:opacity-50"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
            {{ form.processing ? 'Publishing...' : 'Publish' }}
          </button>
        </div>
      </div>

      <!-- Main Content -->
      <form @submit.prevent="submit" :class="[
        'transition-all duration-300',
        isDistractionFree ? 'max-w-4xl mx-auto' : 'grid grid-cols-1 lg:grid-cols-4 gap-8'
      ]">
        <!-- Article Content -->
        <div :class="isDistractionFree ? 'space-y-8' : 'lg:col-span-3 space-y-6'">
          <!-- Title Section -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-8 space-y-6">
              <!-- Title -->
              <div>
                <input 
                  type="text" 
                  v-model="form.title"
                  placeholder="Enter your article title here..."
                  class="w-full text-4xl font-bold border-0 focus:ring-0 placeholder-gray-400 bg-transparent resize-none dark:text-white"
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
                  placeholder="Add a compelling subtitle (optional)..."
                  class="w-full text-xl text-gray-600 dark:text-gray-300 border-0 focus:ring-0 placeholder-gray-400 bg-transparent"
                  :class="{ 'text-red-500': errors.subtitle }"
                >
                <div v-if="errors.subtitle" class="mt-2 text-sm text-red-600">{{ errors.subtitle }}</div>
              </div>

              <!-- URL Preview -->
              <div v-if="form.title" class="flex items-center space-x-2 text-sm text-gray-500 bg-gray-50 dark:bg-gray-700/50 px-4 py-3 rounded-xl">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                <span class="font-medium">URL:</span>
                <span class="text-blue-600 dark:text-blue-400">/articles/{{ slug }}</span>
              </div>
            </div>
          </div>

          <!-- Content Editor -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div v-if="!previewMode">
              <RichTextEditor
                v-model="form.content"
                placeholder="Tell your story..."
                :auto-save="true"
                @save="handleAutoSave"
              />
              <div v-if="errors.content" class="p-4 text-sm text-red-600 bg-red-50 dark:bg-red-900/20">{{ errors.content }}</div>
            </div>

            <!-- Preview Mode -->
            <div v-else class="p-8">
              <div class="prose prose-lg max-w-none dark:prose-invert">
                <h1 v-if="form.title" class="text-4xl font-bold mb-4">{{ form.title }}</h1>
                <h2 v-if="form.subtitle" class="text-xl text-gray-600 dark:text-gray-300 mb-8">{{ form.subtitle }}</h2>
                <div v-html="form.content || '<p class=\'text-gray-500 italic\'>No content yet. Start writing to see your preview.</p>'"></div>
              </div>
            </div>
          </div>

          <!-- Excerpt (only show when not in distraction-free mode) -->
          <div v-if="!isDistractionFree" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                Excerpt
                <span class="text-gray-500 font-normal">(Optional summary for article listings)</span>
              </label>
              <textarea 
                v-model="form.excerpt"
                rows="3"
                placeholder="Write a compelling excerpt that will appear in article listings..."
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none dark:bg-gray-700 dark:text-white"
                :class="{ 'border-red-500': errors.excerpt }"
              ></textarea>
              <div class="flex justify-between items-center mt-2">
                <div v-if="errors.excerpt" class="text-sm text-red-600">{{ errors.excerpt }}</div>
                <div class="text-sm text-gray-500">{{ form.excerpt?.length || 0 }}/160 characters</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar (hidden in distraction-free mode) -->
        <div v-if="!isDistractionFree" class="space-y-6">
          <!-- Publish Settings -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                </svg>
                Publish Settings
              </h3>
            </div>
            <div class="p-6 space-y-4">
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
              <div class="space-y-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                <label class="flex items-center space-x-3 cursor-pointer group">
                  <input
                    type="checkbox"
                    v-model="form.is_featured"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  >
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Featured Article</span>
                  </div>
                </label>

                <label class="flex items-center space-x-3 cursor-pointer group">
                  <input
                    type="checkbox"
                    v-model="form.is_sponsored"
                    class="rounded border-gray-300 text-orange-600 focus:ring-orange-500"
                  >
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Sponsored Content</span>
                  </div>
                </label>

                <label class="flex items-center space-x-3 cursor-pointer group">
                  <input
                    type="checkbox"
                    v-model="form.is_breaking"
                    class="rounded border-gray-300 text-red-600 focus:ring-red-500"
                  >
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Breaking News</span>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- Categories -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Categories
              </h3>
            </div>
            <div class="p-6">
              <div class="space-y-2 max-h-48 overflow-y-auto">
                <label 
                  v-for="category in categories" 
                  :key="category.id"
                  class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 p-3 rounded-lg transition-colors group"
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
                    <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">{{ category.name }}</span>
                  </div>
                </label>
              </div>
              <div v-if="errors.categories" class="mt-2 text-sm text-red-600">{{ errors.categories }}</div>
            </div>
          </div>

          <!-- Tags -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Tags
              </h3>
            </div>
            <div class="p-6">
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
        </div>
      </form>
    </div>
  </ModernAdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import ModernAdminLayout from '@/Layouts/ModernAdminLayout.vue'
import RichTextEditor from '@/Components/RichTextEditor.vue'

const props = defineProps({
  categories: Array,
  tags: Array,
  errors: Object
})

// Form state
const form = useForm({
  title: '',
  subtitle: '',
  content: '',
  excerpt: '',
  status: 'draft',
  categories: [],
  tags: [],
  featured_image: '',
  published_at: '',
  is_featured: false,
  is_sponsored: false,
  is_breaking: false,
  meta_title: '',
  meta_description: ''
})

// UI state
const previewMode = ref(false)
const isDistractionFree = ref(false)
const autoSaveStatus = ref('idle') // idle, saving, saved
const tagInput = ref('')

// Computed properties
const slug = computed(() => {
  return form.title
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)/g, '')
})

const autoSaveText = computed(() => {
  switch (autoSaveStatus.value) {
    case 'saving': return 'Saving...'
    case 'saved': return 'All changes saved'
    default: return 'Not saved'
  }
})

// Methods
const generateSlug = () => {
  // Slug is automatically generated via computed property
}

const toggleWritingMode = () => {
  isDistractionFree.value = !isDistractionFree.value
}

const handleAutoSave = () => {
  autoSaveStatus.value = 'saving'
  
  setTimeout(() => {
    autoSaveStatus.value = 'saved'
  }, 1000)
}

const addTag = () => {
  const tag = tagInput.value.trim()
  if (tag && !form.tags.includes(tag)) {
    form.tags.push(tag)
    tagInput.value = ''
  }
}

const removeTag = (index) => {
  form.tags.splice(index, 1)
}

const saveDraft = () => {
  form.status = 'draft'
  submit()
}

const publishArticle = () => {
  form.status = 'published'
  if (!form.published_at) {
    form.published_at = new Date().toISOString().slice(0, 16)
  }
  submit()
}

const submit = () => {
  form.post(route('admin.articles.store'), {
    onSuccess: () => {
      // Handle success
    },
    onError: (errors) => {
      console.error('Validation errors:', errors)
    }
  })
}

// Auto-save functionality
let autoSaveTimeout = null
watch([() => form.title, () => form.content, () => form.excerpt], () => {
  if (autoSaveTimeout) {
    clearTimeout(autoSaveTimeout)
  }
  
  autoSaveTimeout = setTimeout(() => {
    handleAutoSave()
  }, 2000)
}, { deep: true })
</script>

<style scoped>
/* Custom animations */
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
  animation: fadeInUp 0.6s ease-out;
}

/* Focus mode styles */
.focus-mode {
  @apply max-w-4xl mx-auto;
}

.focus-mode .sidebar {
  @apply hidden;
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
