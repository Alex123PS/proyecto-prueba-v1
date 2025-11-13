import api from '@/apirest/api.js'

class ConductoresService {
  static async getConductores() {
    const response = await api.get('/conductores')
    return response.data.data
  }

  static async createConductor(data) {
    const response = await api.post('/conductores', data)
    return response.data.data
  }

  static async updateConductor(id, data) {
    const response = await api.put(`/conductores/editar/${id}`, data)
    return response.data.data
  }

  static async activarConductor(id) {
    const response = await api.put(`/conductores/activar/${id}`)
    return response.data.data
  }

  static async desactivarConductor(id) {
    const response = await api.put(`/conductores/desactivar/${id}`)
    return response.data.data
  }
}

export default ConductoresService