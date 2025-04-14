<template>
  <div class="space-y-6">
    <div v-if="loading" class="text-center py-4">
      Loading...
    </div>
    <div v-else-if="error" class="text-center text-red-600 py-4">
      {{ error }}
    </div>
    <div v-else-if="barbers.length === 0" class="text-center text-gray-500 py-4">
      No barbers found
    </div>
    <div v-else>
      <!-- Barbers Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div v-for="barber in barbers" 
             :key="barber.id" 
             class="bg-white rounded-lg shadow-sm p-6 border border-gray-200"
             :class="{ 'ring-2 ring-blue-500 bg-blue-50': selectedBarber?.id === barber.id,
                      'hover:border-blue-300': selectedBarber?.id !== barber.id }"
             @click="selectBarber(barber)">
          <div class="text-center">
            <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-gray-100 overflow-hidden">
              <img 
                :src="getBarberImagePath(barber)" 
                :alt="barber.name"
                class="w-full h-full object-cover"
                @error="handleImageError"
              />
            </div>
            <h2 class="text-lg font-medium text-gray-900">{{ barber.name }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ barber.email }}</p>
            <p class="text-sm text-gray-500">{{ barber.phone }}</p>
          </div>
        </div>
      </div>

      <!-- Selected Barber's Appointments -->
      <div v-if="selectedBarber" class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 bg-gray-50">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">{{ selectedBarber.name }}'s Appointments</h3>
            <div class="flex gap-2">
              <button 
                @click="filterType = 'today'"
                :class="[
                  'px-3 py-1 text-sm font-medium rounded-md',
                  filterType === 'today' 
                    ? 'bg-blue-100 text-blue-800 border-blue-200'
                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200 border-gray-200'
                ]"
                class="border"
              >
                Today
              </button>
              <button 
                @click="filterType = 'upcoming'"
                :class="[
                  'px-3 py-1 text-sm font-medium rounded-md',
                  filterType === 'upcoming'
                    ? 'bg-blue-100 text-blue-800 border-blue-200'
                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200 border-gray-200'
                ]"
                class="border"
              >
                Upcoming
              </button>
            </div>
          </div>
        </div>
        <div class="p-6">
          <div v-if="paginatedAppointments.length === 0" class="text-center text-gray-500 py-4">
            No {{ filterType === 'today' ? 'appointments for today' : 'upcoming appointments' }}
          </div>
          <div v-else>
            <div class="divide-y divide-gray-200">
              <div v-for="appointment in paginatedAppointments" 
                   :key="appointment.id"
                   class="py-4 flex items-center justify-between hover:bg-gray-50 px-4 -mx-4">
                <div class="flex-1 grid grid-cols-2 gap-4">
                  <!-- Client Information -->
                  <div>
                    <h4 class="font-medium text-gray-900 mb-1">Client Information</h4>
                    <div class="space-y-1">
                      <p class="text-sm">
                        <span class="text-gray-500">Name:</span> 
                        <span class="font-medium">{{ appointment.client_name }}</span>
                      </p>
                      <p class="text-sm">
                        <span class="text-gray-500">Email:</span> 
                        <span class="font-medium">{{ appointment.client_email }}</span>
                      </p>
                      <p class="text-sm">
                        <span class="text-gray-500">Phone:</span> 
                        <span class="font-medium">{{ appointment.client_phone }}</span>
                      </p>
                    </div>
                  </div>

                  <!-- Appointment Details -->
                  <div>
                    <h4 class="font-medium text-gray-900 mb-1">Appointment Details</h4>
                    <div class="space-y-1">
                      <p class="text-sm">
                        <span class="text-gray-500">Date:</span>
                        <span class="font-medium">{{ formatDate(appointment.appointment_date) }}</span>
                      </p>
                      <p class="text-sm">
                        <span class="text-gray-500">Time:</span>
                        <span class="font-medium">{{ formatTime(appointment.appointment_time) }}</span>
                      </p>
                      <p class="text-sm">
                        <span class="text-gray-500">Service:</span>
                        <span class="font-medium">{{ getServiceName(appointment.service_id) }}</span>
                      </p>
                      <p class="text-sm">
                        <span class="text-gray-500">Created:</span>
                        <span class="font-medium">{{ formatDateTime(appointment.created_at) }}</span>
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Status and Actions -->
                <div class="ml-4 flex flex-col items-end gap-2">
                  <span :class="{
                    'px-3 py-1 text-xs font-medium rounded-full': true,
                    'bg-green-100 text-green-800': appointment.status === 'confirmed',
                    'bg-yellow-100 text-yellow-800': appointment.status === 'pending',
                    'bg-red-100 text-red-800': appointment.status === 'cancelled'
                  }">
                    {{ appointment.status }}
                  </span>
                  <button 
                    v-if="appointment.status === 'confirmed'"
                    @click.stop="cancelAppointment(appointment.id)"
                    :disabled="cancellingAppointment === appointment.id"
                    class="text-sm font-medium text-red-600 hover:text-red-800 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                  >
                    <svg v-if="cancellingAppointment === appointment.id" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Cancel
                  </button>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex items-center justify-between border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6 -mx-6 -mb-6 rounded-b-lg">
              <div class="flex flex-1 justify-between sm:hidden">
                <button
                  @click="currentPage--"
                  :disabled="currentPage === 1"
                  class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Previous
                </button>
                <button
                  @click="currentPage++"
                  :disabled="currentPage >= totalPages"
                  class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Next
                </button>
              </div>
              <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ ((currentPage - 1) * itemsPerPage) + 1 }}</span>
                    to
                    <span class="font-medium">{{ Math.min(currentPage * itemsPerPage, filteredAppointments.length) }}</span>
                    of
                    <span class="font-medium">{{ filteredAppointments.length }}</span>
                    results
                  </p>
                </div>
                <div>
                  <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    <button
                      @click="currentPage--"
                      :disabled="currentPage === 1"
                      class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <span class="sr-only">Previous</span>
                      <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                      </svg>
                    </button>
                    <button
                      v-for="page in totalPages"
                      :key="page"
                      @click="currentPage = page"
                      :class="[
                        'relative inline-flex items-center px-4 py-2 text-sm font-semibold',
                        currentPage === page
                          ? 'z-10 bg-blue-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600'
                          : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0'
                      ]"
                    >
                      {{ page }}
                    </button>
                    <button
                      @click="currentPage++"
                      :disabled="currentPage >= totalPages"
                      class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <span class="sr-only">Next</span>
                      <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                      </svg>
                    </button>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useBookingsStore } from '@/stores/bookingsStore'

const barbers = ref([])
const loading = ref(false)
const error = ref(null)
const bookingsStore = useBookingsStore()
const selectedBarber = ref(null)
const currentPage = ref(1)
const itemsPerPage = 10
const filterType = ref('today')

const services = {
  1: 'Haircut',
  2: 'Beard Trim',
  3: 'Hot Towel Shave',
  4: 'Haircut & Beard'
};

const getServiceName = (serviceId) => {
  return services[serviceId] || `Service ${serviceId}`;
};

// Get barber image path
const getBarberImagePath = (barber) => {
  if (!barber || !barber.id) {
    return '/images/barbers/default-barber.jpg';
  }
  
  switch (parseInt(barber.id)) {
    case 1:
      return '/images/barbers/barber1.jpg';
    case 2:
      return '/images/barbers/barber2.jpg';
    case 3:
      return '/images/barbers/barber3.jpg';
    default:
      return '/images/barbers/default-barber.jpg';
  }
};

// Handle image loading errors
const handleImageError = (event) => {
  event.target.src = '/images/barbers/default-barber.jpg';
}

// Fetch barbers data
const fetchBarbers = async () => {
  try {
    loading.value = true
    error.value = null
    const response = await axios.get('/api/barbers')
    barbers.value = response.data.data || []
    // Select first barber by default
    if (barbers.value.length > 0) {
      selectedBarber.value = barbers.value[0]
    }
  } catch (err) {
    console.error('Error fetching barbers:', err)
    error.value = 'Failed to load barbers'
  } finally {
    loading.value = false
  }
}

const selectBarber = async (barber) => {
  selectedBarber.value = barber
  currentPage.value = 1 // Reset to first page
  filterType.value = 'today' // Default to today's view
  
  // Fetch appointments for this barber
  try {
    await bookingsStore.fetchBookings(barber.id)
  } catch (error) {
    console.error('Error fetching barber appointments:', error)
  }
}

// Get filtered and sorted appointments for selected barber
const filteredAppointments = computed(() => {
  if (!selectedBarber.value) return []
  
  const today = new Date().toISOString().split('T')[0];
  const tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  const tomorrowStr = tomorrow.toISOString().split('T')[0];
  
  const appointments = bookingsStore.bookings
    .filter(b => b.barber_id === selectedBarber.value.id)
    .sort((a, b) => {
      // First compare by date
      const dateComparison = a.appointment_date.localeCompare(b.appointment_date);
      if (dateComparison !== 0) return dateComparison;
      
      // If same date, compare by time
      return a.appointment_time.localeCompare(b.appointment_time);
    });
  
  if (filterType.value === 'today') {
    return appointments.filter(b => b.appointment_date === today);
  } else {
    // upcoming - show only future dates starting from tomorrow
    return appointments.filter(b => b.appointment_date >= tomorrowStr);
  }
})

// Pagination
const totalPages = computed(() => Math.ceil(filteredAppointments.value.length / itemsPerPage))
const paginationStart = computed(() => (currentPage.value - 1) * itemsPerPage)
const paginationEnd = computed(() => paginationStart.value + itemsPerPage)
const paginatedAppointments = computed(() => 
  filteredAppointments.value.slice(paginationStart.value, paginationEnd.value)
)

// Cancel appointment
const cancelAppointment = async (appointmentId) => {
  if (cancellingAppointment.value === appointmentId) return;
  
  try {
    if (!confirm('Сигурни ли сте, че искате да откажете тази резервация? Часът ще стане свободен за нови записвания.')) {
      return;
    }

    cancellingAppointment.value = appointmentId;
    
    // Call the store method to cancel the appointment
    await bookingsStore.cancelBooking(appointmentId);
    
    // Show success message
    alert('Резервацията беше успешно отказана.');

  } catch (error) {
    console.error('Error cancelling appointment:', error);
    alert('Възникна грешка при отказване на резервацията. Моля, опитайте отново.');
  } finally {
    cancellingAppointment.value = null;
  }
}

// Add loading state for cancel button
const cancellingAppointment = ref(null);

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

const formatDateTime = (datetime) => {
  return new Date(datetime).toLocaleString('bg-BG', {
    year: 'numeric',
    month: 'numeric',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(async () => {
  try {
    // Fetch both barbers and appointments data
    await Promise.all([
      fetchBarbers(),
      bookingsStore.fetchBookings()
    ])
  } catch (error) {
    console.error('Error loading data:', error)
  }
})
</script>

<style scoped>
.grid > div {
  cursor: pointer;
  transition: all 0.2s;
}
.grid > div:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}
</style> 