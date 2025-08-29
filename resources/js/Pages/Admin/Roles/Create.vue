<template>
  <AdminLayout title="Create Role">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">Create New Role</h2>
          
          <form @submit.prevent="submit" class="space-y-6">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">Role Name</label>
              <input
                type="text"
                id="name"
                v-model="form.name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              />
            </div>

            <div>
              <label for="guard_name" class="block text-sm font-medium text-gray-700">Guard Name</label>
              <input
                type="text"
                id="guard_name"
                v-model="form.guard_name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Permissions</label>
              <div class="mt-2 space-y-2">
                <div v-for="permission in permissions" :key="permission.id" class="flex items-center">
                  <input
                    :id="`permission-${permission.id}`"
                    type="checkbox"
                    :value="permission.name"
                    v-model="form.permissions"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  />
                  <label :for="`permission-${permission.id}`" class="ml-2 block text-sm text-gray-900">
                    {{ permission.name }}
                  </label>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-end space-x-3">
              <Link
                :href="route('admin.roles.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Cancel
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
              >
                Create Role
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  permissions: {
    type: Array,
    default: () => []
  }
})

const form = useForm({
  name: '',
  guard_name: 'web',
  permissions: []
})

const submit = () => {
  form.post(route('admin.roles.store'), {
    onSuccess: () => {
      form.reset()
    }
  })
}
</script>
