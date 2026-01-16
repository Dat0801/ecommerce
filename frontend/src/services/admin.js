import apiClient from './api'

export const getDashboard = () => apiClient.get('/admin/dashboard')
