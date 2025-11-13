import api from '@/apirest/api.js'

class ConstanciasService {
  static async getConstancias() {
    const response = await api.get('/constancias')
    return response.data.data
  }

  static async createConstancias(data) {
    const response = await api.post('/constancias', data)
    return response.data.data
  }

  static async updateConstancias(id, data) {
    const response = await api.put(`/constancias/editar/${id}`, data)
    return response.data.data
  }

  static async generarPDF(id) {
    const response = await api.get(`/constancias/pdf/${id}`);
    return response.data.data; // devuelve la URL del PDF
  }

}

export default ConstanciasService