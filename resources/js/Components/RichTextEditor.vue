<template>
  <div class="rich-text-editor">
    <!-- Toolbar -->
    <div class="toolbar bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 p-3 rounded-t-lg">
      <div class="flex flex-wrap items-center gap-2">
        <!-- Text Formatting -->
        <div class="flex items-center space-x-1 border-r border-gray-300 dark:border-gray-600 pr-3">
          <button
            type="button"
            @click="execCommand('bold')"
            :class="[
              'p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors',
              isActive('bold') ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300'
            ]"
            title="Bold (Ctrl+B)"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path d="M12.6 8.7c.6-.4 1-1.1 1-1.9 0-1.4-1.2-2.6-2.6-2.6H6v12h5.4c1.6 0 2.9-1.3 2.9-2.9 0-1.2-.7-2.2-1.7-2.6zM8.1 6.4h2.7c.6 0 1.1.5 1.1 1.1s-.5 1.1-1.1 1.1H8.1V6.4zm3.1 7.1H8.1v-2.6h3.1c.7 0 1.3.6 1.3 1.3s-.6 1.3-1.3 1.3z"/>
            </svg>
          </button>
          
          <button
            type="button"
            @click="execCommand('italic')"
            :class="[
              'p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors',
              isActive('italic') ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300'
            ]"
            title="Italic (Ctrl+I)"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path d="M8.5 4h3l-.5 2h-1l-2 8h1l-.5 2h-3l.5-2h1l2-8h-1l.5-2z"/>
            </svg>
          </button>
          
          <button
            type="button"
            @click="execCommand('underline')"
            :class="[
              'p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors',
              isActive('underline') ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300'
            ]"
            title="Underline (Ctrl+U)"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path d="M6 3v6c0 2.2 1.8 4 4 4s4-1.8 4-4V3h-2v6c0 1.1-.9 2-2 2s-2-.9-2-2V3H6zm-2 14h12v2H4v-2z"/>
            </svg>
          </button>
        </div>

        <!-- Headings -->
        <div class="flex items-center space-x-1 border-r border-gray-300 dark:border-gray-600 pr-3">
          <select
            @change="formatHeading($event.target.value)"
            class="text-sm border-0 bg-transparent text-gray-600 dark:text-gray-300 focus:ring-0"
          >
            <option value="">Paragraph</option>
            <option value="h1">Heading 1</option>
            <option value="h2">Heading 2</option>
            <option value="h3">Heading 3</option>
            <option value="h4">Heading 4</option>
          </select>
        </div>

        <!-- Lists -->
        <div class="flex items-center space-x-1 border-r border-gray-300 dark:border-gray-600 pr-3">
          <button
            type="button"
            @click="execCommand('insertUnorderedList')"
            class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-gray-600 dark:text-gray-300"
            title="Bullet List"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path d="M3 4a1 1 0 100 2 1 1 0 000-2zM6 4h11a1 1 0 110 2H6a1 1 0 110-2zM3 9a1 1 0 100 2 1 1 0 000-2zM6 9h11a1 1 0 110 2H6a1 1 0 110-2zM3 14a1 1 0 100 2 1 1 0 000-2zM6 14h11a1 1 0 110 2H6a1 1 0 110-2z"/>
            </svg>
          </button>
          
          <button
            type="button"
            @click="execCommand('insertOrderedList')"
            class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-gray-600 dark:text-gray-300"
            title="Numbered List"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path d="M3 4h1v1H3V4zM3 7h1v1H3V7zM3 10h1v1H3v-1zM6 4h11a1 1 0 110 2H6a1 1 0 110-2zM6 9h11a1 1 0 110 2H6a1 1 0 110-2zM6 14h11a1 1 0 110 2H6a1 1 0 110-2z"/>
            </svg>
          </button>
        </div>

        <!-- Links and Media -->
        <div class="flex items-center space-x-1 border-r border-gray-300 dark:border-gray-600 pr-3">
          <button
            type="button"
            @click="insertLink"
            class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-gray-600 dark:text-gray-300"
            title="Insert Link"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z"/>
            </svg>
          </button>
          
          <button
            type="button"
            @click="insertImage"
            class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-gray-600 dark:text-gray-300"
            title="Insert Image"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
            </svg>
          </button>
        </div>

        <!-- Code and Quote -->
        <div class="flex items-center space-x-1 border-r border-gray-300 dark:border-gray-600 pr-3">
          <button
            type="button"
            @click="insertCodeBlock"
            class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-gray-600 dark:text-gray-300"
            title="Code Block"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </button>
          
          <button
            type="button"
            @click="execCommand('formatBlock', 'blockquote')"
            class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-gray-600 dark:text-gray-300"
            title="Quote"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
            </svg>
          </button>
        </div>

        <!-- Alignment -->
        <div class="flex items-center space-x-1">
          <button
            type="button"
            @click="execCommand('justifyLeft')"
            class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-gray-600 dark:text-gray-300"
            title="Align Left"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h8a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h8a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
            </svg>
          </button>
          
          <button
            type="button"
            @click="execCommand('justifyCenter')"
            class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-gray-600 dark:text-gray-300"
            title="Align Center"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm2 4a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm-2 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm2 4a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd"/>
            </svg>
          </button>
          
          <button
            type="button"
            @click="execCommand('justifyRight')"
            class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-gray-600 dark:text-gray-300"
            title="Align Right"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm4 4a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm-4 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm4 4a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1z" clip-rule="evenodd"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Editor Content -->
    <div class="relative">
      <div
        ref="editor"
        contenteditable="true"
        @input="handleInput"
        @keydown="handleKeydown"
        @paste="handlePaste"
        :placeholder="placeholder"
        class="min-h-[400px] p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none prose prose-lg max-w-none"
        style="white-space: pre-wrap;"
      ></div>
      
      <!-- Word Count -->
      <div class="absolute bottom-4 right-4 text-sm text-gray-500 dark:text-gray-400 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm px-3 py-1 rounded-full">
        {{ wordCount }} words • {{ readingTime }} min read
      </div>
    </div>

    <!-- Auto-save Indicator -->
    <div v-if="autoSave" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 rounded-b-lg">
      <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
        <div 
          :class="[
            'w-2 h-2 rounded-full',
            saveStatus === 'saving' ? 'bg-yellow-500 animate-pulse' :
            saveStatus === 'saved' ? 'bg-green-500' : 'bg-gray-400'
          ]"
        ></div>
        <span>
          {{ 
            saveStatus === 'saving' ? 'Saving...' :
            saveStatus === 'saved' ? 'All changes saved' : 'Not saved'
          }}
        </span>
      </div>
      <div class="text-sm text-gray-500 dark:text-gray-400">
        Last saved: {{ lastSaved ? formatTime(lastSaved) : 'Never' }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Start writing your content...'
  },
  autoSave: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'save'])

const editor = ref(null)
const saveStatus = ref('idle') // idle, saving, saved
const lastSaved = ref(null)
let saveTimeout = null

// Content management
const handleInput = () => {
  if (editor.value) {
    const content = editor.value.innerHTML
    emit('update:modelValue', content)
    
    if (props.autoSave) {
      debouncedSave()
    }
  }
}

const debouncedSave = () => {
  saveStatus.value = 'saving'
  
  if (saveTimeout) {
    clearTimeout(saveTimeout)
  }
  
  saveTimeout = setTimeout(() => {
    emit('save')
    saveStatus.value = 'saved'
    lastSaved.value = new Date()
  }, 1000)
}

// Word count and reading time
const wordCount = computed(() => {
  if (!editor.value) return 0
  const text = editor.value.textContent || ''
  return text.trim().split(/\s+/).filter(word => word.length > 0).length
})

const readingTime = computed(() => {
  const wordsPerMinute = 200
  return Math.ceil(wordCount.value / wordsPerMinute) || 1
})

// Editor commands
const execCommand = (command, value = null) => {
  document.execCommand(command, false, value)
  editor.value.focus()
}

const isActive = (command) => {
  return document.queryCommandState(command)
}

const formatHeading = (tag) => {
  if (tag) {
    execCommand('formatBlock', tag)
  } else {
    execCommand('formatBlock', 'p')
  }
}

const insertLink = () => {
  const url = prompt('Enter URL:')
  if (url) {
    execCommand('createLink', url)
  }
}

const insertImage = () => {
  const url = prompt('Enter image URL:')
  if (url) {
    execCommand('insertImage', url)
  }
}

const insertCodeBlock = () => {
  const selection = window.getSelection()
  if (selection.rangeCount > 0) {
    const range = selection.getRangeAt(0)
    const code = document.createElement('pre')
    code.style.backgroundColor = '#f3f4f6'
    code.style.padding = '1rem'
    code.style.borderRadius = '0.5rem'
    code.style.fontFamily = 'monospace'
    code.style.overflow = 'auto'
    
    const codeContent = document.createElement('code')
    codeContent.textContent = range.toString() || 'Your code here...'
    code.appendChild(codeContent)
    
    range.deleteContents()
    range.insertNode(code)
    selection.removeAllRanges()
  }
}

// Keyboard shortcuts
const handleKeydown = (e) => {
  if (e.ctrlKey || e.metaKey) {
    switch (e.key) {
      case 'b':
        e.preventDefault()
        execCommand('bold')
        break
      case 'i':
        e.preventDefault()
        execCommand('italic')
        break
      case 'u':
        e.preventDefault()
        execCommand('underline')
        break
      case 's':
        e.preventDefault()
        if (props.autoSave) {
          emit('save')
        }
        break
    }
  }
}

// Paste handling
const handlePaste = (e) => {
  e.preventDefault()
  const text = e.clipboardData.getData('text/plain')
  execCommand('insertText', text)
}

// Utility functions
const formatTime = (date) => {
  return date.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Watch for external content changes
watch(() => props.modelValue, (newValue) => {
  if (editor.value && editor.value.innerHTML !== newValue) {
    editor.value.innerHTML = newValue
  }
})

onMounted(() => {
  if (editor.value && props.modelValue) {
    editor.value.innerHTML = props.modelValue
  }
})

onUnmounted(() => {
  if (saveTimeout) {
    clearTimeout(saveTimeout)
  }
})
</script>

<style scoped>
.rich-text-editor {
  @apply border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden;
}

.prose {
  @apply text-gray-900 dark:text-white;
}

.prose h1 {
  @apply text-3xl font-bold mb-4;
}

.prose h2 {
  @apply text-2xl font-bold mb-3;
}

.prose h3 {
  @apply text-xl font-bold mb-2;
}

.prose h4 {
  @apply text-lg font-bold mb-2;
}

.prose p {
  @apply mb-4;
}

.prose ul, .prose ol {
  @apply mb-4 pl-6;
}

.prose li {
  @apply mb-1;
}

.prose blockquote {
  @apply border-l-4 border-gray-300 dark:border-gray-600 pl-4 italic text-gray-600 dark:text-gray-400 mb-4;
}

.prose pre {
  @apply bg-gray-100 dark:bg-gray-700 p-4 rounded-lg mb-4 overflow-auto;
}

.prose code {
  @apply bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-sm;
}

.prose img {
  @apply max-w-full h-auto rounded-lg mb-4;
}

.prose a {
  @apply text-blue-600 dark:text-blue-400 hover:underline;
}

/* Placeholder styling */
[contenteditable]:empty:before {
  content: attr(placeholder);
  color: #9CA3AF;
  pointer-events: none;
}
</style>
