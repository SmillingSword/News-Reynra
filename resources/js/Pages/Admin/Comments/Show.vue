<template>
    <AdminLayout title="Comment Details">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Comment Details
                </h2>
                <Link :href="route('admin.comments.index')" 
                      class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Comments
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Comment Information</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">User</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ comment.user?.name || 'Anonymous' }}
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ comment.user?.email || 'N/A' }}
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Article</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ comment.article?.title || 'Deleted Article' }}
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                        <span :class="[
                                            'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                            comment.is_approved 
                                                ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
                                                : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
                                        ]">
                                            {{ comment.is_approved ? 'Approved' : 'Pending' }}
                                        </span>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Created At</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ new Date(comment.created_at).toLocaleString() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Comment Content</h3>
                                
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                                        {{ comment.content }}
                                    </p>
                                </div>
                                
                                <div class="mt-6 flex space-x-3">
                                    <button @click="toggleApproval" 
                                            :class="[
                                                'px-4 py-2 rounded-md text-sm font-medium',
                                                comment.is_approved 
                                                    ? 'bg-red-600 hover:bg-red-700 text-white'
                                                    : 'bg-green-600 hover:bg-green-700 text-white'
                                            ]">
                                        {{ comment.is_approved ? 'Unapprove' : 'Approve' }}
                                    </button>
                                    
                                    <button @click="deleteComment" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    comment: Object,
});

const toggleApproval = () => {
    router.patch(route('admin.comments.update', props.comment.id), {
        is_approved: !props.comment.is_approved
    });
};

const deleteComment = () => {
    if (confirm('Are you sure you want to delete this comment?')) {
        router.delete(route('admin.comments.destroy', props.comment.id));
    }
};
</script>
