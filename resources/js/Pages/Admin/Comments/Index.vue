<template>
  <Head title="Comments Management" />

  <ModernAdminLayout page-title="Comments Management">
    <div class="space-y-6">
      <!-- Header Actions -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">Comments Management</h2>
          <p class="text-gray-600">Moderate and manage user comments</p>
        </div>
        
        <div class="flex items-center space-x-4">
          <!-- Filter -->
          <select
            v-model="statusFilter"
            @change="filterComments"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">All Comments</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="spam">Spam</option>
          </select>

          <!-- Search -->
          <div class="relative">
            <input
              v-model="searchQuery"
              @input="searchComments"
              type="text"
              placeholder="Search comments..."
              class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
            <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Comments</p>
              <p class="text-2xl font-bold text-gray-900">{{ commentStats.total }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Pending</p>
              <p class="text-2xl font-bold text-gray-900">{{ commentStats.pending }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Approved</p>
              <p class="text-2xl font-bold text-gray-900">{{ commentStats.approved }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Spam</p>
              <p class="text-2xl font-bold text-gray-900">{{ commentStats.spam }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Comments List -->
      <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">All Comments</h3>
        </div>

        <div class="divide-y divide-gray-200">
          <div
            v-for="comment in filteredComments"
            :key="comment.id"
            class="p-6 hover:bg-gray-50 transition-colors"
          >
            <div class="flex items-start space-x-4">
              <!-- Avatar -->
              <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                  <span class="text-sm font-medium text-white">
                    {{ (comment.user?.name || comment.author_name || 'A').charAt(0).toUpperCase() }}
                  </span>
                </div>
              </div>

              <!-- Comment Content -->
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-2">
                    <p class="text-sm font-medium text-gray-900">
                      {{ comment.user?.name || comment.author_name || 'Anonymous' }}
                    </p>
                    <span class="text-sm text-gray-500">•</span>
                    <p class="text-sm text-gray-500">{{ formatDate(comment.created_at) }}</p>
                    <span v-if="comment.parent_id" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      Reply
                    </span>
                  </div>
                  
                  <!-- Status Badge -->
                  <span :class="getStatusColor(comment.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                    {{ comment.status }}
                  </span>
                </div>

                <!-- Article Info -->
                <p class="text-sm text-gray-600 mt-1">
                  on <Link :href="route('admin.articles.show', comment.article?.id)" class="text-blue-600 hover:text-blue-800">{{ comment.article?.title }}</Link>
                </p>

                <!-- Comment Text -->
                <div class="mt-2">
                  <p class="text-gray-900">{{ comment.content }}</p>
                </div>

                <!-- Reply Form -->
                <div v-if="replyingTo === comment.id" class="mt-4 p-4 bg-gray-50 rounded-lg">
                  <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center">
                      <span class="text-xs font-medium text-white">
                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                      </span>
                    </div>
                    <div class="flex-1">
                      <textarea
                        v-model="replyContent"
                        rows="3"
                        placeholder="Write your reply..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                      ></textarea>
                      <div class="flex items-center justify-end space-x-2 mt-2">
                        <button
                          @click="cancelReply"
                          class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-800"
                        >
                          Cancel
                        </button>
                        <button
                          @click="submitReply(comment)"
                          :disabled="!replyContent.trim()"
                          class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white text-sm font-medium rounded-lg transition-colors"
                        >
                          Reply
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Replies -->
                <div v-if="comment.replies && comment.replies.length > 0" class="mt-4 ml-6 space-y-4">
                  <div
                    v-for="reply in comment.replies"
                    :key="reply.id"
                    class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg"
                  >
                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center">
                      <span class="text-xs font-medium text-white">
                        {{ (reply.user?.name || reply.author_name || 'A').charAt(0).toUpperCase() }}
                      </span>
                    </div>
                    <div class="flex-1">
                      <div class="flex items-center space-x-2">
                        <p class="text-sm font-medium text-gray-900">
                          {{ reply.user?.name || reply.author_name || 'Anonymous' }}
                        </p>
                        <span class="text-xs text-gray-500">{{ formatDate(reply.created_at) }}</span>
                        <span v-if="reply.user?.id === reply.article?.author_id" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                          Author
                        </span>
                      </div>
                      <p class="text-sm text-gray-900 mt-1">{{ reply.content }}</p>
                    </div>
                  </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-4 mt-4">
                  <button
                    v-if="comment.status === 'pending'"
                    @click="approveComment(comment.id)"
                    class="text-green-600 hover:text-green-800 text-sm font-medium"
                  >
                    Approve
                  </button>
                  <button
                    v-if="comment.status === 'approved'"
                    @click="rejectComment(comment.id)"
                    class="text-yellow-600 hover:text-yellow-800 text-sm font-medium"
                  >
                    Reject
                  </button>
                  <button
                    @click="startReply(comment.id)"
                    class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                  >
                    Reply
                  </button>
                  <button
                    @click="markAsSpam(comment.id)"
                    class="text-orange-600 hover:text-orange-800 text-sm font-medium"
                  >
                    Mark as Spam
                  </button>
                  <button
                    @click="deleteComment(comment.id)"
                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredComments.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No comments found</h3>
          <p class="mt-1 text-sm text-gray-500">Comments will appear here when users start engaging with your articles.</p>
        </div>
      </div>
    </div>
  </ModernAdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import ModernAdminLayout from '@/Layouts/ModernAdminLayout.vue'

// Sample data - in real app this would come from props
const statusFilter = ref('')
const searchQuery = ref('')
const replyingTo = ref(null)
const replyContent = ref('')

const commentStats = {
  total: 156,
  pending: 23,
  approved: 128,
  spam: 5
}

// Sample comments data with nested replies
const comments = [
  {
    id: 1,
    content: "Great article! Really enjoyed reading about the new game mechanics.",
    user: { id: 2, name: "John Doe" },
    article: { id: 1, title: "New Gaming Trends 2024", author_id: 1 },
    status: 'approved',
    created_at: '2024-01-15T10:30:00Z',
    parent_id: null,
    replies: [
      {
        id: 2,
        content: "Thanks for the feedback! We're glad you enjoyed it.",
        user: { id: 1, name: "Admin User" },
        article: { id: 1, title: "New Gaming Trends 2024", author_id: 1 },
        status: 'approved',
        created_at: '2024-01-15T11:00:00Z',
        parent_id: 1
      }
    ]
  },
  {
    id: 3,
    content: "I disagree with some points mentioned in this article. The analysis seems biased.",
    user: { id: 3, name: "Jane Smith" },
    article: { id: 2, title: "Gaming Industry Analysis" },
    status: 'pending',
    created_at: '2024-01-14T15:45:00Z',
    parent_id: null,
    replies: []
  },
  {
    id: 4,
    content: "This is spam content with promotional links.",
    author_name: "Spammer",
    author_email: "spam@example.com",
    article: { id: 1, title: "New Gaming Trends 2024" },
    status: 'spam',
    created_at: '2024-01-13T09:20:00Z',
    parent_id: null,
    replies: []
  }
]

const filteredComments = computed(() => {
  let filtered = comments

  if (statusFilter.value) {
    filtered = filtered.filter(comment => comment.status === statusFilter.value)
  }

  if (searchQuery.value) {
    filtered = filtered.filter(comment =>
      comment.content.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      (comment.user?.name || comment.author_name || '').toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  return filtered
})

const getStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    spam: 'bg-orange-100 text-orange-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const filterComments = () => {
  // In real app, this would trigger a server request
  console.log('Filtering by status:', statusFilter.value)
}

const searchComments = () => {
  // In real app, this would trigger a server request
  console.log('Searching for:', searchQuery.value)
}

const startReply = (commentId) => {
  replyingTo.value = commentId
  replyContent.value = ''
}

const cancelReply = () => {
  replyingTo.value = null
  replyContent.value = ''
}

const submitReply = (comment) => {
  if (!replyContent.value.trim()) return

  // In real app, this would send to server
  alert(`Reply submitted to comment ${comment.id}: "${replyContent.value}"`)
  
  // Add reply to local data for demo
  comment.replies.push({
    id: Date.now(),
    content: replyContent.value,
    user: { id: 1, name: "Admin User" },
    article: comment.article,
    status: 'approved',
    created_at: new Date().toISOString(),
    parent_id: comment.id
  })

  cancelReply()
}

const approveComment = (id) => {
  alert(`Comment ${id} approved`)
  // In real app: router.patch(route('admin.comments.approve', id))
}

const rejectComment = (id) => {
  alert(`Comment ${id} rejected`)
  // In real app: router.patch(route('admin.comments.reject', id))
}

const markAsSpam = (id) => {
  if (confirm('Mark this comment as spam?')) {
    alert(`Comment ${id} marked as spam`)
    // In real app: router.patch(route('admin.comments.spam', id))
  }
}

const deleteComment = (id) => {
  if (confirm('Are you sure you want to delete this comment?')) {
    alert(`Comment ${id} deleted`)
    // In real app: router.delete(route('admin.comments.destroy', id))
  }
}
</script>
