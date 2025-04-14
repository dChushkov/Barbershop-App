<template>
  <div>
    <!-- Main Background -->
    <div 
      v-if="!isAdminRoute"
      style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('/images/backgrounds/barbershop-bg.jpg') center/cover no-repeat;
        opacity: 0.8;
        z-index: -1;
      "
    ></div>

    <!-- Navbar -->
    <Navbar v-if="!isAdminRoute" />

    <!-- Main Content -->
    <main :class="{ 'min-h-screen pb-32': !isAdminRoute }">
      <router-view />
    </main>

    <!-- Footer -->
    <Footer v-if="!isAdminRoute" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import Navbar from './components/Navbar.vue';
import Footer from './components/Footer.vue';

const route = useRoute();
const isAdminRoute = computed(() => route.path.startsWith('/admin'));

const logoUrl = new URL('@/assets/images/logo.png', import.meta.url).href;
</script> 