<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal -->
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
      <div class="relative transform overflow-hidden rounded-lg bg-[#1a1a1a] border border-white/10 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-white/10">
          <div class="flex items-center justify-between">
            <h3 class="text-2xl font-semibold text-white">
              Book Appointment
            </h3>
            <button @click="closeModal" class="text-gray-400 hover:text-white transition-colors">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Barber Info -->
          <div class="flex items-center mt-4 space-x-4" v-if="selectedBarber">
            <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-white/10 bg-white/5">
              <img 
                :src="getBarberImage(selectedBarber)"
                :alt="selectedBarber.name"
                class="w-full h-full object-cover"
                @error="handleImageError"
              />
            </div>
            <div>
              <h4 class="text-lg font-medium text-white">{{ selectedBarber.name }}</h4>
              <p class="text-sm text-gray-400">Professional Barber</p>
            </div>
          </div>

          <!-- Progress Steps -->
          <div class="flex justify-between mt-6">
            <div v-for="(step, index) in steps" :key="index" 
              class="flex items-center">
              <div class="flex items-center justify-center w-8 h-8 rounded-full transition-colors duration-300"
                :class="[
                  currentStep > index 
                    ? 'bg-green-500 text-white' 
                    : currentStep === index 
                      ? 'bg-white text-black'
                      : 'bg-white/10 text-gray-400'
                ]">
                {{ index + 1 }}
              </div>
              <span class="ml-2 text-sm"
                :class="[
                  currentStep === index 
                    ? 'text-white font-medium' 
                    : 'text-gray-400'
                ]">
                {{ step }}
              </span>
              <div v-if="index < steps.length - 1" 
                class="w-12 h-px mx-4 bg-white/10"></div>
            </div>
          </div>
        </div>

        <!-- Modal Content -->
        <div class="px-6 py-6">
          <!-- Step 1: Service Selection -->
          <div v-if="currentStep === 0" class="space-y-6">
            <div v-if="isLoadingServices" class="text-center py-4">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-white"></div>
              <p class="mt-2 text-gray-400">Loading services...</p>
            </div>
            
            <div v-else-if="serviceError" class="text-center py-4 text-red-500">
              {{ serviceError }}
            </div>
            
            <div v-else-if="!barberServices.length" class="text-center py-4 text-gray-400">
              No services available for this barber.
            </div>
            
            <div v-else v-for="service in barberServices" :key="service.id"
              class="flex items-center justify-between p-4 rounded-lg border transition-colors cursor-pointer"
              :class="[
                selectedService?.id === service.id 
                  ? 'bg-white/10 border-white' 
                  : 'border-white/10 hover:border-white/20 bg-black/20'
              ]"
              @click="selectService(service)">
              <div class="flex flex-col text-white">
                <span class="text-lg font-semibold">{{ service.name }}</span>
                <span class="text-sm text-gray-400">
                  {{ service.duration_minutes || 30 }} minutes
                </span>
              </div>
              <div class="flex items-center space-x-4">
                <div class="text-right">
                  <p class="text-lg font-medium text-white">{{ service.base_price || 30 }} €</p>
                </div>
                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center"
                  :class="[
                    selectedService?.id === service.id 
                      ? 'border-white bg-white' 
                      : 'border-white/50'
                  ]"
                >
                  <div v-if="selectedService?.id === service.id" class="w-3 h-3 bg-black rounded-full"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 2: Date Selection -->
          <div v-if="currentStep === 1" class="space-y-6">
            <div class="relative">
              <label class="block text-sm font-medium text-gray-300 mb-2">
                Select Date
              </label>
              <input
                type="date"
                v-model="selectedDateStr"
                :min="minDateStr"
                class="w-full px-4 py-2 bg-white/10 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-white/10 focus:border-white/30 transition-colors text-white"
                @change="handleDateChange"
              />
              <p v-if="showDateError" class="text-red-500 text-sm mt-2">
                Please select a date to continue
              </p>
            </div>
          </div>

          <!-- Step 3: Time Selection -->
          <div v-if="currentStep === 2" class="space-y-4">
            <div v-if="bookingStore.isLoading" class="text-center py-8">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-white"></div>
              <p class="mt-2 text-gray-400">Loading available times...</p>
            </div>
            
            <div v-else-if="bookingStore.error" class="text-center py-8">
              <p class="text-red-500">{{ bookingStore.error }}</p>
              <button 
                @click="retryFetchTimeSlots"
                class="mt-4 px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors"
              >
                Retry
              </button>
            </div>

            <div v-else-if="!bookingStore.availableTimeSlots?.length" class="text-center py-8">
              <p class="text-gray-400">No available time slots for this date.</p>
              <p class="text-sm text-gray-500 mt-2">Please try selecting a different date.</p>
            </div>

            <div v-else class="grid grid-cols-3 gap-3 max-h-[400px] overflow-y-auto p-2">
              <button v-for="(slot, index) in bookingStore.availableTimeSlots" :key="slot.time"
                class="p-3 text-center rounded-lg border transition-colors relative"
                :class="[
                  slot.isAvailable 
                    ? isSlotSelected(slot.time, index)
                      ? 'bg-white text-black border-white' 
                      : 'border-white/10 bg-black/20 text-white hover:bg-white/10'
                    : 'border-red-500/50 bg-red-900/30 text-red-200 cursor-not-allowed'
                ]"
                :disabled="!slot.isAvailable || !isSlotValidForService(slot, index)"
                @click="slot.isAvailable && isSlotValidForService(slot, index) && selectTimeWithDuration(slot, index)"
              >
                {{ formatTimeSlot(slot.time) }}
                <div v-if="!slot.isAvailable" class="text-xs text-red-300 mt-1">Booked</div>
                <div v-else-if="selectedService?.duration_minutes > 30 && !hasEnoughConsecutiveSlots(index)" 
                  class="text-xs text-orange-400 mt-1">Not enough time</div>
                
                <!-- Show which slots are needed for longer services on hover -->
                <div v-if="selectedService?.duration_minutes > 30 && slot.isAvailable && hasEnoughConsecutiveSlots(index)" 
                  class="absolute inset-0 bg-black/70 text-white opacity-0 hover:opacity-100 flex items-center justify-center rounded-lg transition-opacity">
                  <span class="text-xs">
                    Uses {{ Math.ceil(selectedService.duration_minutes / 30) }} slots
                    <br>
                    ({{ formatTimeSlot(slot.time) }} - 
                    {{ getEndTimeForSlot(slot.time, selectedService.duration_minutes) }})
                  </span>
                </div>
              </button>
            </div>
            
            <!-- Debug button - only in development -->
            <div v-if="false && bookingStore.availableTimeSlots?.length" class="mt-4 text-center">
              <button 
                @click="debugTimeSlots"
                class="text-xs text-gray-500 hover:text-white"
              >
                Debug Slots
              </button>
            </div>
            
            <p v-if="showTimeError" class="text-red-500 text-sm mt-2">
              Please select a time to continue
            </p>
          </div>

          <!-- Step 4: Confirmation -->
          <div v-if="currentStep === 3" class="space-y-6">
            <!-- Booking Summary -->
            <div class="bg-black/20 rounded-lg p-6 border border-white/10">
              <h4 class="text-lg font-medium text-white mb-4">Booking Summary</h4>
              <div class="space-y-2 text-gray-300">
                <p><span class="text-gray-400">Service:</span> {{ selectedService?.name }}</p>
                <p><span class="text-gray-400">Duration:</span> {{ selectedService?.duration_minutes || 30 }} minutes</p>
                <p><span class="text-gray-400">Barber:</span> {{ selectedBarber?.name }}</p>
                <p><span class="text-gray-400">Date:</span> {{ formattedDate }}</p>
                <p><span class="text-gray-400">Time:</span> {{ selectedTime ? formatTimeSlot(selectedTime) : '' }}</p>
                <p><span class="text-gray-400">Price:</span> {{ formattedPrice }}</p>
              </div>
            </div>

            <!-- Customer Details Form -->
            <div class="space-y-4">
              <h4 class="text-lg font-medium text-white">Your Details</h4>
              
              <div class="space-y-4">
                <!-- Name Input -->
                <div>
                  <label class="block text-sm font-medium text-gray-300 mb-1">
                    Name *
                  </label>
                  <input 
                    v-model="customerForm.name"
                    type="text"
                    class="w-full px-4 py-2 bg-black/20 border rounded-lg focus:outline-none focus:ring-2 transition-colors"
                    :class="[
                      formErrors.name 
                        ? 'border-red-500 focus:ring-red-500/20' 
                        : 'border-white/10 focus:border-white/30 focus:ring-white/10'
                    ]"
                    placeholder="Enter your name"
                  />
                  <p v-if="formErrors.name" class="mt-1 text-sm text-red-500">
                    {{ formErrors.name }}
                  </p>
                </div>

                <!-- Email Input -->
                <div>
                  <label class="block text-sm font-medium text-gray-300 mb-1">
                    Email *
                  </label>
                  <input 
                    v-model="customerForm.email"
                    type="email"
                    class="w-full px-4 py-2 bg-black/20 border rounded-lg focus:outline-none focus:ring-2 transition-colors"
                    :class="[
                      formErrors.email 
                        ? 'border-red-500 focus:ring-red-500/20' 
                        : 'border-white/10 focus:border-white/30 focus:ring-white/10'
                    ]"
                    placeholder="Enter your email"
                  />
                  <p v-if="formErrors.email" class="mt-1 text-sm text-red-500">
                    {{ formErrors.email }}
                  </p>
                </div>

                <!-- Phone Input -->
                <div>
                  <label class="block text-sm font-medium text-gray-300 mb-1">
                    Phone *
                  </label>
                  <input 
                    v-model="customerForm.phone"
                    type="tel"
                    class="w-full px-4 py-2 bg-black/20 border rounded-lg focus:outline-none focus:ring-2 transition-colors"
                    :class="[
                      formErrors.phone 
                        ? 'border-red-500 focus:ring-red-500/20' 
                        : 'border-white/10 focus:border-white/30 focus:ring-white/10'
                    ]"
                    placeholder="Enter your phone number"
                  />
                  <p v-if="formErrors.phone" class="mt-1 text-sm text-red-500">
                    {{ formErrors.phone }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-white/10 flex justify-between">
          <button 
            v-if="currentStep > 0"
            @click="currentStep--"
            class="px-4 py-2 text-white bg-black/40 hover:bg-black/60 rounded-lg transition-colors">
            Back
          </button>
          <button 
            v-if="currentStep < steps.length - 1"
            @click="nextStep"
            :disabled="!canContinue"
            class="px-6 py-2 rounded-lg transition-colors ml-auto"
            :class="[
              canContinue
                ? 'bg-white text-black hover:bg-gray-100 cursor-pointer'
                : 'bg-gray-600 text-gray-400 cursor-not-allowed opacity-50'
            ]">
            Continue
          </button>
          <button 
            v-else
            @click="confirmBooking"
            :disabled="!isFormValid || isSubmitting"
            class="px-6 py-2 rounded-lg transition-colors ml-auto"
            :class="[
              isFormValid && !isSubmitting
                ? 'bg-green-500 text-white hover:bg-green-600 cursor-pointer'
                : 'bg-gray-600 text-gray-400 cursor-not-allowed opacity-50'
            ]">
            {{ isSubmitting ? 'Processing...' : 'Confirm Booking' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch, reactive, computed, h, onMounted } from 'vue';
import { useBookingStore } from '@/stores/bookingStore';
import { storeToRefs } from 'pinia';
import { useToast } from 'vue-toastification';
import { format, startOfDay, isValid } from 'date-fns';
import { barberApi, serviceApi, bookingApi } from '@/services/api';
import axios from 'axios';

const props = defineProps({
  isOpen: Boolean,
  selectedBarber: {
    type: Object,
    required: true
  },
  preSelectedServiceId: {
    type: Number,
    default: null
  }
});

const emit = defineEmits(['close']);
const bookingStore = useBookingStore();
const { availableTimeSlots, isLoading, error } = storeToRefs(bookingStore);

const toast = useToast();
const customerForm = reactive({
  name: '',
  email: '',
  phone: '',
  notes: ''
});

const formErrors = reactive({
  name: '',
  email: '',
  phone: ''
});

const steps = ['Select Service', 'Choose Date', 'Choose Time', 'Confirm'];
const currentStep = ref(0);
const selectedService = ref(null);
const selectedDate = ref(null);
const selectedTime = ref(null);
const showDateError = ref(false);
const showTimeError = ref(false);
const showServiceError = ref(false);

// Update date handling
const selectedDateStr = ref('');
const minDateStr = computed(() => {
  const today = new Date();
  return today.toISOString().split('T')[0];
});

const handleDateChange = (event) => {
  const dateStr = event.target.value;
  if (!dateStr) {
    selectedDate.value = null;
    showDateError.value = true;
    return;
  }
  
  showDateError.value = false;
  const date = new Date(dateStr);
  if (isValid(date)) {
    selectedDate.value = startOfDay(date).getTime();
    console.log('Date changed to:', dateStr, 'timestamp:', selectedDate.value);
    
    // Clear previously selected time when date changes
    selectedTime.value = null;
  } else {
    selectedDate.value = null;
    console.error('Invalid date selected:', dateStr);
    toast.error('Please select a valid date');
  }
};

// Update selectedDateDate computed property
const selectedDateDate = computed({
  get: () => {
    if (!selectedDate.value || isNaN(selectedDate.value)) {
      return '';
    }
    const date = new Date(selectedDate.value);
    if (!isValid(date)) {
      return '';
    }
    return date.toISOString().split('T')[0];
  },
  set: (newDate) => {
    if (!newDate) {
      selectedDate.value = null;
      return;
    }
    const date = new Date(newDate);
    if (isValid(date)) {
      selectedDate.value = startOfDay(date).getTime();
    } else {
      selectedDate.value = null;
    }
  }
});

// Function to load time slots
const loadTimeSlots = async (barberId, date) => {
  if (!barberId || !date) {
    console.error('Missing required parameters:', { barberId, date });
    return;
  }

  try {
    bookingStore.isLoading = true;
    bookingStore.error = null;
    
    console.log('Fetching time slots for barber:', barberId, 'date:', date);
    const response = await bookingApi.getAvailableTimeSlots(barberId, date);
    
    if (!response.slots || !Array.isArray(response.slots)) {
      throw new Error('Invalid response format from API');
    }
    
    // Convert time slots to Bulgarian time
    const slotsInBulgarianTime = response.slots.map(slot => ({
      ...slot,
      time: new Date(`${date}T${slot.time}`).toLocaleTimeString('bg-BG', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
        timeZone: 'Europe/Sofia'
      })
    }));
    
    bookingStore.availableTimeSlots = slotsInBulgarianTime;
    console.log('Loaded time slots (Bulgarian time):', slotsInBulgarianTime);
    
    // Log booked slots for debugging
    const bookedSlots = slotsInBulgarianTime.filter(slot => !slot.isAvailable);
    console.log('Booked slots (Bulgarian time):', bookedSlots.map(slot => slot.time));
  } catch (error) {
    console.error('Error loading time slots:', error);
    bookingStore.error = error.message || 'Could not load appointments. Please try again.';
    
    // Clear existing slots
    bookingStore.availableTimeSlots = [];
  } finally {
    bookingStore.isLoading = false;
  }
};

// Watch for date changes to load time slots
watch(selectedDate, async (newDate) => {
  if (!newDate || isNaN(newDate) || !props.selectedBarber?.id) return;
  
  const dateObj = new Date(newDate);
  if (!isValid(dateObj)) return;
  
  // Clear selected time when date changes
  selectedTime.value = null;
  
  // Format date in UTC to avoid timezone issues
  const formattedDate = format(dateObj, 'yyyy-MM-dd');
  await loadTimeSlots(props.selectedBarber.id, formattedDate);
});

// Retry function now just calls loadTimeSlots
const retryFetchTimeSlots = async () => {
  if (!selectedDate.value || !props.selectedBarber?.id) return;
  
  const dateObj = new Date(selectedDate.value);
  if (!isValid(dateObj)) return;
  
  const formattedDate = format(dateObj, 'yyyy-MM-dd');
  await loadTimeSlots(props.selectedBarber.id, formattedDate);
};

const barberServices = ref([]);
const isLoadingServices = ref(false);
const serviceError = ref(null);

// Load barber services when component mounts
onMounted(() => {
  console.log('Booking Modal mounted with props:', props);
  console.log('Selected barber in props:', props.selectedBarber);

  if (props.selectedBarber?.id) {
    isLoadingServices.value = true;
    serviceError.value = null;
    
    try {
      console.log('Loading services for barber:', props.selectedBarber.id);
      // Hardcoded services that match the database structure
      barberServices.value = [
        { 
          id: 1, 
          name: 'Haircut', 
          duration_minutes: 30, 
          base_price: 25,
          description: "Classic haircut service tailored to your preferences."
        },
        { 
          id: 2, 
          name: 'Beard Trim', 
          duration_minutes: 30, 
          base_price: 15,
          description: "Professional beard grooming service."
        },
        { 
          id: 3, 
          name: 'Hot Towel Shave', 
          duration_minutes: 30, 
          base_price: 30,
          description: "Traditional hot towel shave experience."
        },
        { 
          id: 4, 
          name: 'Haircut & Beard', 
          duration_minutes: 60, 
          base_price: 35,
          description: "Complete package including haircut and beard trim."
        }
      ];
    } catch (error) {
      console.error('Error loading barber services:', error);
      serviceError.value = null; // No error since we're using hardcoded data
    } finally {
      isLoadingServices.value = false;
    }
  }
});

// Watch for changes in selectedBarber
watch(() => props.selectedBarber?.id, async (newBarberId) => {
  if (newBarberId) {
    isLoadingServices.value = true;
    serviceError.value = null;
    
    try {
      console.log('Reloading services for barber:', newBarberId);
      // Hardcoded services matching the database structure
      barberServices.value = [
        { 
          id: 1, 
          name: 'Haircut', 
          duration_minutes: 30, 
          base_price: 25,
          description: "Classic haircut service tailored to your preferences."
        },
        { 
          id: 2, 
          name: 'Beard Trim', 
          duration_minutes: 30, 
          base_price: 15,
          description: "Professional beard grooming service."
        },
        { 
          id: 3, 
          name: 'Hot Towel Shave', 
          duration_minutes: 30, 
          base_price: 30,
          description: "Traditional hot towel shave experience."
        },
        { 
          id: 4, 
          name: 'Haircut & Beard', 
          duration_minutes: 60, 
          base_price: 35,
          description: "Complete package including haircut and beard trim."
        }
      ];
    } catch (error) {
      console.error('Error reloading barber services:', error);
      serviceError.value = null; // No error since we're using hardcoded data
    } finally {
      isLoadingServices.value = false;
    }
  }
});

// Update service selection
const selectService = (service) => {
  if (!service) return;
  
  console.log('Selected service:', service);
  selectedService.value = service;
  bookingStore.selectedService = service;
  bookingStore.selectedDuration = service.duration_minutes;
  showServiceError.value = false;
  
  // Move to next step
  nextStep();
};

const selectTime = (time) => {
  // This function is replaced by selectTimeWithDuration
  // Keeping for backward compatibility
  selectedTime.value = time;
  showTimeError.value = false;
};

const nextStep = () => {
  if (currentStep.value === 0 && !selectedService.value) {
    // Show error or disable button if no service selected
    return;
  }
  if (currentStep.value === 1 && !selectedDate.value) {
    showDateError.value = true;
    return;
  }
  if (currentStep.value === 2 && !selectedTime.value) {
    showTimeError.value = true;
    return;
  }
  showDateError.value = false;
  showTimeError.value = false;
  currentStep.value++;
};

const validateEmail = (email) => {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
};

const validatePhone = (phone) => {
  return /^[0-9]{10}$/.test(phone.replace(/[^0-9]/g, ''));
};

const validateField = (field, value) => {
  switch (field) {
    case 'name':
      return value.trim() !== '';
    case 'email':
      return validateEmail(value);
    case 'phone':
      return validatePhone(value);
    default:
      return true;
  }
};

const getErrorMessage = (field) => {
  switch (field) {
    case 'name':
      return 'Name is required';
    case 'email':
      return !customerForm.email.trim() ? 'Email is required' : 'Please enter a valid email';
    case 'phone':
      return !customerForm.phone.trim() ? 'Phone number is required' : 'Please enter a valid 10-digit phone number';
    default:
      return 'This field is required';
  }
};

const validateForm = () => {
  let isValid = true;
  formErrors.name = '';
  formErrors.email = '';
  formErrors.phone = '';

  if (!customerForm.name.trim()) {
    formErrors.name = 'Name is required';
    isValid = false;
  }

  if (!customerForm.email.trim()) {
    formErrors.email = 'Email is required';
    isValid = false;
  } else if (!validateEmail(customerForm.email)) {
    formErrors.email = 'Please enter a valid email';
    isValid = false;
  }

  if (!customerForm.phone.trim()) {
    formErrors.phone = 'Phone number is required';
    isValid = false;
  } else if (!validatePhone(customerForm.phone)) {
    formErrors.phone = 'Please enter a valid 10-digit phone number';
    isValid = false;
  }

  return isValid;
};

// Add isSubmitting state
const isSubmitting = ref(false);

// Update confirmBooking function
const confirmBooking = async () => {
  // If already submitting, don't allow multiple clicks
  if (isSubmitting.value) return;
  
  if (!isFormValid.value) {
    Object.keys(formErrors).forEach(field => {
      if (!validateField(field, customerForm[field])) {
        formErrors[field] = getErrorMessage(field);
      }
    });
    return;
  }

  try {
    // Set submitting flag to true to disable button
    isSubmitting.value = true;
    
    const dateObj = new Date(selectedDate.value);
    const formattedDate = format(dateObj, 'yyyy-MM-dd');
    
    // Calculate how many additional time slots we need based on service duration
    const serviceDuration = selectedService.value.duration_minutes || 30;
    const requiredTimeSlots = Math.ceil(serviceDuration / 30);
    
    // Get the selected time slot and additional time slots if needed
    const baseTimeSlot = selectedTime.value;
    let timeSlots = [baseTimeSlot];
    
    // Only add additional time slots if the service requires more than 30 minutes
    if (requiredTimeSlots > 1) {
      // Find the index of the selected time slot
      const selectedIndex = bookingStore.availableTimeSlots.findIndex(
        slot => slot.time === baseTimeSlot
      );
      
      // Check if we have enough consecutive available slots
      for (let i = 1; i < requiredTimeSlots; i++) {
        const nextIndex = selectedIndex + i;
        
        // Make sure next slot exists and is available
        if (
          nextIndex < bookingStore.availableTimeSlots.length && 
          bookingStore.availableTimeSlots[nextIndex].isAvailable
        ) {
          timeSlots.push(bookingStore.availableTimeSlots[nextIndex].time);
        } else {
          toast.error(`The selected service requires ${serviceDuration} minutes, but not enough consecutive time slots are available.`);
          isSubmitting.value = false; // Reset submitting state
          return;
        }
      }
    }
    
    // Log the selected slot and additional slots for debug purposes
    console.log(`Selected slot: ${baseTimeSlot}, additional slots: ${timeSlots.slice(1).join(', ')}`);
    
    const bookingData = {
      barber_id: props.selectedBarber.id,
      service_id: selectedService.value.id,
      appointment_date: formattedDate,
      appointment_time: baseTimeSlot,
      client_name: customerForm.name,
      client_email: customerForm.email,
      client_phone: customerForm.phone,
      status: 'confirmed'
    };

    console.log('Submitting booking:', bookingData);
    
    // Actually send the data to the API
    const response = await axios.post('/api/appointments', bookingData);
    
    console.log('Booking response:', response);
    toast.success('Booking confirmed successfully!');
    closeModal();
  } catch (error) {
    console.error('Error creating booking:', error);
    const errorMessage = error.response?.data?.message || 'An unexpected error occurred. Please try again.';
    toast.error(errorMessage);
    // Reset submitting state so user can try again
    isSubmitting.value = false;
  }
};

const closeModal = () => {
  console.log('Closing modal and resetting all values');
  
  // Reset step
  currentStep.value = 0;
  
  // Reset selections
  selectedService.value = null;
  selectedDate.value = null;
  selectedDateStr.value = '';
  selectedTime.value = null;
  
  // Reset error states
  showDateError.value = false;
  showTimeError.value = false;
  showServiceError.value = false;
  
  // Reset form
  Object.assign(customerForm, {
    name: '',
    email: '',
    phone: '',
    notes: ''
  });
  
  // Reset form errors
  Object.assign(formErrors, {
    name: '',
    email: '',
    phone: ''
  });
  
  // Reset submission state
  isSubmitting.value = false;
  
  // Clear booking store state
  bookingStore.$reset();
  
  // Reset barber services
  barberServices.value = [];
  
  // Emit close event
  emit('close');
};

const canContinue = computed(() => {
  switch (currentStep.value) {
    case 0:
      return selectedService.value !== null;
    case 1:
      return selectedDate.value !== null;
    case 2:
      return selectedTime.value !== null;
    default:
      return true;
  }
});

const isFormValid = computed(() => {
  return customerForm.name.trim() !== '' && 
         validateEmail(customerForm.email) && 
         validatePhone(customerForm.phone);
});

// Update formattedDate computed property
const formattedDate = computed(() => {
  if (!selectedDate.value || isNaN(selectedDate.value)) {
    console.warn('Invalid selectedDate in formattedDate:', selectedDate.value);
    return '';
  }
  const dateObj = new Date(selectedDate.value);
  if (!isValid(dateObj)) {
    console.error('Invalid dateObj in formattedDate:', dateObj);
    return '';
  }
  try {
    return format(startOfDay(dateObj), 'dd MMM yyyy');
  } catch (error) {
    console.error('Error formatting date:', error);
    return '';
  }
});

const formatTimeSlot = (time) => {
  const [hours, minutes] = time.split(':');
  const date = new Date();
  date.setHours(parseInt(hours));
  date.setMinutes(parseInt(minutes));
  
  return new Intl.DateTimeFormat('bg-BG', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: false,
    timeZone: 'Europe/Sofia'
  }).format(date);
};

const selectTimeSlot = (slot) => {
  selectedTime.value = slot.time;
  // You can add additional logic here if needed
};

const formattedPrice = computed(() => {
  if (!selectedService.value?.base_price) return '';
  return `${selectedService.value.base_price} €`;
});

const getBarberImage = (barber) => {
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

const handleImageError = (e) => {
  console.error('Image failed to load:', e.target.src);
  e.target.src = '/images/barbers/default-barber.jpg';
};

const selectTimeWithDuration = (slot, index) => {
  selectedTime.value = slot.time;
  showTimeError.value = false;
  
  // Log selected time slot and additional slots for services longer than 30 minutes
  if (selectedService.value && selectedService.value.duration_minutes > 30) {
    const slotsNeeded = Math.ceil(selectedService.value.duration_minutes / 30);
    const additionalSlots = [];
    
    for (let i = 1; i < slotsNeeded; i++) {
      const nextIndex = index + i;
      if (nextIndex < bookingStore.availableTimeSlots.length) {
        additionalSlots.push(bookingStore.availableTimeSlots[nextIndex].time);
      }
    }
    
    console.log(`Selected slot: ${slot.time}, additional slots: ${additionalSlots.join(', ')}`);
  }
};

// Check if a slot can be selected based on service duration
const isSlotValidForService = (slot, index) => {
  if (!selectedService.value || selectedService.value.duration_minutes <= 30) {
    return slot.isAvailable;
  }
  
  return slot.isAvailable && hasEnoughConsecutiveSlots(index);
};

// Check if there are enough consecutive available slots 
const hasEnoughConsecutiveSlots = (startIndex) => {
  if (!selectedService.value) return true;
  
  const slotsNeeded = Math.ceil(selectedService.value.duration_minutes / 30);
  if (slotsNeeded <= 1) return true;
  
  // Check if we have enough consecutive slots from this position
  for (let i = 0; i < slotsNeeded; i++) {
    const slotIndex = startIndex + i;
    
    // Make sure slot exists and is available
    if (
      slotIndex >= bookingStore.availableTimeSlots.length ||
      !bookingStore.availableTimeSlots[slotIndex].isAvailable
    ) {
      return false;
    }
  }
  
  return true;
};

// Add method to check if a slot is part of the current selection
const isSlotSelected = (time, index) => {
  if (selectedTime.value === time) return true;
  
  // Check if this slot is part of a multi-slot selection
  if (selectedTime.value && selectedService.value && selectedService.value.duration_minutes > 30) {
    const selectedIndex = bookingStore.availableTimeSlots.findIndex(slot => slot.time === selectedTime.value);
    if (selectedIndex === -1) return false;
    
    const slotsNeeded = Math.ceil(selectedService.value.duration_minutes / 30);
    
    // Check if current index is within the range of selected slots
    return index > selectedIndex && index < selectedIndex + slotsNeeded;
  }
  
  return false;
};

// Add a debug function for time slots
const debugTimeSlots = () => {
  console.log('=== DEBUG TIME SLOTS ===');
  console.log('Selected date:', selectedDate.value);
  console.log('Formatted date:', format(new Date(selectedDate.value), 'yyyy-MM-dd'));
  console.log('Barber ID:', props.selectedBarber.id);
  console.log('Current stored time slots:', bookingStore.availableTimeSlots);
  
  // Make a direct API call to get appointments
  axios.get('/api/appointments', {
    params: {
      barber_id: props.selectedBarber.id,
      date: format(new Date(selectedDate.value), 'yyyy-MM-dd')
    }
  }).then(response => {
    console.log('Actual appointments from API:', response.data);
    const bookedTimes = response.data.map(appointment => appointment.appointment_time);
    console.log('Booked times extracted:', bookedTimes);
    
    // Check for time format inconsistencies
    bookedTimes.forEach(time => {
      console.log(`Time: "${time}" - type: ${typeof time}, length: ${time.length}`);
    });
  }).catch(error => {
    console.error('Error fetching appointments for debug:', error);
  });
};

// Add a helper function to calculate the end time for a service
const getEndTimeForSlot = (startTime, durationMinutes) => {
  const [hours, minutes] = startTime.split(':').map(Number);
  const date = new Date();
  date.setHours(hours);
  date.setMinutes(minutes + durationMinutes);
  
  return new Intl.DateTimeFormat('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  }).format(date);
};

// Add watch for preSelectedServiceId
watch(() => props.preSelectedServiceId, (serviceId) => {
  if (serviceId) {
    // Find matching service in loaded services
    const matchingService = barberServices.value.find(service => service.id === serviceId);
    if (matchingService) {
      console.log(`Auto-selecting service: ${matchingService.name} (ID: ${matchingService.id})`);
      selectedService.value = matchingService;
      bookingStore.selectedService = matchingService;
      bookingStore.selectedDuration = matchingService.duration_minutes;
      showServiceError.value = false;
      
      // Optionally auto-advance to date selection
      if (currentStep.value === 0) {
        currentStep.value = 1;
      }
    }
  }
}, { immediate: true });

// Add watch for when barber services are loaded to auto-select service
watch([() => barberServices.value, () => props.preSelectedServiceId], ([services, serviceId]) => {
  if (services.length > 0 && serviceId) {
    // Find matching service in loaded services
    const matchingService = services.find(service => service.id === serviceId);
    if (matchingService) {
      console.log(`Auto-selecting service: ${matchingService.name} (ID: ${matchingService.id})`);
      selectedService.value = matchingService;
      bookingStore.selectedService = matchingService;
      bookingStore.selectedDuration = matchingService.duration_minutes;
      showServiceError.value = false;
      
      // Optionally auto-advance to date selection
      if (currentStep.value === 0) {
        currentStep.value = 1;
      }
    }
  }
}, { immediate: true });
</script>

<style scoped>
.time-slots-container {
  min-height: 300px;
}

/* Add smooth scrollbar for time slots */
.time-slots-container .overflow-y-auto {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e0 #f7fafc;
}

.time-slots-container .overflow-y-auto::-webkit-scrollbar {
  width: 8px;
}

.time-slots-container .overflow-y-auto::-webkit-scrollbar-track {
  background: #f7fafc;
  border-radius: 4px;
}

.time-slots-container .overflow-y-auto::-webkit-scrollbar-thumb {
  background-color: #cbd5e0;
  border-radius: 4px;
  border: 2px solid #f7fafc;
}

/* Add styles for service selection */
.service-card {
  @apply bg-opacity-10;
  backdrop-filter: blur(10px);
}

.service-card:hover {
  @apply bg-opacity-20;
}
</style> 