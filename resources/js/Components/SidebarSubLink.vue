<template>
  <Link
    :href="href"
    :class="[
      'group flex items-center pl-11 pr-3 py-2 text-sm font-medium rounded-lg transition-all duration-200',
      active
        ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600'
        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
    ]"
  >
    <!-- Connector Line -->
    <div class="absolute left-6 w-2 h-2 border-l-2 border-b-2 border-gray-300 rounded-bl-sm"></div>
    
    <!-- Icon (optional) -->
    <svg 
      v-if="showIcon"
      :class="[
        'flex-shrink-0 w-4 h-4 mr-3 transition-colors duration-200',
        active ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500'
      ]"
      fill="none" 
      stroke="currentColor" 
      viewBox="0 0 24 24"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
    </svg>
    
    <!-- Label -->
    <span class="flex-1 truncate">
      <slot />
    </span>
    
    <!-- Badge -->
    <span 
      v-if="badge" 
      :class="[
        'ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
        active 
          ? 'bg-blue-100 text-blue-600' 
          : 'bg-gray-100 text-gray-600 group-hover:bg-gray-200'
      ]"
    >
      {{ badge }}
    </span>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  href: {
    type: String,
    required: true
  },
  active: {
    type: Boolean,
    default: false
  },
  badge: {
    type: [String, Number],
    default: null
  },
  showIcon: {
    type: Boolean,
    default: false
  }
})
</script>

<style scoped>
/* Custom positioning for the connector line */
.group {
  position: relative;
}
</style>
