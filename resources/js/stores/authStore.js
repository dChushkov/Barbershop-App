import { defineStore } from 'pinia'
import axios from 'axios'

// Temporary test credentials
const TEST_EMAIL = 'admin@test.com'
const TEST_PASSWORD = 'admin123'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('admin_token'),
    loading: false,
    error: null
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    getUser: (state) => state.user
  },

  actions: {
    async login(email, password) {
      try {
        this.loading = true
        this.error = null
        
        // Temporary login logic for testing
        if (email === TEST_EMAIL && password === TEST_PASSWORD) {
          const mockToken = 'test-token-123'
          const mockUser = {
            id: 1,
            email: TEST_EMAIL,
            name: 'Admin User'
          }
          
          this.token = mockToken
          this.user = mockUser
          
          localStorage.setItem('admin_token', mockToken)
          axios.defaults.headers.common['Authorization'] = `Bearer ${mockToken}`
          
          return true
        } else {
          throw new Error('Invalid credentials')
        }
      } catch (error) {
        this.error = error.message || 'Login failed'
        throw error
      } finally {
        this.loading = false
      }
    },

    async logout() {
      this.token = null
      this.user = null
      localStorage.removeItem('admin_token')
      delete axios.defaults.headers.common['Authorization']
    },

    async checkAuth() {
      try {
        if (!this.token) return false

        // Temporary auth check for testing
        if (this.token === 'test-token-123') {
          this.user = {
            id: 1,
            email: TEST_EMAIL,
            name: 'Admin User'
          }
          return true
        }
        return false
      } catch (error) {
        this.token = null
        this.user = null
        localStorage.removeItem('admin_token')
        return false
      }
    },

    async init() {
      if (this.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        await this.checkAuth()
      }
    }
  }
}) 