import axios from 'axios';

const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Add response interceptor for handling errors
api.interceptors.response.use(
  (response) => response.data,
  (error) => {
    if (error.response) {
      return Promise.reject(error.response.data || { message: 'An error occurred' });
    }
    return Promise.reject(error);
  }
);

export const barberApi = {
  // Get all barbers
  getBarbers: () => 
    api.get('/barbers'),
  
  // Get barber by ID
  getBarber: (id) => 
    api.get(`/barbers/${id}`),
    
  // Get services for a barber
  getServices: (barberId) => 
    api.get(`/barbers/${barberId}/services`)
};

export const serviceApi = {
  // Get all services
  getServices: () => 
    api.get('/services'),
  
  // Get service by ID
  getService: (id) => 
    api.get(`/services/${id}`)
};

export const bookingApi = {
  // Get available time slots for a specific date and barber
  getAvailableTimeSlots: (barberId, date) => 
    api.get(`/appointments/available-slots/${barberId}`, { 
      params: { date }
    }),
  
  // Create a new booking
  createBooking: (bookingData) => 
    api.post('/appointments', bookingData),
    
  // Cancel a booking
  cancelBooking: (bookingId) => 
    api.post(`/appointments/${bookingId}/cancel`)
};

export default api; 