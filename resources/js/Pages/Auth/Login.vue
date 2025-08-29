<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const isLoading = ref(false);

const submit = () => {
    isLoading.value = true;
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
            isLoading.value = false;
        },
    });
};

onMounted(() => {
    // Add staggered animations to form elements
    const formElements = document.querySelectorAll('.form-element');
    formElements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            el.style.transition = 'all 0.6s ease-out';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, (index + 1) * 150);
    });
});
</script>

<template>
    <GuestLayout>
        <Head title="Sign In - News Reynra" />

        <!-- Welcome Header -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-white mb-2">Welcome Back!</h2>
            <p class="text-blue-100 text-sm">Sign in to access your dashboard</p>
        </div>

        <!-- Status Message -->
        <div v-if="status" class="mb-6 p-4 rounded-xl bg-green-500/20 border border-green-400/30 backdrop-blur-sm">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium text-green-100">{{ status }}</span>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Email Field -->
            <div class="form-element">
                <div class="relative">
                    <input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        class="peer w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-transparent focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/40 backdrop-blur-sm transition-all duration-300"
                        placeholder="Email"
                    />
                    <label 
                        for="email" 
                        class="absolute left-4 -top-2.5 bg-gradient-to-r from-blue-200 to-purple-200 px-2 py-1 text-xs font-medium text-blue-900 rounded-md transition-all duration-300 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70 peer-placeholder-shown:top-4 peer-placeholder-shown:bg-transparent peer-focus:-top-2.5 peer-focus:text-xs peer-focus:text-blue-900 peer-focus:bg-gradient-to-r peer-focus:from-blue-200 peer-focus:to-purple-200"
                    >
                        Email Address
                    </label>
                </div>
                <InputError class="mt-2 text-red-300" :message="form.errors.email" />
            </div>

            <!-- Password Field -->
            <div class="form-element">
                <div class="relative">
                    <input
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        class="peer w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-transparent focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/40 backdrop-blur-sm transition-all duration-300"
                        placeholder="Password"
                    />
                    <label 
                        for="password" 
                        class="absolute left-4 -top-2.5 bg-gradient-to-r from-blue-200 to-purple-200 px-2 py-1 text-xs font-medium text-blue-900 rounded-md transition-all duration-300 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70 peer-placeholder-shown:top-4 peer-placeholder-shown:bg-transparent peer-focus:-top-2.5 peer-focus:text-xs peer-focus:text-blue-900 peer-focus:bg-gradient-to-r peer-focus:from-blue-200 peer-focus:to-purple-200"
                    >
                        Password
                    </label>
                </div>
                <InputError class="mt-2 text-red-300" :message="form.errors.password" />
            </div>

            <!-- Remember Me -->
            <div class="form-element">
                <label class="flex items-center space-x-3 cursor-pointer group">
                    <div class="relative">
                        <input
                            type="checkbox"
                            v-model="form.remember"
                            class="sr-only"
                        />
                        <div 
                            :class="[
                                'w-5 h-5 rounded border-2 transition-all duration-300 flex items-center justify-center',
                                form.remember 
                                    ? 'bg-gradient-to-r from-blue-500 to-purple-500 border-white/40' 
                                    : 'bg-white/10 border-white/30 group-hover:border-white/50'
                            ]"
                        >
                            <svg 
                                v-if="form.remember" 
                                class="w-3 h-3 text-white" 
                                fill="currentColor" 
                                viewBox="0 0 20 20"
                            >
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <span class="text-sm text-white/90 group-hover:text-white transition-colors duration-300">
                        Remember me for 30 days
                    </span>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="form-element space-y-4">
                <!-- Login Button -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full relative overflow-hidden bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-2xl disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none group"
                >
                    <!-- Loading Spinner -->
                    <div v-if="isLoading" class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-700 flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    
                    <!-- Button Content -->
                    <div :class="{ 'opacity-0': isLoading }" class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span>Sign In</span>
                    </div>

                    <!-- Shine Effect -->
                    <div class="absolute inset-0 -top-1 -left-1 bg-gradient-to-r from-transparent via-white/20 to-transparent transform -skew-x-12 group-hover:animate-shine"></div>
                </button>

                <!-- Forgot Password Link -->
                <div v-if="canResetPassword" class="text-center">
                    <Link
                        :href="route('password.request')"
                        class="inline-flex items-center space-x-1 text-sm text-blue-200 hover:text-white transition-colors duration-300 group"
                    >
                        <svg class="w-4 h-4 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        <span>Forgot your password?</span>
                    </Link>
                </div>
            </div>
        </form>

        <!-- Register Link -->
        <div class="mt-8 text-center form-element">
            <p class="text-blue-100 text-sm">
                Don't have an account? 
                <Link 
                    :href="route('register')" 
                    class="font-semibold text-white hover:text-blue-200 transition-colors duration-300 underline decoration-2 underline-offset-4 hover:decoration-blue-200"
                >
                    Create one here
                </Link>
            </p>
        </div>
    </GuestLayout>
</template>

<style scoped>
@keyframes shine {
  0% {
    transform: translateX(-100%) skewX(-12deg);
  }
  100% {
    transform: translateX(200%) skewX(-12deg);
  }
}

.animate-shine {
  animation: shine 0.8s ease-out;
}

/* Custom focus styles for better accessibility */
input:focus {
  box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
}

/* Smooth transitions for all interactive elements */
* {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}
</style>
