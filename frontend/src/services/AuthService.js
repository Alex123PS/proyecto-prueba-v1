import api from '@/apirest/api.js'

class AuthService {
  static async login(credentials) {
    try {
      const response = await api.post('/auth/login', credentials)
      return response.data // trae success true/false
    } catch (error) {
      if (error.response && error.response.data) {
        return error.response.data // ex: { success: false, message: "Credenciales incorrectas" }
      }
      throw error // error real (servidor caído, etc.)
    }
  }

  static async logout() {
    return (await api.post('/auth/logout')).data
  }

  static async validateToken() {
    return (await api.get('/auth/validate-token')).data
  }
}

export default AuthService
