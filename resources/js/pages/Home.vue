<template>
  <div class="min-h-screen pt-24">
    <!-- Hero Section -->
    <div class="text-center mb-16 px-4">
      <div class="max-w-md mx-auto mb-16">
        <!-- Logo with animations -->
        <div class="animate-slide-down">
          <img 
            :src="logoUrl" 
            alt="BarberShop Logo" 
            class="w-full h-auto max-w-[300px] mx-auto rotate-0 hover:rotate-360 transition-transform duration-[1.5s] ease-in-out rounded-full bg-black/60 p-6 hover:shadow-2xl animate-pulse"
          />
        </div>
      </div>
    </div>

    <!-- Features Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Expert Barbers Card -->
        <div class="group relative rounded-2xl overflow-hidden transform transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-black/50">
          <!-- Background Image -->
          <div 
            class="absolute inset-0 bg-cover bg-center transition-transform duration-300 group-hover:scale-110"
            :style="{ backgroundImage: 'url(' + barberBgUrl + ')' }"
          ></div>
          <!-- Overlay -->
          <div class="absolute inset-0 bg-black/50"></div>
          <!-- Content -->
          <div class="relative p-8 border border-gray-800/50 h-full">
            <div class="text-white mb-6">
              <!-- Person Icon -->
              <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" viewBox="0 0 448 512" fill="currentColor">
                <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-4 group-hover:text-gray-300 transition-colors duration-300">
              Expert Barbers
            </h3>
            <p class="text-gray-400 text-lg leading-relaxed">
              Our team of skilled professionals brings years of experience and passion to every cut.
            </p>
          </div>
        </div>

        <!-- Flexible Hours Card -->
        <div class="group relative rounded-2xl overflow-hidden transform transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-black/50">
          <!-- Background Image -->
          <div 
            class="absolute inset-0 bg-cover bg-center transition-transform duration-300 group-hover:scale-110"
            :style="{ backgroundImage: 'url(' + hoursBgUrl + ')' }"
          ></div>
          <!-- Overlay -->
          <div class="absolute inset-0 bg-black/40"></div>
          <!-- Content -->
          <div class="relative p-8 border border-gray-800/30 h-full">
            <div class="text-white mb-6">
              <!-- Modern Clock Icon -->
              <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10" stroke-width="1.5"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6l4 4"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2v2m10 8h-2M4 12H2m18 0a8 8 0 1 1-16 0"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-4 group-hover:text-gray-300 transition-colors duration-300">
              Flexible Hours
            </h3>
            <p class="text-gray-400 text-lg leading-relaxed">
              Open 6 days a week with extended hours to accommodate your busy schedule.
            </p>
          </div>
        </div>

        <!-- Easy Booking Card -->
        <div class="group relative rounded-2xl overflow-hidden transform transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-black/50">
          <!-- Background Image -->
          <div 
            class="absolute inset-0 bg-cover bg-center transition-transform duration-300 group-hover:scale-110"
            :style="{ backgroundImage: 'url(' + bookingBgUrl + ')' }"
          ></div>
          <!-- Overlay -->
          <div class="absolute inset-0 bg-black/40"></div>
          <!-- Content -->
          <div class="relative p-8 border border-gray-800/30 h-full">
            <div class="text-white mb-6">
              <!-- Modern Calendar Icon -->
              <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="1.5"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 2v4M8 2v4m-5 4h18M8 14h2m-2 4h2m4-4h2m-2 4h2"/>
                <circle cx="9" cy="16" r="1" fill="currentColor"/>
                <circle cx="15" cy="16" r="1" fill="currentColor"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-4 group-hover:text-gray-300 transition-colors duration-300">
              Easy Booking
            </h3>
            <p class="text-gray-400 text-lg leading-relaxed">
              Book your appointment online in just a few clicks. No more waiting in line.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- CTA Section -->
    <div class="text-center mb-16">
      <router-link
        to="/barbers"
        class="inline-flex items-center px-8 py-3 border-2 border-white text-lg font-medium text-white bg-black/30 hover:bg-white hover:text-black transition-all duration-300 transform hover:scale-105 rounded-lg"
      >
        Book Your Appointment
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </router-link>
    </div>
  </div>
</template>

<script setup>
const logoUrl = new URL('@/assets/images/logo.png', import.meta.url).href;
const barberBgUrl = new URL('@/assets/images/barber-bg.jpg', import.meta.url).href;
const hoursBgUrl = new URL('@/assets/images/hours-bg.jpg', import.meta.url).href;
const bookingBgUrl = new URL('@/assets/images/booking-bg.jpg', import.meta.url).href;
</script>

<style>
@keyframes slide-down {
  from {
    opacity: 0;
    transform: translateY(-100px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slide-down {
  animation: slide-down 1s ease-out;
}

.rotate-0 {
  transform: rotate(0deg);
}

.hover\:rotate-360:hover {
  transform: rotate(360deg);
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.2);
  }
  50% {
    box-shadow: 0 0 30px 0 rgba(255, 255, 255, 0.3);
  }
}
</style> 