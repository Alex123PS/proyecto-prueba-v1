import api from '@/apirest/api.js'

class UsersService {
  // Obtener todos los usuarios
  static async getUsers() {
    const response = await api.get('/users')
    return response.data.data
  }

  // Crear nuevo usuario
  static async createUser(payload) {
    const response = await api.post('/users', payload)
    return response.data.data
  }

  // Actualizar usuario existente
  static async updateUser(id, payload) {
    const response = await api.put(`/users/editar/${id}`, payload)
    return response.data.data
  }

  // Activar usuario
  static async activarUser(id) {
    const response = await api.put(`/users/activar/${id}`)
    return response.data.data
  }

  // Desactivar usuario
  static async desactivarUser(id) {
    const response = await api.put(`/users/desactivar/${id}`)
    return response.data.data
  }
}

export default UsersService
