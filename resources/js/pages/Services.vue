<template>
  <div class="min-h-screen pt-24 pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Title Section -->
      <div class="text-center mb-16 relative">
        <div class="absolute inset-0 bg-gradient-to-b from-black/90 via-black/75 to-transparent -top-24 h-48 w-full"></div>
        <div class="relative">
          <h1 class="text-5xl md:text-7xl font-bold uppercase tracking-widest">
            <span class="block mb-2 text-white opacity-85 mix-blend-overlay drop-shadow-[0_2px_12px_rgba(255,255,255,0.25)]">Our</span>
            <span class="block text-white opacity-[0.9] mix-blend-overlay drop-shadow-[0_2px_12px_rgba(255,255,255,0.25)]">Services</span>
          </h1>
        </div>
      </div>

      <p class="text-center text-gray-300 max-w-3xl mx-auto mb-12">
        Choose from our range of professional barbering services. Our experienced barbers use premium products to deliver exceptional results every time.
      </p>

      <!-- Loading state -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>
      </div>

      <!-- Error state -->
      <div v-else-if="error" class="bg-red-900/50 p-6 rounded-md text-center">
        <p class="text-red-300">{{ error }}</p>
      </div>

      <!-- Services grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div v-for="service in services" :key="service.id" 
             class="group relative rounded-2xl overflow-hidden transform transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-white/10">
          <!-- Service Image Background -->
          <div class="absolute inset-0 bg-cover bg-center brightness-110 contrast-105 transition-transform duration-500 group-hover:scale-110 group-hover:brightness-125"
            :style="{ backgroundImage: `url(${getServiceImage(service.id)})` }">
          </div>
          <!-- Overlay gradient -->
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent group-hover:opacity-50 transition-opacity"></div>
          
          <!-- Content -->
          <div class="relative p-8 h-full flex flex-col">
            <!-- Service Title -->
            <h3 class="text-3xl font-bold text-white mb-3 transform transition-transform duration-300 group-hover:translate-x-2 drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
              {{ service.name }}
            </h3>
            
            <!-- Service Details -->
            <div class="bg-black/30 backdrop-blur-sm rounded-lg p-4 mt-6 transform transition-all duration-300 group-hover:scale-105 shadow-lg">
              <p class="text-white mb-4 line-clamp-3 group-hover:line-clamp-none transition-all">
                {{ service.description || getDefaultDescription(service.name) }}
              </p>
              
              <div class="flex flex-wrap justify-between items-center mt-auto pt-4">
                <div class="flex items-center text-sm text-gray-200 mb-4 md:mb-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>{{ service.duration_minutes }} minutes</span>
                </div>
                <span class="text-xl font-bold text-white px-3 py-1 rounded-full bg-black/40 border border-white/30 shadow-xl">
                  {{ service.base_price }} €
                </span>
              </div>
            </div>

            <!-- Book Button - Positioned at bottom -->
            <button @click="bookService(service)"
                    class="mt-6 w-full bg-white/20 hover:bg-white text-white hover:text-black border border-white/50 
                          py-3 px-4 rounded-lg font-medium transition-all duration-300 
                          backdrop-blur-sm flex items-center justify-center transform
                          group-hover:translate-y-0 group-hover:opacity-100 shadow-lg shadow-black/20 hover:shadow-white/20">
              <span>Book Now</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { serviceApi } from '@/services/api';
import axios from 'axios';

const router = useRouter();
const services = ref([]);
const loading = ref(true);
const error = ref(null);

// Load services data from API
onMounted(async () => {
  try {
    // Call the API to get services from the database
    const response = await axios.get('/api/services');
    if (response.status !== 200) {
      throw new Error('Failed to fetch services');
    }
    
    console.log('Services loaded from API:', response.data);
    services.value = response.data;
    
    // If no data is returned, use fallback data
    if (!services.value || services.value.length === 0) {
      console.warn('No services returned from API, using fallback data');
      useFallbackData();
    }
  } catch (e) {
    console.error('Error fetching services:', e);
    error.value = 'Failed to load services. Please try again later.';
    useFallbackData();
  } finally {
    loading.value = false;
  }
});

// Function to load fallback service data if API fails
const useFallbackData = () => {
  services.value = [
    {
      id: 1, 
      name: "Haircut", 
      base_price: 25, 
      duration_minutes: 30,
      description: "Classic haircut service tailored to your preferences. Includes consultation, washing, cutting, and styling for a fresh, clean look."
    },
    {
      id: 2, 
      name: "Beard Trim", 
      base_price: 15, 
      duration_minutes: 30,
      description: "Professional beard grooming service. Includes trimming, shaping, and finishing with premium beard products for a well-maintained appearance."
    },
    {
      id: 3, 
      name: "Hot Towel Shave", 
      base_price: 30, 
      duration_minutes: 30,
      description: "Traditional hot towel shave experience. Includes pre-shave oil, hot towel treatment, straight razor shave, and after-shave balm for ultimate relaxation."
    },
    {
      id: 4, 
      name: "Haircut & Beard", 
      base_price: 35, 
      duration_minutes: 60,
      description: "Complete package including haircut and beard trim. Perfect for a full style refresh, with extra attention to detail for both hair and facial hair."
    }
  ];
};

// Function to generate description if missing in the database
const getDefaultDescription = (serviceName) => {
  switch (serviceName) {
    case 'Haircut':
      return "Classic haircut service tailored to your preferences. Includes consultation, washing, cutting, and styling for a fresh, clean look.";
    case 'Beard Trim':
      return "Professional beard grooming service. Includes trimming, shaping, and finishing with premium beard products for a well-maintained appearance.";
    case 'Hot Towel Shave':
      return "Traditional hot towel shave experience. Includes pre-shave oil, hot towel treatment, straight razor shave, and after-shave balm for ultimate relaxation.";
    case 'Haircut & Beard':
      return "Complete package including haircut and beard trim. Perfect for a full style refresh, with extra attention to detail for both hair and facial hair.";
    default:
      return "Professional barbering service performed by our skilled team using premium products.";
  }
};

// Function to get the appropriate image for each service
const getServiceImage = (serviceId) => {
  switch (parseInt(serviceId)) {
    case 1:
      return '/images/services/haircut.jpg';
    case 2:
      return '/images/services/beard-trim.jpg';
    case 3:
      return '/images/services/hot-towel-shave.jpg';
    case 4:
      return '/images/services/haircut-and-beard.jpg';
    default:
      return '/images/services/default-service.jpg';
  }
};

// Redirect to barbers page with pre-selected service
const bookService = (service) => {
  router.push({
    name: 'barbers',
    query: { preSelectedServiceId: service.id }
  });
};
</script> 