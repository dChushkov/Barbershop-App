import { defineStore } from 'pinia';
import { bookingApi } from '@/services/api';
import { ref } from 'vue';
import axios from 'axios';

/**
 * @typedef {Object} TimeSlot
 * @property {string} time - The time slot (e.g., "9:00 AM")
 * @property {boolean} isAvailable - Whether the slot is available
 */

/**
 * @typedef {Object} Booking
 * @property {string} barberId - The ID of the barber
 * @property {string} serviceId - The ID of the service
 * @property {Date} date - The booking date
 * @property {string} time - The booking time
 * @property {string} customerName - The customer's name
 * @property {string} customerEmail - The customer's email
 * @property {string} customerPhone - The customer's phone
 * @property {string} [notes] - Optional booking notes
 * @property {string} [status] - Booking status (pending, confirmed, cancelled)
 */

export const useBookingStore = defineStore('booking', {
  state: () => ({
    selectedBarber: null,
    selectedService: null,
    selectedDate: null,
    selectedTime: null,
    selectedDuration: null,
    /** @type {TimeSlot[]} */
    availableTimeSlots: [],
    /** @type {Booking[]} */
    userBookings: [],
    currentBooking: null,
    isLoading: false,
    error: null
  }),

  actions: {
    /**
     * Fetch available time slots for a barber on a specific date
     */
    async fetchAvailableTimeSlots(barberId, date) {
      if (!barberId || !date) {
        console.error('Missing required parameters:', { barberId, date });
        return;
      }

      this.isLoading = true;
      this.error = null;

      try {
        console.log('Fetching time slots with:', { barberId, date });
        const response = await bookingApi.getAvailableTimeSlots(barberId, date);
        console.log('Available time slots response:', response);
        this.availableTimeSlots = response.data;
      } catch (error) {
        console.error('Error fetching time slots:', error);
        this.error = error.response?.data?.message || 'Failed to fetch available time slots';
        this.availableTimeSlots = [];
      } finally {
        this.isLoading = false;
      }
    },

    /**
     * Create a new booking
     */
    async createBooking(bookingData) {
      this.isLoading = true;
      this.error = null;

      try {
        console.log('Creating booking with data:', bookingData);
        // Date is already formatted as YYYY-MM-DD from the component
        const response = await bookingApi.createBooking(bookingData);
        
        this.currentBooking = response.data.booking;
        return { success: true, booking: response.data.booking };
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create booking. Please try again.';
        console.error('Error creating booking:', error);
        return { success: false, error: this.error };
      } finally {
        this.isLoading = false;
      }
    },

    /**
     * Fetch user's bookings
     */
    async fetchUserBookings() {
      this.isLoading = true;
      this.error = null;

      try {
        const response = await bookingApi.getUserBookings();
        this.userBookings = response.bookings;
      } catch (error) {
        this.error = error.message || 'Failed to fetch your bookings. Please try again.';
        console.error('Error fetching user bookings:', error);
      } finally {
        this.isLoading = false;
      }
    },

    /**
     * Cancel a booking
     * @param {string} bookingId - The ID of the booking to cancel
     * @param {string} customerEmail - The customer's email for verification
     */
    async cancelBooking(bookingId, customerEmail) {
      this.isLoading = true;
      this.error = null;

      try {
        await bookingApi.cancelBooking(bookingId, { email: customerEmail });
        return { success: true };
      } catch (error) {
        this.error = error.message || 'Failed to cancel booking. Please try again.';
        console.error('Error cancelling booking:', error);
        return { success: false, error: this.error };
      } finally {
        this.isLoading = false;
      }
    },

    $reset() {
      this.selectedBarber = null;
      this.selectedService = null;
      this.selectedDate = null;
      this.selectedTime = null;
      this.selectedDuration = null;
      this.availableTimeSlots = [];
      this.userBookings = [];
      this.currentBooking = null;
      this.isLoading = false;
      this.error = null;
    }
  },

  getters: {
    /**
     * Get available time slots filtered by availability
     * @returns {TimeSlot[]}
     */
    getAvailableSlots: (state) => {
      return state.availableTimeSlots.filter(slot => slot.isAvailable);
    },

    /**
     * Get user's upcoming bookings
     * @returns {Booking[]}
     */
    getUpcomingBookings: (state) => {
      const now = new Date();
      return state.userBookings
        .filter(booking => {
          const bookingDate = new Date(booking.date + ' ' + booking.time);
          return bookingDate > now && booking.status !== 'cancelled';
        })
        .sort((a, b) => new Date(a.date + ' ' + a.time) - new Date(b.date + ' ' + b.time));
    },

    /**
     * Get user's past bookings
     * @returns {Booking[]}
     */
    getPastBookings: (state) => {
      const now = new Date();
      return state.userBookings
        .filter(booking => {
          const bookingDate = new Date(booking.date + ' ' + booking.time);
          return bookingDate <= now || booking.status === 'cancelled';
        })
        .sort((a, b) => new Date(b.date + ' ' + b.time) - new Date(a.date + ' ' + a.time));
    }
  }
}); 