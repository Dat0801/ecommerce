import apiClient from './api'

export const checkout = async (payload, sessionId) => {
  return apiClient.post('/checkout', payload, {
    headers: {
      ...(sessionId ? { 'X-Session-Id': sessionId } : {})
    }
  })
}

export const getOrders = async (params = {}) => {
  return apiClient.get('/orders', { params })
}

export const getOrderDetail = async (id) => {
  return apiClient.get(`/orders/${id}`)
}

export const adminGetOrders = async (params = {}) => {
  return apiClient.get('/admin/orders', { params })
}

export const adminUpdateOrderStatus = async (id, status) => {
  return apiClient.put(`/admin/orders/${id}/status`, { status })
}
