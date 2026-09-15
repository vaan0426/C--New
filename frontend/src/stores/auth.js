import { defineStore } from 'pinia'
import api from '../lib/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('auth_token') || null,
    ready: null,
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
      this.ready = Promise.resolve()
      localStorage.setItem('auth_token', data.token)
    },

    async register(payload) {
      return api.post('/register', payload)
    },

    // Idempotent: on a hard navigation the router guard needs to await the
    // same in-flight request fetchUser() kicks off from main.js, rather
    // than firing (and racing) a second one.
    fetchUser() {
      if (!this.token) return Promise.resolve()
      if (!this.ready) {
        this.ready = api
          .get('/user')
          .then(({ data }) => {
            this.user = data
          })
          .catch(() => {
            this.token = null
            this.user = null
            localStorage.removeItem('auth_token')
          })
      }
      return this.ready
    },

    async logout() {
      try {
        await api.post('/logout')
      } finally {
        this.token = null
        this.user = null
        this.ready = null
        localStorage.removeItem('auth_token')
      }
    },
  },
})
