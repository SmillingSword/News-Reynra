<template>
  <Head :title="`Edit ${game.name}`" />

  <ModernAdminLayout :page-title="`Edit ${game.name}`">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center space-x-4">
          <Link 
            :href="route('admin.games.show', game.slug)"
            class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors duration-200"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </Link>
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit {{ game.name }}</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Update game information and settings</p>
          </div>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-8">
        <!-- Basic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Basic Information</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Game Name *
              </label>
              <input
                v-model="form.name"
                type="text"
                required
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                placeholder="Enter game name"
              />
              <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Description
              </label>
              <textarea
                v-model="form.description"
                rows="4"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                placeholder="Enter game description"
              ></textarea>
              <InputError :message="form.errors.description" class="mt-2" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Genre *
              </label>
              <input
                v-model="form.genre"
                type="text"
                required
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                placeholder="e.g., Action, RPG, Strategy"
              />
              <InputError :message="form.errors.genre" class="mt-2" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Release Date
              </label>
              <input
                v-model="form.release_date"
                type="date"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
              />
              <InputError :message="form.errors.release_date" class="mt-2" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Developer
              </label>
              <input
                v-model="form.developer"
                type="text"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                placeholder="Enter developer name"
              />
              <InputError :message="form.errors.developer" class="mt-2" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Publisher
              </label>
              <input
                v-model="form.publisher"
                type="text"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                placeholder="Enter publisher name"
              />
              <InputError :message="form.errors.publisher" class="mt-2" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Rating (0-10)
              </label>
              <input
                v-model="form.rating"
                type="number"
                min="0"
                max="10"
                step="0.1"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                placeholder="e.g., 8.5"
              />
              <InputError :message="form.errors.rating" class="mt-2" />
            </div>
          </div>
        </div>

        <!-- Platforms -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Platforms *</h3>
          
          <div class="space-y-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <label 
                v-for="platform in availablePlatforms" 
                :key="platform"
                class="flex items-center space-x-3 p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-200"
              >
                <input
                  v-model="form.platforms"
                  type="checkbox"
                  :value="platform"
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ platform }}</span>
              </label>
            </div>
            
            <div class="flex items-center space-x-4">
              <input
                v-model="newPlatform"
                type="text"
                placeholder="Add custom platform"
                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                @keyup.enter="addPlatform"
              />
              <button
                type="button"
                @click="addPlatform"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200"
              >
                Add
              </button>
            </div>
            
            <InputError :message="form.errors.platforms" class="mt-2" />
          </div>
        </div>

        <!-- Media & Links -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Media & Links</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Cover Image URL
              </label>
              <input
                v-model="form.cover_image"
                type="url"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                placeholder="https://example.com/game-cover.jpg"
              />
              <InputError :message="form.errors.cover_image" class="mt-2" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Trailer URL
              </label>
              <input
                v-model="form.trailer_url"
                type="url"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                placeholder="https://youtube.com/watch?v=..."
              />
              <InputError :message="form.errors.trailer_url" class="mt-2" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Official Website
              </label>
              <input
                v-model="form.official_website"
                type="url"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                placeholder="https://game-website.com"
              />
              <InputError :message="form.errors.official_website" class="mt-2" />
            </div>
          </div>
        </div>

        <!-- Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Settings</h3>
          
          <div class="space-y-4">
            <label class="flex items-center space-x-3">
              <input
                v-model="form.is_featured"
                type="checkbox"
                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
              />
              <div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Featured Game</span>
                <p class="text-xs text-gray-500 dark:text-gray-400">Display this game prominently on the site</p>
              </div>
            </label>

            <label class="flex items-center space-x-3">
              <input
                v-model="form.is_active"
                type="checkbox"
                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
              />
              <div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                <p class="text-xs text-gray-500 dark:text-gray-400">Make this game visible to users</p>
              </div>
            </label>
          </div>
        </div>

        <!-- SEO -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">SEO Settings</h3>
          
          <div class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Meta Title
              </label>
              <input
                v-model="form.meta_title"
                type="text"
                maxlength="60"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                placeholder="SEO title for search engines"
              />
              <div class="flex justify-between mt-1">
                <InputError :message="form.errors.meta_title" />
                <span class="text-xs text-gray-500">{{ form.meta_title?.length || 0 }}/60</span>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Meta Description
              </label>
              <textarea
                v-model="form.meta_description"
                rows="3"
                maxlength="160"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                placeholder="SEO description for search engines"
              ></textarea>
              <div class="flex justify-between mt-1">
                <InputError :message="form.errors.meta_description" />
                <span class="text-xs text-gray-500">{{ form.meta_description?.length || 0 }}/160</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Preview -->
        <div v-if="form.name" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Preview</h3>
          
          <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
            <div class="aspect-video bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 rounded-lg mb-4 flex items-center justify-center overflow-hidden">
              <img 
                v-if="form.cover_image" 
                :src="form.cover_image" 
                :alt="form.name"
                class="w-full h-full object-cover"
                @error="$event.target.style.display = 'none'"
              />
              <svg v-else class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.01M15 10h1.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>

            <div class="space-y-3">
              <div class="flex items-start justify-between">
                <h3 class="font-semibold text-gray-900 dark:text-white text-lg">{{ form.name }}</h3>
                <div class="flex items-center space-x-2 ml-2">
                  <span 
                    v-if="form.is_featured"
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400"
                  >
                    Featured
                  </span>
                  <span 
                    :class="[
                      'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                      form.is_active 
                        ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' 
                        : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
                    ]"
                  >
                    {{ form.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </div>
              </div>

              <p v-if="form.description" class="text-gray-600 dark:text-gray-400 text-sm">{{ form.description }}</p>

              <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
                <span v-if="form.genre">{{ form.genre }}</span>
                <span v-if="form.developer">by {{ form.developer }}</span>
                <span v-if="form.rating">{{ form.rating }}/10</span>
              </div>

              <div v-if="form.platforms.length > 0" class="flex flex-wrap gap-1">
                <span 
                  v-for="platform in form.platforms" 
                  :key="platform"
                  class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400"
                >
                  {{ platform }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
          <button
            type="button"
            @click="showDeleteModal = true"
            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200"
          >
            Delete Game
          </button>
          
          <div class="flex items-center space-x-4">
            <Link 
              :href="route('admin.games.show', game.slug)"
              class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
            >
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
            >
              <span v-if="form.processing">Updating...</span>
              <span v-else>Update Game</span>
            </button>
          </div>
        </div>
      </form>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6">
        <div class="flex items-center mb-4">
          <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-full mr-4">
            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delete Game</h3>
        </div>
        
        <p class="text-gray-600 dark:text-gray-400 mb-6">
          Are you sure you want to delete "{{ game.name }}"? This action cannot be undone.
        </p>
        
        <div class="flex items-center justify-end space-x-4">
          <button
            @click="showDeleteModal = false"
            class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200"
          >
            Cancel
          </button>
          <button
            @click="deleteGame"
            :disabled="deleteForm.processing"
            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 disabled:opacity-50"
          >
            <span v-if="deleteForm.processing">Deleting...</span>
            <span v-else>Delete</span>
          </button>
        </div>
      </div>
    </div>
  </ModernAdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import ModernAdminLayout from '@/Layouts/ModernAdminLayout.vue'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
  game: Object,
})

const availablePlatforms = [
  'PC', 'PlayStation 5', 'PlayStation 4', 'Xbox Series X/S', 'Xbox One',
  'Nintendo Switch', 'iOS', 'Android', 'Mac', 'Linux'
]

const newPlatform = ref('')
const showDeleteModal = ref(false)

const form = useForm({
  name: props.game.name,
  description: props.game.description,
  genre: props.game.genre,
  platforms: props.game.platforms || [],
  release_date: props.game.release_date,
  developer: props.game.developer,
  publisher: props.game.publisher,
  rating: props.game.rating,
  cover_image: props.game.cover_image,
  trailer_url: props.game.trailer_url,
  official_website: props.game.official_website,
  is_featured: props.game.is_featured,
  is_active: props.game.is_active,
  meta_title: props.game.meta?.title || '',
  meta_description: props.game.meta?.description || '',
})

const deleteForm = useForm({})

const addPlatform = () => {
  if (newPlatform.value.trim() && !form.platforms.includes(newPlatform.value.trim())) {
    form.platforms.push(newPlatform.value.trim())
    newPlatform.value = ''
  }
}

const submit = () => {
  form.patch(route('admin.games.update', props.game.slug))
}

const deleteGame = () => {
  deleteForm.delete(route('admin.games.destroy', props.game.slug))
}
</script>
