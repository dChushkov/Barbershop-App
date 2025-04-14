<template>
  <div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="text-sm font-medium text-gray-500">Today's Bookings</h3>
        <p class="mt-2 text-3xl font-semibold">{{ bookingsStore.getTodayBookings }}</p>
      </div>
      
      <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="text-sm font-medium text-gray-500">Active Barbers</h3>
        <p class="mt-2 text-3xl font-semibold">{{ barbers.length }}</p>
        <p v-if="barbersError" class="mt-1 text-sm text-red-600">{{ barbersError }}</p>
      </div>
      
      <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="text-sm font-medium text-gray-500">Total Services</h3>
        <p class="mt-2 text-3xl font-semibold">{{ services.length }}</p>
      </div>
      
      <!-- Archive Button -->
      <div class="bg-white p-6 rounded-lg shadow-sm flex flex-col justify-between">
        <div>
          <h3 class="text-sm font-medium text-gray-500">Archive Old Appointments</h3>
          <p class="text-sm text-gray-400 mt-1">Appointments older than 1 month will be archived</p>
        </div>
        <button 
          @click="archiveOldAppointments"
          :disabled="isArchiving"
          class="mt-4 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <svg v-if="isArchiving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ isArchiving ? 'Archiving...' : 'Archive Old Appointments' }}
        </button>
      </div>
    </div>

    <!-- Recent Bookings -->
    <div class="bg-white rounded-lg shadow-sm">
      <div class="p-6 border-b">
        <h2 class="text-lg font-medium">Today's Bookings</h2>
        <p class="text-sm text-gray-500">Showing all bookings for today, sorted by booking time</p>
      </div>
      <div class="p-6">
        <div v-if="bookingsStore.loading" class="text-center py-4">
          Loading...
        </div>
        <div v-else-if="bookingsStore.error" class="text-center text-red-600 py-4">
          {{ bookingsStore.error }}
        </div>
        <div v-else-if="bookingsStore.getRecentBookings.length === 0" class="text-center text-gray-500 py-4">
          No bookings for today
        </div>
        <div v-else class="divide-y">
          <div v-for="booking in bookingsStore.getRecentBookings" 
               :key="booking.id"
               class="py-4 flex items-center justify-between">
            <div>
              <p class="font-medium">{{ booking.client_name }}</p>
              <p class="text-sm text-gray-500">
                Appointment at {{ formatTime(booking.appointment_time) }}
              </p>
              <p class="text-sm text-gray-500">
                Phone: {{ booking.client_phone }}
              </p>
              <p v-if="booking.created_at" class="text-xs text-gray-400 mt-1">
                Booked {{ formatTimeAgo(booking.created_at) }}
              </p>
            </div>
            <div class="flex items-center gap-4">
              <span :class="{
                'px-2 py-1 text-xs rounded-full': true,
                'bg-green-100 text-green-800': booking.status === 'confirmed',
                'bg-yellow-100 text-yellow-800': booking.status === 'pending',
                'bg-red-100 text-red-800': booking.status === 'cancelled'
              }">
                {{ booking.status }}
              </span>
              <button 
                v-if="booking.status === 'confirmed'"
                @click="cancelBooking(booking.id)"
                class="text-sm text-red-600 hover:text-red-800"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useBookingsStore } from '@/stores/bookingsStore'
import axios from 'axios'

const bookingsStore = useBookingsStore()
const barbers = ref([])
const services = ref([])
const barbersError = ref(null)
const isArchiving = ref(false)

onMounted(async () => {
  try {
    // Fetch initial data
    await Promise.all([
      bookingsStore.fetchBookings(),
      fetchBarbers(),
      fetchServices()
    ])
  } catch (error) {
    console.error('Error loading dashboard data:', error)
  }
})

const fetchBarbers = async () => {
  try {
    barbersError.value = null
    const response = await axios.get('/api/barbers')
    barbers.value = response.data.data // Access the data property from the response
  } catch (error) {
    console.error('Error fetching barbers:', error)
    barbersError.value = 'Failed to load barbers'
    barbers.value = []
  }
}

const fetchServices = async () => {
  try {
    const response = await axios.get('/api/services')
    services.value = response.data
  } catch (error) {
    console.error('Error fetching services:', error)
    services.value = []
  }
}

const cancelBooking = async (bookingId) => {
  if (confirm('Are you sure you want to cancel this booking?')) {
    try {
      await bookingsStore.cancelBooking(bookingId)
    } catch (error) {
      console.error('Error cancelling booking:', error)
    }
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('bg-BG', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatTime = (time) => {
  return time.substring(0, 5) // Format HH:mm
}

const formatTimeAgo = (date) => {
  const now = new Date();
  const bookingDate = new Date(date);
  const diffInMinutes = Math.floor((now - bookingDate) / (1000 * 60));
  
  if (diffInMinutes < 1) return 'just now';
  if (diffInMinutes < 60) return `${diffInMinutes} minutes ago`;
  
  const diffInHours = Math.floor(diffInMinutes / 60);
  if (diffInHours < 24) return `${diffInHours} hours ago`;
  
  const diffInDays = Math.floor(diffInHours / 24);
  return `${diffInDays} days ago`;
}

const archiveOldAppointments = async () => {
  if (!confirm('Are you sure you want to archive appointments older than 1 month? They will be hidden from the dashboard but kept in the database.')) {
    return;
  }

  try {
    isArchiving.value = true;
    const response = await axios.post('/api/appointments/archive-old');
    alert(response.data.message);
    
    // Refresh the bookings data
    await bookingsStore.fetchBookings();
  } catch (error) {
    console.error('Error archiving appointments:', error);
    alert('Failed to archive appointments. Please try again.');
  } finally {
    isArchiving.value = false;
  }
};
</script> 