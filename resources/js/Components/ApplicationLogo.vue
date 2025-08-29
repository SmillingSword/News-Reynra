<template>
    <!-- NR Logo with Container -->
    <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl shadow-xl flex items-center justify-center relative overflow-hidden group">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
        
        <!-- NR Text -->
        <div class="relative z-10 font-black text-2xl tracking-tighter leading-none text-white">
            <span class="text-blue-100">N</span><span class="text-purple-200">R</span>
        </div>
        
        <!-- Shine Effect -->
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
        
        <!-- Glow Effect -->
        <div class="absolute inset-0 bg-white/10 rounded-2xl blur-md group-hover:bg-white/20 transition-all duration-300"></div>
    </div>
</template>
