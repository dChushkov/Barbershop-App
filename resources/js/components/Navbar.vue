<template>
  <nav class="bg-black/60 backdrop-blur-sm shadow-lg fixed w-full z-50 border-b border-white/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-20">
        <!-- Logo -->
        <div class="flex-shrink-0 flex items-center">
          <router-link to="/" class="flex items-center space-x-3">
            <img 
              :src="logoUrl" 
              alt="BarberShop Logo" 
              class="h-16 w-16 rounded-full bg-black/80 p-1.5"
            />
            <span class="text-3xl font-bold text-white hover:text-gray-200 transition-colors duration-300">
              BarberShop
            </span>
          </router-link>
        </div>

        <!-- Navigation Links -->
        <div class="hidden sm:flex sm:items-center sm:space-x-8">
          <router-link 
            v-for="(item, index) in navItems"
            :key="index"
            :to="item.path"
            class="relative px-3 py-2 text-lg font-medium transition-colors duration-300 hover:text-gray-300"
            :class="[
              $route.path === item.path 
                ? 'text-white after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-full after:bg-white' 
                : 'text-gray-400'
            ]"
          >
            {{ item.name }}
          </router-link>
        </div>

        <!-- Mobile menu button -->
        <div class="flex items-center sm:hidden">
          <button 
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-800 focus:outline-none"
          >
            <span class="sr-only">Open main menu</span>
            <svg 
              class="h-6 w-6" 
              :class="{'hidden': mobileMenuOpen, 'block': !mobileMenuOpen}"
              xmlns="http://www.w3.org/2000/svg" 
              fill="none" 
              viewBox="0 0 24 24" 
              stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg 
              class="h-6 w-6" 
              :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen}"
              xmlns="http://www.w3.org/2000/svg" 
              fill="none" 
              viewBox="0 0 24 24" 
              stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div 
      class="sm:hidden bg-black/95"
      :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen}"
    >
      <div class="pt-2 pb-3 space-y-1">
        <router-link
          v-for="(item, index) in navItems"
          :key="index"
          :to="item.path"
          class="block px-3 py-2 text-base font-medium transition-colors duration-300"
          :class="[
            $route.path === item.path
              ? 'text-white bg-gray-900'
              : 'text-gray-400 hover:text-white hover:bg-gray-800'
          ]"
          @click="mobileMenuOpen = false"
        >
          {{ item.name }}
        </router-link>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue';

const mobileMenuOpen = ref(false);
const logoUrl = new URL('@/assets/images/logo.png', import.meta.url).href;

const navItems = [
  { name: 'Home', path: '/' },
  { name: 'Our Barbers', path: '/barbers' },
  { name: 'Services', path: '/services' },
  { name: 'Contacts', path: '/contacts' }
];
</script> 