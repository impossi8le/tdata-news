import { defineStore } from 'pinia'
import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  timeout: 10000,
  withCredentials: true,
  headers: { 'Accept': 'application/json' },
})

export const useAuthStore = defineStore('auth', {
  state: () => ({
    isAuthenticated: false,
    user: null,
  }),
  actions: {
    async login(login, password) {
      const { data } = await api.post('/login', { login, password })
      this.isAuthenticated = true
      this.user = data.user
      return true
    },
    async logout() {
      await api.post('/logout')
      this.isAuthenticated = false
      this.user = null
    },
    async check() {
      try {
        const { data } = await api.get('/me')
        this.isAuthenticated = data.authorized
        if (data.authorized) {
          this.user = { id: data.user_id }
        } else {
          this.user = null
        }
      } catch {
        this.isAuthenticated = false
        this.user = null
      }
    }
  }
})
