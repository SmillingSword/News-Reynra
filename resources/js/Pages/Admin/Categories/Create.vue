<template>
  <ModernAdminLayout page-title="Create Category">
    <div class="max-w-4xl mx-auto space-y-8">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create New Category</h1>
          <p class="text-gray-600 dark:text-gray-400 mt-1">Add a new category to organize your content</p>
        </div>
        <Link 
          :href="route('admin.categories.index')" 
          class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition-colors duration-200"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Back to Categories
        </Link>
      </div>

      <!-- Form -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <form @submit.prevent="submit" class="p-8 space-y-8">
          <!-- Basic Information -->
          <div class="space-y-6">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Basic Information</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Essential details about the category</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <!-- Name -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Category Name *
                </label>
                <input
                  id="name"
                  type="text"
                  v-model="form.name"
                  required
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                  placeholder="Enter category name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
              </div>

              <!-- Color -->
              <div>
                <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Category Color *
                </label>
                <div class="flex items-center space-x-3">
                  <input
                    id="color"
                    type="color"
                    v-model="form.color"
                    required
                    class="w-16 h-12 border border-gray-300 dark:border-gray-600 rounded-xl cursor-pointer"
                  />
                  <input
                    type="text"
                    v-model="form.color"
                    class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                    placeholder="#3B82F6"
                  />
                </div>
                <InputError class="mt-2" :message="form.errors.color" />
              </div>
            </div>

            <!-- Description -->
            <div>
              <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Description
              </label>
              <textarea
                id="description"
                v-model="form.description"
                rows="4"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                placeholder="Enter category description"
              ></textarea>
              <InputError class="mt-2" :message="form.errors.description" />
            </div>
          </div>

          <!-- Organization -->
          <div class="space-y-6">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Organization</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Category hierarchy and ordering</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <!-- Parent Category -->
              <div>
                <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Parent Category
                </label>
                <select
                  id="parent_id"
                  v-model="form.parent_id"
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                >
                  <option value="">No Parent (Top Level)</option>
                  <option v-for="parent in parentCategories" :key="parent.id" :value="parent.id">
                    {{ parent.name }}
                  </option>
                </select>
                <InputError class="mt-2" :message="form.errors.parent_id" />
              </div>

              <!-- Sort Order -->
              <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Sort Order
                </label>
                <input
                  id="sort_order"
                  type="number"
                  v-model="form.sort_order"
                  min="0"
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                  placeholder="0"
                />
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Lower numbers appear first</p>
                <InputError class="mt-2" :message="form.errors.sort_order" />
              </div>
            </div>

            <!-- Icon -->
            <div>
              <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Icon (Optional)
              </label>
              <input
                id="icon"
                type="text"
                v-model="form.icon"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                placeholder="e.g., fas fa-tag, heroicon-tag, etc."
              />
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">CSS class for icon (Font Awesome, Heroicons, etc.)</p>
              <InputError class="mt-2" :message="form.errors.icon" />
            </div>
          </div>

          <!-- SEO Settings -->
          <div class="space-y-6">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">SEO Settings</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Search engine optimization</p>
            </div>

            <div class="grid grid-cols-1 gap-6">
              <!-- Meta Title -->
              <div>
                <label for="meta_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Meta Title
                </label>
                <input
                  id="meta_title"
                  type="text"
                  v-model="form.meta_title"
                  maxlength="60"
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                  placeholder="SEO title for search engines"
                />
                <div class="flex justify-between items-center mt-1">
                  <p class="text-xs text-gray-500 dark:text-gray-400">Recommended: 50-60 characters</p>
                  <span class="text-xs text-gray-500 dark:text-gray-400">{{ form.meta_title?.length || 0 }}/60</span>
                </div>
                <InputError class="mt-2" :message="form.errors.meta_title" />
              </div>

              <!-- Meta Description -->
              <div>
                <label for="meta_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Meta Description
                </label>
                <textarea
                  id="meta_description"
                  v-model="form.meta_description"
                  maxlength="160"
                  rows="3"
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                  placeholder="Brief description for search engine results"
                ></textarea>
                <div class="flex justify-between items-center mt-1">
                  <p class="text-xs text-gray-500 dark:text-gray-400">Recommended: 150-160 characters</p>
                  <span class="text-xs text-gray-500 dark:text-gray-400">{{ form.meta_description?.length || 0 }}/160</span>
                </div>
                <InputError class="mt-2" :message="form.errors.meta_description" />
              </div>
            </div>
          </div>

          <!-- Status -->
          <div class="space-y-6">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Status</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Category visibility and availability</p>
            </div>

            <div class="flex items-center space-x-3">
              <input
                id="is_active"
                type="checkbox"
                v-model="form.is_active"
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
              />
              <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                Active Category
              </label>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Inactive categories won't be visible to users</p>
            <InputError class="mt-2" :message="form.errors.is_active" />
          </div>

          <!-- Preview -->
          <div v-if="form.name || form.color" class="space-y-6">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Preview</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">How your category will appear</p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6">
              <div class="flex items-center space-x-4">
                <div 
                  class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold shadow-sm"
                  :style="{ backgroundColor: form.color || '#3B82F6' }"
                >
                  {{ (form.name || 'C').charAt(0).toUpperCase() }}
                </div>
                <div>
                  <h4 class="font-semibold text-gray-900 dark:text-white">{{ form.name || 'Category Name' }}</h4>
                  <p class="text-sm text-gray-600 dark:text-gray-400">{{ form.description || 'No description provided' }}</p>
                </div>
                <span :class="form.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'" class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium">
                  <div class="w-2 h-2 rounded-full mr-1" :class="form.is_active ? 'bg-green-500' : 'bg-gray-400'"></div>
                  {{ form.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
            <Link 
              :href="route('admin.categories.index')" 
              class="px-6 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition-colors duration-200"
            >
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-3 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
            >
              <span v-if="form.processing" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Creating...
              </span>
              <span v-else>Create Category</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </ModernAdminLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import ModernAdminLayout from '@/Layouts/ModernAdminLayout.vue'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
  parentCategories: {
    type: Array,
    default: () => []
  }
})

const form = useForm({
  name: '',
  description: '',
  color: '#3B82F6',
  icon: '',
  parent_id: '',
  sort_order: 0,
  is_active: true,
  meta_title: '',
  meta_description: ''
})

const submit = () => {
  form.post(route('admin.categories.store'))
}
</script>

<style scoped>
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
