<template>
  <Head :title="`Edit User: ${user.name}`" />

  <ModernAdminLayout :page-title="`Edit User: ${user.name}`">
    <div class="max-w-4xl mx-auto">
      <!-- Back Button -->
      <div class="mb-6">
        <Link
          :href="route('admin.users.show', user.id)"
          class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          Back to User Details
        </Link>
      </div>

      <form @submit.prevent="submit" class="space-y-8">
        <!-- Basic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Basic Information</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Update user's essential details</p>
          </div>
          
          <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Name -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Full Name *
                </label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  required
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                  placeholder="Enter full name"
                >
                <InputError :message="form.errors.name" class="mt-2" />
              </div>

              <!-- Email -->
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Email Address *
                </label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                  placeholder="Enter email address"
                >
                <InputError :message="form.errors.email" class="mt-2" />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Password -->
              <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  New Password
                </label>
                <input
                  id="password"
                  v-model="form.password"
                  type="password"
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                  placeholder="Leave blank to keep current password"
                >
                <InputError :message="form.errors.password" class="mt-2" />
              </div>

              <!-- Confirm Password -->
              <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Confirm New Password
                </label>
                <input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  type="password"
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                  placeholder="Confirm new password"
                >
                <InputError :message="form.errors.password_confirmation" class="mt-2" />
              </div>
            </div>

            <!-- Email Verification -->
            <div class="flex items-center">
              <input
                id="email_verified"
                v-model="form.email_verified"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              >
              <label for="email_verified" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                Mark email as verified
              </label>
            </div>
          </div>
        </div>

        <!-- Roles & Permissions -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Roles & Permissions</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Manage user roles and access levels</p>
          </div>
          
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div
                v-for="role in roles"
                :key="role.id"
                class="relative"
              >
                <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                  <input
                    :value="role.id"
                    v-model="form.roles"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900 dark:text-white capitalize">
                      {{ role.name }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                      {{ getRoleDescription(role.name) }}
                    </div>
                  </div>
                </label>
              </div>
            </div>
            <InputError :message="form.errors.roles" class="mt-2" />
          </div>
        </div>

        <!-- Gaming Profile -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Gaming Profile</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Update gaming preferences and information</p>
          </div>
          
          <div class="p-6 space-y-6">
            <!-- Bio -->
            <div>
              <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Bio
              </label>
              <textarea
                id="bio"
                v-model="form.bio"
                rows="3"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                placeholder="Tell us about yourself..."
              ></textarea>
              <InputError :message="form.errors.bio" class="mt-2" />
            </div>

            <!-- Gaming Preferences -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Gaming Preferences
              </label>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <label
                  v-for="preference in gameGenres"
                  :key="preference"
                  class="flex items-center"
                >
                  <input
                    :value="preference"
                    v-model="form.gaming_preferences"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ preference }}</span>
                </label>
              </div>
            </div>

            <!-- Favorite Games -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Favorite Games
              </label>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <label
                  v-for="game in popularGames"
                  :key="game"
                  class="flex items-center"
                >
                  <input
                    :value="game"
                    v-model="form.favorite_games"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ game }}</span>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
          <button
            type="button"
            @click="deleteUser"
            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Delete User
          </button>

          <div class="flex items-center space-x-4">
            <Link
              :href="route('admin.users.show', user.id)"
              class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-lg transition-colors flex items-center"
            >
              <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ form.processing ? 'Updating...' : 'Update User' }}
            </button>
          </div>
        </div>
      </form>

      <!-- Delete Confirmation Modal -->
      <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
          <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30">
              <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-2">Delete User</h3>
            <div class="mt-2 px-7 py-3">
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Are you sure you want to delete <strong>{{ user.name }}</strong>? This action cannot be undone.
              </p>
            </div>
            <div class="flex items-center justify-center space-x-4 mt-4">
              <button
                @click="showDeleteModal = false"
                class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors"
              >
                Cancel
              </button>
              <button
                @click="confirmDelete"
                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </ModernAdminLayout>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import ModernAdminLayout from '@/Layouts/ModernAdminLayout.vue'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
  user: Object,
  roles: Array,
  userRoles: Array,
})

const showDeleteModal = ref(false)

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  password: '',
  password_confirmation: '',
  roles: props.userRoles,
  email_verified: !!props.user.email_verified_at,
  bio: props.user.bio || '',
  gaming_preferences: props.user.gaming_preferences || [],
  favorite_games: props.user.favorite_games || [],
})

const gameGenres = [
  'Action', 'Adventure', 'RPG', 'Strategy', 'Simulation', 'Sports',
  'Racing', 'Puzzle', 'Fighting', 'Shooter', 'Horror', 'Platformer'
]

const popularGames = [
  'Minecraft', 'Fortnite', 'League of Legends', 'Valorant', 'CS:GO', 'Dota 2',
  'Among Us', 'Call of Duty', 'FIFA', 'GTA V', 'Apex Legends', 'Overwatch'
]

const submit = () => {
  form.put(route('admin.users.update', props.user.id))
}

const deleteUser = () => {
  showDeleteModal.value = true
}

const confirmDelete = () => {
  router.delete(route('admin.users.destroy', props.user.id))
}

const getRoleDescription = (roleName) => {
  const descriptions = {
    admin: 'Full system access',
    editor: 'Content management',
    user: 'Basic user access',
  }
  return descriptions[roleName] || 'Custom role'
}
</script>
