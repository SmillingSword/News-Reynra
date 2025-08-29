<template>
  <Head :title="`Edit Tag: ${tag.name}`" />

  <ModernAdminLayout :page-title="`Edit Tag: ${tag.name}`">
    <div class="max-w-4xl mx-auto space-y-8">
      <!-- Breadcrumb -->
      <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <Link :href="route('admin.dashboard')" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-9 9a1 1 0 001.414 1.414L9 4.414V17a1 1 0 102 0V4.414l7.293 7.293a1 1 0 001.414-1.414l-9-9z"/>
              </svg>
              Dashboard
            </Link>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
              </svg>
              <Link :href="route('admin.tags.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                Tags
              </Link>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
              </svg>
              <Link :href="route('admin.tags.show', tag.slug)" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                {{ tag.name }}
              </Link>
            </div>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit</span>
            </div>
          </li>
        </ol>
      </nav>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form -->
        <div class="lg:col-span-2">
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Basic Information Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
                <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                <p class="text-sm text-gray-600 mt-1">Update the basic details for your tag</p>
              </div>

              <div class="p-6 space-y-6">
                <!-- Tag Name -->
                <div>
                  <InputLabel for="name" value="Tag Name" class="text-sm font-medium text-gray-700" />
                  <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="e.g., Gaming, Esports, Reviews"
                    required
                    autofocus
                  />
                  <InputError class="mt-2" :message="form.errors.name" />
                  <p class="mt-1 text-sm text-gray-500">Choose a descriptive name for your tag</p>
                </div>

                <!-- Description -->
                <div>
                  <InputLabel for="description" value="Description" class="text-sm font-medium text-gray-700" />
                  <textarea
                    id="description"
                    v-model="form.description"
                    rows="4"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Describe what this tag represents..."
                  ></textarea>
                  <InputError class="mt-2" :message="form.errors.description" />
                  <p class="mt-1 text-sm text-gray-500">Optional description to help users understand this tag</p>
                </div>

                <!-- Color Picker -->
                <div>
                  <InputLabel for="color" value="Tag Color" class="text-sm font-medium text-gray-700" />
                  <div class="mt-2 flex items-center space-x-4">
                    <div class="relative">
                      <input
                        id="color"
                        v-model="form.color"
                        type="color"
                        class="h-12 w-20 rounded-lg border-2 border-gray-300 cursor-pointer"
                      />
                    </div>
                    <div class="flex-1">
                      <TextInput
                        v-model="form.color"
                        type="text"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="#10B981"
                        pattern="^#[0-9A-Fa-f]{6}$"
                      />
                    </div>
                  </div>
                  <InputError class="mt-2" :message="form.errors.color" />
                  <p class="mt-1 text-sm text-gray-500">Choose a color to represent this tag</p>
                </div>

                <!-- Featured Toggle -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">Featured Tag</h4>
                    <p class="text-sm text-gray-500">Featured tags appear prominently on the homepage</p>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input
                      v-model="form.is_featured"
                      type="checkbox"
                      class="sr-only peer"
                    />
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                  </label>
                </div>
              </div>
            </div>

            <!-- SEO Settings Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-blue-50">
                <h3 class="text-lg font-semibold text-gray-900">SEO Settings</h3>
                <p class="text-sm text-gray-600 mt-1">Optimize your tag for search engines</p>
              </div>

              <div class="p-6 space-y-6">
                <!-- Meta Title -->
                <div>
                  <InputLabel for="meta_title" value="Meta Title" class="text-sm font-medium text-gray-700" />
                  <TextInput
                    id="meta_title"
                    v-model="form.meta_title"
                    type="text"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="SEO title for this tag"
                    maxlength="60"
                  />
                  <InputError class="mt-2" :message="form.errors.meta_title" />
                  <p class="mt-1 text-sm text-gray-500">
                    {{ form.meta_title ? form.meta_title.length : 0 }}/60 characters
                  </p>
                </div>

                <!-- Meta Description -->
                <div>
                  <InputLabel for="meta_description" value="Meta Description" class="text-sm font-medium text-gray-700" />
                  <textarea
                    id="meta_description"
                    v-model="form.meta_description"
                    rows="3"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Brief description for search engines"
                    maxlength="160"
                  ></textarea>
                  <InputError class="mt-2" :message="form.errors.meta_description" />
                  <p class="mt-1 text-sm text-gray-500">
                    {{ form.meta_description ? form.meta_description.length : 0 }}/160 characters
                  </p>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <Link
                  :href="route('admin.tags.index')"
                  class="px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                >
                  Cancel
                </Link>
                <Link
                  :href="route('admin.tags.show', tag.slug)"
                  class="px-6 py-3 border border-blue-300 rounded-lg text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                >
                  View Tag
                </Link>
              </div>

              <div class="flex items-center space-x-4">
                <!-- Delete Button -->
                <button
                  v-if="canDelete"
                  @click="showDeleteModal = true"
                  type="button"
                  class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                >
                  Delete Tag
                </button>

                <button
                  type="submit"
                  :disabled="form.processing"
                  class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                >
                  <span v-if="form.processing" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Updating...
                  </span>
                  <span v-else>Update Tag</span>
                </button>
              </div>
            </div>
          </form>
        </div>

        <!-- Preview Sidebar -->
        <div class="lg:col-span-1">
          <div class="sticky top-6 space-y-6">
            <!-- Live Preview -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                <h3 class="text-lg font-semibold text-gray-900">Live Preview</h3>
                <p class="text-sm text-gray-600 mt-1">See how your tag will appear</p>
              </div>

              <div class="p-6">
                <!-- Tag Preview -->
                <div class="space-y-4">
                  <div class="p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                    <div class="flex items-center space-x-3 mb-2">
                      <div
                        class="w-4 h-4 rounded-full border-2 border-white shadow-sm"
                        :style="{ backgroundColor: form.color || '#10B981' }"
                      ></div>
                      <h4 class="font-semibold text-gray-900">
                        {{ form.name || 'Tag Name' }}
                      </h4>
                      <span v-if="form.is_featured" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        Featured
                      </span>
                    </div>
                    <p v-if="form.description" class="text-sm text-gray-600">
                      {{ form.description }}
                    </p>
                    <p v-else class="text-sm text-gray-400 italic">
                      No description provided
                    </p>
                  </div>

                  <!-- Tag Chip Preview -->
                  <div>
                    <h5 class="text-sm font-medium text-gray-700 mb-2">As Tag Chip:</h5>
                    <span
                      class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border"
                      :style="{
                        backgroundColor: form.color ? form.color + '20' : '#10B98120',
                        borderColor: form.color || '#10B981',
                        color: form.color || '#10B981'
                      }"
                    >
                      {{ form.name || 'Tag Name' }}
                    </span>
                  </div>

                  <!-- SEO Preview -->
                  <div v-if="form.meta_title || form.meta_description">
                    <h5 class="text-sm font-medium text-gray-700 mb-2">SEO Preview:</h5>
                    <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                      <h6 class="text-blue-600 text-sm font-medium">
                        {{ form.meta_title || form.name || 'Tag Name' }}
                      </h6>
                      <p class="text-green-600 text-xs mt-1">
                        example.com/tags/{{ tag.slug }}
                      </p>
                      <p v-if="form.meta_description" class="text-gray-600 text-sm mt-1">
                        {{ form.meta_description }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Usage Stats -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-blue-50">
                <h3 class="text-lg font-semibold text-gray-900">Usage Statistics</h3>
              </div>

              <div class="p-6">
                <dl class="space-y-3">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Usage Count</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ tag.usage_count }} times</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Articles Tagged</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ tag.articles_count || 0 }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                    <dd class="text-sm text-gray-600">{{ formatDate(tag.created_at) }}</dd>
                  </div>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false">
      <div class="p-6">
        <div class="flex items-center space-x-4">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
              </svg>
            </div>
          </div>
          <div class="flex-1">
            <h3 class="text-lg font-medium text-gray-900">Delete Tag</h3>
            <p class="text-sm text-gray-500 mt-1">
              Are you sure you want to delete "{{ tag.name }}"? This action cannot be undone.
            </p>
            <p v-if="tag.usage_count > 0" class="text-sm text-red-600 mt-2 font-medium">
              ⚠️ This tag is currently used in {{ tag.usage_count }} articles. You cannot delete it until it's removed from all articles.
            </p>
          </div>
        </div>

        <div class="flex items-center justify-end space-x-4 mt-6">
          <button
            @click="showDeleteModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Cancel
          </button>
          <button
            @click="deleteTag"
            :disabled="tag.usage_count > 0 || deleteForm.processing"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="deleteForm.processing">Deleting...</span>
            <span v-else>Delete Tag</span>
          </button>
        </div>
      </div>
    </Modal>
  </ModernAdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import ModernAdminLayout from '@/Layouts/ModernAdminLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  tag: Object,
})

const showDeleteModal = ref(false)

const form = useForm({
  name: props.tag.name,
  description: props.tag.description || '',
  color: props.tag.color,
  is_featured: props.tag.is_featured,
  meta_title: props.tag.meta?.title || '',
  meta_description: props.tag.meta?.description || '',
})

const deleteForm = useForm({})

const canDelete = computed(() => {
  return props.tag.usage_count === 0
})

const submit = () => {
  form.put(route('admin.tags.update', props.tag.slug))
}

const deleteTag = () => {
  deleteForm.delete(route('admin.tags.destroy', props.tag.slug), {
    onSuccess: () => {
      showDeleteModal.value = false
    }
  })
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'long',
    day: 'numeric',
    year: 'numeric'
  })
}
</script>
