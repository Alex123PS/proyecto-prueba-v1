import { defineStore } from 'pinia'
import AuthService from '@/services/AuthService'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    roles: [],
    permissions: [],
  }),

  actions: {
    async login(credentials) {
      const data = await AuthService.login(credentials)
    
      if (!data.success) {
        // el backend dijo que falló (credenciales malas)
        return false
        }

      this.user = data.user
      this.token = data.token
      this.roles = data.roles
      this.permissions = data.permissions

      localStorage.setItem('token', this.token)

      return true
    },

    async validateToken() {
      if (!this.token) return false
      try {
        const data = await AuthService.validateToken()
        this.user = data.user
        this.roles = data.roles
        this.permissions = data.permissions
        return true
      } catch {
        this.logout()
        return false
      }
    },

    async logout() {
      try {
            await AuthService.logout()
        } catch (e) {
            console.error('Error en logout', e)
        } finally {
            this.user = null
            this.token = null
            this.roles = []
            this.permissions = []
            localStorage.removeItem('token')
  }
    },

    hasPermission(perm) {
      return this.permissions.includes(perm)
    }
  },
})
