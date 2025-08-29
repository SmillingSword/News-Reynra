<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';

onMounted(() => {
  // Add entrance animations
  const elements = document.querySelectorAll('.animate-fade-in-up');
  elements.forEach((el, index) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
      el.style.transition = 'all 0.8s ease-out';
      el.style.opacity = '1';
      el.style.transform = 'translateY(0)';
    }, index * 200);
  });
});
</script>

<template>
    <div class="min-h-screen relative overflow-hidden bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-700 flex items-center justify-center p-4">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-0 left-0 w-72 h-72 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute top-1/4 right-0 w-96 h-96 bg-purple-400/20 rounded-full blur-3xl animate-bounce" style="animation-duration: 4s;"></div>
            <div class="absolute bottom-0 left-1/4 w-80 h-80 bg-blue-400/15 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-indigo-400/10 rounded-full blur-3xl animate-ping" style="animation-duration: 3s;"></div>
        </div>

        <!-- Floating Particles -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-2 h-2 bg-white/30 rounded-full animate-float" style="animation-delay: 0s;"></div>
            <div class="absolute top-1/3 right-1/4 w-1 h-1 bg-white/40 rounded-full animate-float" style="animation-delay: 1s;"></div>
            <div class="absolute bottom-1/4 left-1/3 w-3 h-3 bg-white/20 rounded-full animate-float" style="animation-delay: 2s;"></div>
            <div class="absolute top-2/3 right-1/3 w-1.5 h-1.5 bg-white/35 rounded-full animate-float" style="animation-delay: 3s;"></div>
        </div>

        <!-- Main Content Container -->
        <div class="relative z-10 w-full max-w-md">
            <!-- Logo Section -->
            <div class="text-center mb-8 animate-fade-in-up">
                <Link href="/" class="inline-block">
                    <div class="relative">
                        <div class="absolute inset-0 bg-white/20 rounded-2xl blur-xl"></div>
                        <div class="relative bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/20">
                            <ApplicationLogo class="h-16 w-16 mx-auto fill-current text-white drop-shadow-lg" />
                        </div>
                    </div>
                </Link>
                <h1 class="mt-4 text-3xl font-bold text-white drop-shadow-lg">News Reynra</h1>
                <p class="mt-2 text-blue-100 text-sm">Welcome back! Please sign in to continue</p>
            </div>

            <!-- Form Container with Glassmorphism -->
            <div class="relative animate-fade-in-up" style="animation-delay: 0.2s;">
                <!-- Glassmorphism Background -->
                <div class="absolute inset-0 bg-white/10 backdrop-blur-md rounded-3xl border border-white/20 shadow-2xl"></div>
                
                <!-- Content -->
                <div class="relative p-8">
                    <slot />
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6 animate-fade-in-up" style="animation-delay: 0.4s;">
                <p class="text-blue-100 text-xs">
                    © 2024 News Reynra. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
  }
  33% {
    transform: translateY(-10px) rotate(120deg);
  }
  66% {
    transform: translateY(5px) rotate(240deg);
  }
}

.animate-fade-in-up {
  animation: fade-in-up 0.8s ease-out forwards;
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

/* Glassmorphism effect */
.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}

.backdrop-blur-md {
  backdrop-filter: blur(12px);
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.5);
}
</style>
