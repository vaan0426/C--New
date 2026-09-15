import { defineStore } from 'pinia'
import api from '../lib/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('auth_token') || null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isStaff: (state) => state.user?.role === 'admin' || state.user?.role === 'editor',
    isAdmin: (state) => state.user?.role === 'admin',
  },

  actions: {
    async login(email, password) {
      const { data } = await api.post('/login', { email, password })
      this.token = data.token
      this.user = data.user
      localStorage.setItem('auth_token', data.token)
    },

    async register(payload) {
      return api.post('/register', payload)
    },

    async fetchUser() {
      if (!this.token) return
      const { data } = await api.get('/user')
      this.user = data
    },

    async logout() {
      try {
        await api.post('/logout')
      } finally {
        this.token = null
        this.user = null
        localStorage.removeItem('auth_token')
      }
    },
  },
})
