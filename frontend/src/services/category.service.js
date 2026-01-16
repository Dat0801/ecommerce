import apiClient from './api'

export default {
  getAllCategories(params = {}) {
    return apiClient.get('/categories', { params })
  },

  getCategory(id) {
    return apiClient.get(`/categories/${id}`)
  },

  createCategory(data) {
    return apiClient.post('/admin/categories', data)
  },

  updateCategory(id, data) {
    return apiClient.put(`/admin/categories/${id}`, data)
  },

  deleteCategory(id) {
    return apiClient.delete(`/admin/categories/${id}`)
  }
}
