import { defineStore } from 'pinia'
import axios from 'axios'

export const useBookingsStore = defineStore('bookings', {
  state: () => ({
    bookings: [],
    loading: false,
    error: null
  }),

  getters: {
    getTodayBookings: (state) => {
      const today = new Date().toISOString().split('T')[0]
      console.log('Today\'s date:', today)
      console.log('All bookings:', state.bookings)
      console.log('Bookings dates:', state.bookings.map(b => b.appointment_date))
      const todayBookings = state.bookings.filter(booking => booking.appointment_date === today)
      console.log('Filtered bookings for today:', todayBookings)
      return todayBookings.length
    },
    
    getRecentBookings: (state) => {
      const today = new Date().toISOString().split('T')[0];
      return [...state.bookings]
        .filter(booking => booking.appointment_date === today)
        .sort((a, b) => {
          // Sort by created_at date if available, otherwise by appointment time
          if (a.created_at && b.created_at) {
            return new Date(b.created_at) - new Date(a.created_at);
          }
          const dateA = new Date(`${a.appointment_date} ${a.appointment_time}`);
          const dateB = new Date(`${b.appointment_date} ${b.appointment_time}`);
          return dateB - dateA;
        });
    }
  },

  actions: {
    async fetchBookings(barberId = null, date = null) {
      try {
        this.loading = true
        this.error = null
        
        // If no date provided, fetch all bookings from today onwards
        const today = new Date().toISOString().split('T')[0]
        
        const params = {
          from_date: date || today // Use provided date or today as starting point
        }
        if (barberId) params.barber_id = barberId
        
        console.log('Fetching appointments with params:', params)
        const response = await axios.get('/api/appointments', { params })
        console.log('Raw API response:', response)
        
        if (response.data && response.data.data) {
          this.bookings = response.data.data
          console.log('Set bookings to:', this.bookings)
        } else {
          console.error('Unexpected API response format:', response.data)
          this.bookings = []
        }
      } catch (error) {
        console.error('Error fetching bookings:', error.response || error)
        this.error = error.response?.data?.message || 'Failed to fetch bookings'
        this.bookings = []
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateBookingStatus(bookingId, status) {
      try {
        this.loading = true
        this.error = null
        
        await axios.put(`/api/appointments/${bookingId}/status`, { status })
        
        // Update local state
        const booking = this.bookings.find(b => b.id === bookingId)
        if (booking) {
          booking.status = status
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update booking'
        throw error
      } finally {
        this.loading = false
      }
    },

    async cancelBooking(bookingId) {
      try {
        this.loading = true;
        this.error = null;
        
        await axios.post(`/api/appointments/${bookingId}/cancel`);
        
        // Refresh the bookings data
        await this.fetchBookings();
        
      } catch (error) {
        console.error('Error cancelling booking:', error);
        this.error = 'Failed to cancel booking';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async getAvailableTimeSlots(barberId, date) {
      try {
        this.loading = true
        this.error = null
        
        const response = await axios.get(`/api/barbers/${barberId}/available-slots`, {
          params: { date }
        })
        
        return response.data.slots
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch available time slots'
        throw error
      } finally {
        this.loading = false
      }
    }
  }
}) 