import apiClient from './api'

export default {
  getAllProducts(params) {
    return apiClient.get('/products', { params })
  },

  getProduct(id) {
    return apiClient.get(`/products/${id}`)
  },

  createProduct(data) {
    return apiClient.post('/admin/products', data)
  },

  updateProduct(id, data) {
    return apiClient.put(`/admin/products/${id}`, data)
  },

  deleteProduct(id) {
    return apiClient.delete(`/admin/products/${id}`)
  }
}
