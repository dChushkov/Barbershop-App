<template>
  <div class="min-h-screen pt-24 pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Title Section -->
      <div class="text-center mb-16 relative">
        <div class="absolute inset-0 bg-gradient-to-b from-black/90 via-black/75 to-transparent -top-24 h-48 w-full"></div>
        <div class="relative">
          <h1 class="text-5xl md:text-7xl font-bold uppercase tracking-widest">
            <span class="block mb-2 text-white opacity-85 mix-blend-overlay drop-shadow-[0_2px_12px_rgba(255,255,255,0.25)]">Contact</span>
            <span class="block text-white opacity-[0.9] mix-blend-overlay drop-shadow-[0_2px_12px_rgba(255,255,255,0.25)]">Us</span>
          </h1>
        </div>
      </div>

      <!-- Contact Info and Map Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-16">
        <!-- Contact Information -->
        <div class="bg-black/60 backdrop-blur-sm p-8 rounded-2xl border border-white/10">
          <h2 class="text-3xl font-bold text-white mb-8">Get in Touch</h2>
          
          <!-- Contact Details -->
          <div class="space-y-6">
            <!-- Address -->
            <div class="flex items-start space-x-4">
              <div class="flex-shrink-0 bg-white/10 p-3 rounded-lg">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-semibold text-white">Address</h3>
                <p class="text-gray-300 mt-1">123 Main Street</p>
                <p class="text-gray-300">Sofia, Bulgaria</p>
              </div>
            </div>

            <!-- Phone -->
            <div class="flex items-start space-x-4">
              <div class="flex-shrink-0 bg-white/10 p-3 rounded-lg">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-semibold text-white">Phone</h3>
                <p class="text-gray-300 mt-1">+359 888 123 456</p>
              </div>
            </div>

            <!-- Email -->
            <div class="flex items-start space-x-4">
              <div class="flex-shrink-0 bg-white/10 p-3 rounded-lg">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-semibold text-white">Email</h3>
                <p class="text-gray-300 mt-1">info@barbershop.com</p>
              </div>
            </div>

            <!-- Hours -->
            <div class="flex items-start space-x-4">
              <div class="flex-shrink-0 bg-white/10 p-3 rounded-lg">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-semibold text-white">Working Hours</h3>
                <div class="text-gray-300 mt-1 space-y-1">
                  <p>Monday - Friday: 9am - 8pm</p>
                  <p>Saturday: 10am - 6pm</p>
                  <p>Sunday: Closed</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Google Map -->
        <div class="h-[500px] bg-black/60 backdrop-blur-sm rounded-2xl border border-white/10 overflow-hidden">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2932.5667788864897!2d23.319442776927793!3d42.697756871572325!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40aa8682cb317bf5%3A0x400a01269bf5e60!2z0KHQvtGE0LjRjw!5e0!3m2!1sbg!2sbg!4v1709305144297!5m2!1sbg!2sbg"
            width="100%"
            height="100%"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            class="filter grayscale contrast-125 brightness-75"
          ></iframe>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
</script> 