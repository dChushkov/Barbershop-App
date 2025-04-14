<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 w-64 bg-gray-900 text-white">
      <div class="p-6 border-b border-gray-800">
        <div class="flex items-center">
          <img :src="logoImage" alt="Logo" class="h-8 w-8 mr-2">
          <h1 class="text-xl font-semibold">Admin Panel</h1>
        </div>
      </div>
      <nav class="p-4">
        <router-link 
          to="/admin/dashboard" 
          class="block py-2.5 px-4 rounded transition duration-200 flex items-center"
          :class="{ 'bg-gray-800 text-white': $route.path === '/admin/dashboard',
                   'text-gray-400 hover:bg-gray-800 hover:text-white': $route.path !== '/admin/dashboard' }"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
          Dashboard
        </router-link>
        <router-link 
          to="/admin/barbers" 
          class="block py-2.5 px-4 rounded transition duration-200 flex items-center mt-2"
          :class="{ 'bg-gray-800 text-white': $route.path === '/admin/barbers',
                   'text-gray-400 hover:bg-gray-800 hover:text-white': $route.path !== '/admin/barbers' }"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          Barbers
        </router-link>
      </nav>
      <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-800">
        <button 
          @click="handleLogout" 
          class="w-full flex items-center justify-center px-4 py-2 text-sm text-gray-400 hover:text-white hover:bg-gray-800 rounded transition duration-200"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          Logout
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="ml-64">
      <header class="bg-white shadow-sm">
        <div class="px-6 py-4">
          <h2 class="text-xl font-semibold text-gray-800">{{ currentPage }}</h2>
        </div>
      </header>
      <main class="p-6">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import logoImage from '@/assets/images/logo.png'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const currentPage = computed(() => {
  const pathMap = {
    '/admin/dashboard': 'Dashboard',
    '/admin/barbers': 'Barbers'
  }
  return pathMap[route.path] || 'Dashboard'
})

const handleLogout = async () => {
  await authStore.logout()
  await router.push('/admin/login')
}
</script> 