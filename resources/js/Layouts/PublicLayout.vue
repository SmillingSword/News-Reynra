<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-200 custom-scrollbar">
    <!-- Reading Progress Bar -->
    <div class="progress-bar" :style="{ width: readingProgress + '%' }"></div>
    
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 nav-blur dark:nav-blur-dark transition-all duration-300">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo -->
          <div class="flex items-center">
            <Link :href="route('home')" class="flex items-center space-x-3 group">
              <!-- Enhanced Logo Icon -->
              <div class="relative">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl shadow-lg flex items-center justify-center transform group-hover:scale-105 transition-all duration-300">
                  <!-- NR Logo -->
                  <div class="font-black text-lg tracking-tighter leading-none text-white">
                    <span class="text-blue-200">N</span><span class="text-purple-300">R</span>
                  </div>
                  <!-- Shine Effect -->
                  <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                </div>
                <!-- Glow Effect -->
                <div class="absolute inset-0 w-10 h-10 bg-blue-500/20 rounded-xl blur-md group-hover:bg-blue-500/30 transition-all duration-300"></div>
              </div>
              
              <!-- Enhanced Text Logo -->
              <div class="flex flex-col">
                <span class="text-xl font-bold text-gray-900 dark:text-white tracking-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">
                  News
                  <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent font-extrabold">
                    Reynra
                  </span>
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium tracking-wider uppercase">
                  Gaming News
                </span>
              </div>
            </Link>
          </div>

          <!-- Desktop Navigation -->
          <div class="hidden md:block">
            <div class="ml-10 flex items-baseline space-x-4">
              <NavLink :href="route('home')" :active="route().current('home')">
                Beranda
              </NavLink>
              <NavLink :href="route('articles.index')" :active="route().current('articles.*')">
                Berita
              </NavLink>
              <NavLink :href="route('articles.index', { category: 'review' })" :active="route().current('articles.*') && $page.props.filters?.category === 'review'">
                Review
              </NavLink>
              <NavLink :href="route('articles.index', { category: 'esports' })" :active="route().current('articles.*') && $page.props.filters?.category === 'esports'">
                Esports
              </NavLink>
              <NavLink :href="route('articles.index', { category: 'guide' })" :active="route().current('articles.*') && $page.props.filters?.category === 'guide'">
                Guide
              </NavLink>
              
              <!-- Platform Dropdown -->
              <Dropdown align="right" width="48">
                <template #trigger>
                  <button class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    Platform
                    <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                  </button>
                </template>
                <template #content>
                  <DropdownLink :href="route('articles.index', { platform: 'pc' })">PC</DropdownLink>
                  <DropdownLink :href="route('articles.index', { platform: 'playstation' })">PlayStation</DropdownLink>
                  <DropdownLink :href="route('articles.index', { platform: 'xbox' })">Xbox</DropdownLink>
                  <DropdownLink :href="route('articles.index', { platform: 'nintendo' })">Nintendo Switch</DropdownLink>
                  <DropdownLink :href="route('articles.index', { platform: 'mobile' })">Mobile</DropdownLink>
                </template>
              </Dropdown>
            </div>
          </div>

          <!-- Right side -->
          <div class="flex items-center space-x-4">
            <!-- Search -->
            <button 
              @click="openSearch"
              class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </button>

            <!-- Dark Mode Toggle -->
            <button 
              @click="toggleDarkMode"
              class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
            >
              <svg v-if="isDark" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
              </svg>
              <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
              </svg>
            </button>

            <!-- Auth -->
            <div v-if="$page.props.auth.user" class="flex items-center space-x-3">
              <Dropdown align="right" width="48">
                <template #trigger>
                  <button class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    {{ $page.props.auth.user.name }}
                    <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                  </button>
                </template>
                <template #content>
                  <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                  <DropdownLink :href="route('admin.dashboard')" v-if="canAccessAdmin">Admin</DropdownLink>
                  <DropdownLink :href="route('logout')" method="post" as="button">
                    Log Out
                  </DropdownLink>
                </template>
              </Dropdown>
            </div>
            <div v-else class="flex items-center space-x-2">
              <Link :href="route('login')" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                Login
              </Link>
              <Link :href="route('register')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Register
              </Link>
            </div>

            <!-- Mobile menu button -->
            <button 
              @click="mobileMenuOpen = !mobileMenuOpen"
              class="md:hidden p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Navigation -->
        <div v-show="mobileMenuOpen" class="md:hidden">
          <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 border-t border-gray-200 dark:border-gray-700">
            <ResponsiveNavLink :href="route('home')" :active="route().current('home')">
              Beranda
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('articles.index')" :active="route().current('articles.*')">
              Berita
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('articles.index', { category: 'review' })">
              Review
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('articles.index', { category: 'esports' })">
              Esports
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('articles.index', { category: 'guide' })">
              Guide
            </ResponsiveNavLink>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
      <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <!-- Brand -->
          <div class="col-span-1 md:col-span-2">
            <div class="flex items-center space-x-2 mb-4">
              <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <span class="text-xl font-bold text-gray-900 dark:text-white">News Reynra</span>
            </div>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
              Hub berita gaming terpercaya dengan kurasi berkualitas, pencarian cerdas, dan personalisasi untuk komunitas gamer Indonesia.
            </p>
            <div class="flex space-x-4">
              <a href="#" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                </svg>
              </a>
              <a href="#" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                </svg>
              </a>
              <a href="#" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419-.0189 1.3332-.9555 2.4189-2.1569 2.4189zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9554-2.4189 2.1569-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.9555 2.4189-2.1568 2.4189Z"/>
                </svg>
              </a>
            </div>
          </div>

          <!-- Quick Links -->
          <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white tracking-wider uppercase mb-4">
              Quick Links
            </h3>
            <ul class="space-y-2">
              <li><Link :href="route('articles.index')" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Berita Terbaru</Link></li>
              <li><Link :href="route('articles.index', { category: 'review' })" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Review Game</Link></li>
              <li><Link :href="route('articles.index', { category: 'esports' })" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Esports</Link></li>
              <li><Link :href="route('search')" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Pencarian</Link></li>
            </ul>
          </div>

          <!-- Legal -->
          <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white tracking-wider uppercase mb-4">
              Legal
            </h3>
            <ul class="space-y-2">
              <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Privacy Policy</a></li>
              <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Terms of Service</a></li>
              <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Contact</a></li>
              <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">About</a></li>
            </ul>
          </div>
        </div>

        <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
          <p class="text-center text-gray-500 dark:text-gray-400 text-sm">
            © {{ new Date().getFullYear() }} News Reynra. All rights reserved.
          </p>
        </div>
      </div>
    </footer>

    <!-- Search Modal -->
    <SearchModal v-if="searchModalOpen" @close="searchModalOpen = false" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import SearchModal from '@/Components/SearchModal.vue'

// Mobile menu state
const mobileMenuOpen = ref(false)
const searchModalOpen = ref(false)

// Dark mode state
const isDark = ref(false)

// Reading progress state
const readingProgress = ref(0)

// Check if user can access admin
const canAccessAdmin = computed(() => {
  return true // Temporary: allow all authenticated users
})

// Dark mode toggle
const toggleDarkMode = () => {
  isDark.value = !isDark.value
  if (isDark.value) {
    document.documentElement.classList.add('dark')
    localStorage.setItem('theme', 'dark')
  } else {
    document.documentElement.classList.remove('dark')
    localStorage.setItem('theme', 'light')
  }
}

// Open search modal
const openSearch = () => {
  searchModalOpen.value = true
}

// Update reading progress
const updateReadingProgress = () => {
  const scrollTop = window.pageYOffset
  const docHeight = document.documentElement.scrollHeight - window.innerHeight
  const progress = (scrollTop / docHeight) * 100
  readingProgress.value = Math.min(100, Math.max(0, progress))
}

// Scroll animations observer
const observeScrollAnimations = () => {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate')
        }
      })
    },
    {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    }
  )

  // Observe all elements with animation classes
  const animatedElements = document.querySelectorAll('.fade-in-up, .fade-in-left, .fade-in-right')
  animatedElements.forEach((el) => observer.observe(el))

  return observer
}

// Initialize theme and scroll listeners
onMounted(() => {
  const savedTheme = localStorage.getItem('theme')
  if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    isDark.value = true
    document.documentElement.classList.add('dark')
  }

  // Add scroll listeners
  window.addEventListener('scroll', updateReadingProgress)
  
  // Initialize scroll animations
  setTimeout(() => {
    observeScrollAnimations()
  }, 100)
})

onUnmounted(() => {
  window.removeEventListener('scroll', updateReadingProgress)
})
</script>
