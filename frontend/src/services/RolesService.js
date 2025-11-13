import api from '@/apirest/api.js'

class RolesService {
  static async getRoles() {
    const response = await api.get('/roles')
    return response.data.data
  }

  static async createRole(data) {
    const response = await api.post('/roles', data)
    return response.data.data
  }

  static async updateRole(id, data) {
    const response = await api.put(`/roles/${id}`, data)
    return response.data.data
  }

  static async deleteRole(id) {
    const response = await api.delete(`/roles/${id}`)
    return response.data
  }
}

export default RolesService
