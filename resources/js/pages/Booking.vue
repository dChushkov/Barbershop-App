<template>
  <div class="min-h-screen bg-black/20 pt-24 pb-36">
    <div class="max-w-3xl mx-auto">
      <h1 class="text-4xl font-bold text-white mb-8 text-center">Book Your Appointment</h1>

      <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 shadow-xl">
        <!-- Date Selection -->
        <div class="mb-8">
          <label class="block text-white text-lg font-medium mb-4">Select Date</label>
          <Datepicker
            v-model="selectedDate"
            :format="'dd MMM yyyy'"
            :model-type="'date'"
            :dark="true"
            :locale="'en'"
            :disabled-dates="disabledDates"
            :enable-time-picker="false"
            :calendar-button="true"
            calendar-button-icon="📅"
            class="w-full"
          />
        </div>

        <!-- Time Slots -->
        <div v-if="selectedDate && availableTimeSlots.length > 0" class="mb-8">
          <label class="block text-white text-lg font-medium mb-4">Available Time Slots</label>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <button
              v-for="slot in availableTimeSlots"
              :key="slot"
              @click="selectTimeSlot(slot)"
              class="p-4 text-center rounded-lg border-2 transition-all duration-300"
              :class="[
                selectedTimeSlot === slot
                  ? 'border-white bg-white/20 text-white'
                  : 'border-white/30 text-white/70 hover:border-white hover:text-white'
              ]"
            >
              {{ slot }}
            </button>
          </div>
        </div>

        <!-- No Slots Available -->
        <div v-else-if="selectedDate" class="text-center text-white/70 py-8">
          No available time slots for this date
        </div>

        <!-- Service Selection -->
        <div class="mb-8">
          <label class="block text-white text-lg font-medium mb-4">Select Service</label>
          <select
            v-model="selectedService"
            class="w-full p-4 rounded-lg bg-white/10 border border-white/30 text-white focus:outline-none focus:border-white"
          >
            <option value="" disabled>Choose a service</option>
            <option v-for="service in services" :key="service.id" :value="service">
              {{ service.name }} - ${{ service.base_price }}
            </option>
          </select>
        </div>

        <!-- Submit Button -->
        <button
          @click="submitBooking"
          :disabled="!isFormValid"
          class="w-full py-4 px-6 bg-white text-black rounded-lg font-medium transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-white/90"
        >
          Book Appointment
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const router = useRouter();
const selectedDate = ref(null);
const selectedTimeSlot = ref('');
const selectedService = ref(null);
const availableTimeSlots = ref([]);
const services = ref([]);

// Fetch services on component mount
onMounted(async () => {
  try {
    const response = await fetch('/api/services');
    if (!response.ok) throw new Error('Failed to fetch services');
    const data = await response.json();
    services.value = data.data;
  } catch (error) {
    console.error('Error fetching services:', error);
  }
});

// Watch for date changes to fetch available time slots
watch(selectedDate, async (newDate) => {
  if (newDate) {
    try {
      const response = await fetch(`/api/bookings/available-slots/1?date=${newDate.toISOString()}`);
      if (!response.ok) throw new Error('Failed to fetch time slots');
      const data = await response.json();
      availableTimeSlots.value = data.data;
    } catch (error) {
      console.error('Error fetching time slots:', error);
      availableTimeSlots.value = [];
    }
  } else {
    availableTimeSlots.value = [];
  }
});

// Disable Sundays and past dates
const disabledDates = (date) => {
  return date.getDay() === 0 || date < new Date();
};

// Select time slot
const selectTimeSlot = (slot) => {
  selectedTimeSlot.value = slot;
};

// Form validation
const isFormValid = computed(() => {
  return selectedDate.value && selectedTimeSlot.value && selectedService.value;
});

// Submit booking
const submitBooking = async () => {
  if (!isFormValid.value) return;

  try {
    const response = await fetch('/api/bookings', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        date: selectedDate.value.toISOString(),
        time_slot: selectedTimeSlot.value,
        service_id: selectedService.value.id,
        barber_id: 1 // This should be dynamic based on selected barber
      }),
    });

    if (!response.ok) throw new Error('Failed to create booking');

    // Redirect to success page or show success message
    router.push({ name: 'home' });
  } catch (error) {
    console.error('Error creating booking:', error);
  }
};
</script> 