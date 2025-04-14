<template>
  <div class="min-h-screen pt-24 pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Title Section -->
      <div class="text-center mb-16 relative">
        <div class="absolute inset-0 bg-gradient-to-b from-black/90 via-black/75 to-transparent -top-24 h-48 w-full"></div>
        <div class="relative">
          <h1 class="text-5xl md:text-7xl font-bold uppercase tracking-widest">
            <span class="block mb-2 text-[#ffffff] opacity-85 mix-blend-overlay drop-shadow-[0_2px_12px_rgba(255,255,255,0.25)]">Meet Our</span>
            <span class="block text-[#ffffff] opacity-[0.9] mix-blend-overlay drop-shadow-[0_2px_12px_rgba(255,255,255,0.25)]">Expert Barbers</span>
          </h1>
        </div>
      </div>

      <!-- Barbers Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="barber in barbers" :key="barber.id" class="h-[550px] perspective-1000">
          <!-- Flip Card Container -->
          <div class="card-container">
            
            <!-- Front Side - Barber Image -->
            <div class="card-front">
              <!-- Barber Image -->
              <div class="relative h-[400px] overflow-hidden">
                <img 
                  :src="getBarberImagePath(barber)" 
                  :alt="barber.name"
                  class="w-full h-full object-cover object-center transform transition-all duration-500 group-hover:scale-105"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent"></div>
              </div>

              <!-- Barber Info -->
              <div class="p-6 flex flex-col">
                <h3 class="text-2xl font-semibold text-white mb-6">{{ barber.name }}</h3>
                
                <!-- Book Button -->
                <button 
                  @click.stop="openBookingModal(barber)"
                  class="w-full px-4 py-3 bg-white text-black rounded-lg hover:bg-gray-100 transition-colors">
                  Book Appointment
                </button>
              </div>
            </div>
            
            <!-- Back Side - Bio Information -->
            <div class="card-back">
              <div>
                <h3 class="text-2xl font-semibold text-white mb-4">{{ barber.name }}</h3>
                <div class="w-16 h-1 bg-white/30 mb-6"></div>
                
                <p class="text-gray-300 mb-4 text-lg">{{ barber.bio || getDefaultBio(barber.name) }}</p>
                
                <div class="mt-6">
                  <h4 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-3">Specialties</h4>
                  <div class="flex flex-wrap gap-2">
                    <span v-for="(specialty, index) in getSpecialties(barber)" :key="index"
                      class="px-3 py-1 bg-black/40 text-gray-300 rounded-full text-sm border border-white/10">
                      {{ specialty }}
                    </span>
                  </div>
                </div>
              </div>
              
              <!-- Book Button -->
              <button 
                @click.stop="openBookingModal(barber)"
                class="w-full px-4 py-3 bg-white text-black rounded-lg hover:bg-gray-100 transition-colors mt-6">
                Book Appointment
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Booking Modal -->
  <BookingModal 
    :is-open="showBookingModal"
    :selected-barber="selectedBarber"
    :pre-selected-service-id="preSelectedServiceId"
    @close="closeBookingModal"
  />
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import BookingModal from '@/components/BookingModal.vue';
import { barberApi } from '@/services/api';

const router = useRouter();
const route = useRoute();
const showBookingModal = ref(false);
const selectedBarber = ref(null);
const barbers = ref([]);

// Get preSelectedServiceId from route query if available
const preSelectedServiceId = computed(() => {
  const serviceId = route.query.preSelectedServiceId;
  return serviceId ? parseInt(serviceId) : null;
});

onMounted(async () => {
  try {
    const response = await barberApi.getBarbers();
    barbers.value = response.data.map(barber => ({
      id: barber.id,
      name: barber.name,
      title: barber.title || 'Professional Barber',
      bio: barber.bio || '',
      image: barber.image_url || '/images/barbers/default.jpg',
      specialties: barber.specialties || [],
      services: barber.services.map(service => ({
        id: service.id,
        name: service.name,
        price: service.price,
        duration: service.duration_minutes,
        requiresTwoSlots: service.duration_minutes === 60
      }))
    }));
    console.log('Barber data structure:', barbers.value);
    
    // If there's a preSelectedServiceId in the URL and barbers are loaded, 
    // auto-open the booking modal for the first barber
    if (preSelectedServiceId.value && barbers.value.length > 0) {
      // You could show a message suggesting to select a barber instead
      // or automatically open the booking modal for the first barber
      console.log('Pre-selected service ID:', preSelectedServiceId.value);
    }
  } catch (error) {
    console.error('Error fetching barbers:', error);
  }
});

const openBookingModal = (barber) => {
  selectedBarber.value = barber;
  showBookingModal.value = true;
};

const closeBookingModal = () => {
  showBookingModal.value = false;
  selectedBarber.value = null;
};

// Barber image paths function
const getBarberImagePath = (barber) => {
  if (!barber || !barber.id) {
    console.error('Invalid barber data:', barber);
    return '/images/barbers/default-barber.jpg';
  }
  
  console.log('Getting image for barber ID:', barber.id);
  
  // Use specific file names for each barber ID
  switch (parseInt(barber.id)) {
    case 1:
      return '/images/barbers/barber1.jpg';
    case 2:
      return '/images/barbers/barber2.jpg';
    case 3:
      return '/images/barbers/barber3.jpg';
    default:
      console.warn('Unknown barber ID:', barber.id);
      return '/images/barbers/default-barber.jpg';
  }
};

// Get default bio for barbers
const getDefaultBio = (name) => {
  switch (name) {
    case 'John Smith':
      return 'Experienced barber with over 10 years of expertise in classic and modern cuts. Known for precision fades and razor shaves.';
    case 'Mike Johnson':
      return 'Specialized in beard grooming and styling. Expert in creating personalized looks that enhance your best features.';
    case 'David Wilson':
      return 'Young and talented barber with fresh ideas and modern techniques. Specializes in contemporary styles and creative fades.';
    default:
      return 'Professional barber with passion for the craft and attention to detail. Dedicated to delivering the best haircut experience.';
  }
};

// Get specialties for each barber
const getSpecialties = (barber) => {
  if (barber.specialties && barber.specialties.length > 0) {
    return barber.specialties;
  }
  
  switch (barber.name) {
    case 'John Smith':
      return ['Classic Cuts', 'Precision Fades', 'Hot Towel Shaves'];
    case 'Mike Johnson':
      return ['Beard Styling', 'Luxury Shaves', 'Hair Design'];
    case 'David Wilson':
      return ['Contemporary Styles', 'Creative Fades', 'Texture Work'];
    default:
      return ['Haircuts', 'Beard Trims', 'Styling'];
  }
};
</script>

<style scoped>
.perspective-1000 {
  perspective: 1000px;
}

.transform-style-3d {
  transform-style: preserve-3d;
}

.backface-hidden {
  backface-visibility: hidden;
}

.rotate-y-180 {
  transform: rotateY(180deg);
}

/* Fix for card flipping */
.card-container {
  position: relative;
  width: 100%;
  height: 100%;
  transition: transform 0.8s;
  transform-style: preserve-3d;
}

.card-container:hover {
  transform: rotateY(180deg);
}

.card-front, .card-back {
  position: absolute;
  width: 100%;
  height: 100%;
  -webkit-backface-visibility: hidden; /* Safari */
  backface-visibility: hidden;
  border-radius: 0.5rem;
}

.card-front {
  background-color: #1a1a1a;
  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.card-back {
  background-color: #1a1a1a;
  border: 1px solid rgba(255, 255, 255, 0.1);
  transform: rotateY(180deg);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 2rem;
}
</style> 