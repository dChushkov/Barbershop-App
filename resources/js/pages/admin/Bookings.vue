<template>
  <div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm">
      <div class="p-6 border-b">
        <h2 class="text-lg font-medium">All Bookings</h2>
      </div>
      <div class="p-6">
        <div v-if="bookingsStore.loading" class="text-center py-4">
          Loading...
        </div>
        <div v-else-if="bookingsStore.error" class="text-center text-red-600 py-4">
          {{ bookingsStore.error }}
        </div>
        <div v-else-if="bookingsStore.bookings.length === 0" class="text-center text-gray-500 py-4">
          No bookings found
        </div>
        <div v-else>
          <div class="divide-y">
            <div v-for="booking in paginatedBookings" 
                 :key="booking.id"
                 class="py-4 flex items-center justify-between">
              <div>
                <p class="font-medium">{{ booking.client_name }}</p>
                <p class="text-sm text-gray-500">
                  {{ formatDate(booking.appointment_date) }} at {{ formatTime(booking.appointment_time) }}
                </p>
                <p class="text-sm text-gray-500">
                  Phone: {{ booking.client_phone }}
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
          
          <!-- Pagination -->
          <div class="mt-6 flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
            <div class="flex flex-1 justify-between sm:hidden">
              <button
                @click="currentPage--"
                :disabled="currentPage === 1"
                class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
              >
                Previous
              </button>
              <button
                @click="currentPage++"
                :disabled="currentPage >= totalPages"
                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                :class="{ 'opacity-50 cursor-not-allowed': currentPage >= totalPages }"
              >
                Next
              </button>
            </div>
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Showing
                  <span class="font-medium">{{ paginationStart + 1 }}</span>
                  to
                  <span class="font-medium">{{ Math.min(paginationEnd, sortedBookings.length) }}</span>
                  of
                  <span class="font-medium">{{ sortedBookings.length }}</span>
                  results
                </p>
              </div>
              <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                  <button
                    @click="currentPage--"
                    :disabled="currentPage === 1"
                    class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                    :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
                  >
                    <span class="sr-only">Previous</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                    </svg>
                  </button>
                  <button
                    @click="currentPage++"
                    :disabled="currentPage >= totalPages"
                    class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                    :class="{ 'opacity-50 cursor-not-allowed': currentPage >= totalPages }"
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
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useBookingsStore } from '@/stores/bookingsStore'

const bookingsStore = useBookingsStore()
const currentPage = ref(1)
const itemsPerPage = 10

// Sort bookings by date and time
const sortedBookings = computed(() => {
  return [...bookingsStore.bookings].sort((a, b) => {
    const dateA = new Date(`${a.appointment_date} ${a.appointment_time}`)
    const dateB = new Date(`${b.appointment_date} ${b.appointment_time}`)
    return dateB - dateA
  })
})

// Pagination
const totalPages = computed(() => Math.ceil(sortedBookings.value.length / itemsPerPage))
const paginationStart = computed(() => (currentPage.value - 1) * itemsPerPage)
const paginationEnd = computed(() => paginationStart.value + itemsPerPage)
const paginatedBookings = computed(() => 
  sortedBookings.value.slice(paginationStart.value, paginationEnd.value)
)

onMounted(async () => {
  try {
    await bookingsStore.fetchBookings()
    // Reset to first page when data is loaded
    currentPage.value = 1
  } catch (error) {
    console.error('Error loading bookings:', error)
  }
})

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
</script> 