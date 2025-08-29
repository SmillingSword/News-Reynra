<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const isLoading = ref(false);

const submit = () => {
    isLoading.value = true;
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
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
        <Head title="Register - News Reynra" />

        <!-- Welcome Header -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-white mb-2">Join News Reynra!</h2>
            <p class="text-blue-100 text-sm">Create your account to get started</p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Name Field -->
            <div class="form-element">
                <div class="relative">
                    <input
                        id="name"
                        type="text"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        class="peer w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-transparent focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/40 backdrop-blur-sm transition-all duration-300"
                        placeholder="Name"
                    />
                    <label 
                        for="name" 
                        class="absolute left-4 -top-2.5 bg-gradient-to-r from-blue-200 to-purple-200 px-2 py-1 text-xs font-medium text-blue-900 rounded-md transition-all duration-300 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70 peer-placeholder-shown:top-4 peer-placeholder-shown:bg-transparent peer-focus:-top-2.5 peer-focus:text-xs peer-focus:text-blue-900 peer-focus:bg-gradient-to-r peer-focus:from-blue-200 peer-focus:to-purple-200"
                    >
                        Full Name
                    </label>
                </div>
                <InputError class="mt-2 text-red-300" :message="form.errors.name" />
            </div>

            <!-- Email Field -->
            <div class="form-element">
                <div class="relative">
                    <input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
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
                        autocomplete="new-password"
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

            <!-- Confirm Password Field -->
            <div class="form-element">
                <div class="relative">
                    <input
                        id="password_confirmation"
                        type="password"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        class="peer w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-transparent focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/40 backdrop-blur-sm transition-all duration-300"
                        placeholder="Confirm Password"
                    />
                    <label 
                        for="password_confirmation" 
                        class="absolute left-4 -top-2.5 bg-gradient-to-r from-blue-200 to-purple-200 px-2 py-1 text-xs font-medium text-blue-900 rounded-md transition-all duration-300 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70 peer-placeholder-shown:top-4 peer-placeholder-shown:bg-transparent peer-focus:-top-2.5 peer-focus:text-xs peer-focus:text-blue-900 peer-focus:bg-gradient-to-r peer-focus:from-blue-200 peer-focus:to-purple-200"
                    >
                        Confirm Password
                    </label>
                </div>
                <InputError class="mt-2 text-red-300" :message="form.errors.password_confirmation" />
            </div>

            <!-- Action Buttons -->
            <div class="form-element space-y-4">
                <!-- Register Button -->
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <span>Create Account</span>
                    </div>

                    <!-- Shine Effect -->
                    <div class="absolute inset-0 -top-1 -left-1 bg-gradient-to-r from-transparent via-white/20 to-transparent transform -skew-x-12 group-hover:animate-shine"></div>
                </button>
            </div>
        </form>

        <!-- Login Link -->
        <div class="mt-8 text-center form-element">
            <p class="text-blue-100 text-sm">
                Already have an account? 
                <Link 
                    :href="route('login')" 
                    class="font-semibold text-white hover:text-blue-200 transition-colors duration-300 underline decoration-2 underline-offset-4 hover:decoration-blue-200"
                >
                    Sign in here
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
