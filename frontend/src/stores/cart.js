import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import apiClient from '../services/api'
import { v4 as uuidv4 } from 'uuid'

export const useCartStore = defineStore('cart', () => {
  const items = ref([])
  const loading = ref(false)
  const sessionId = ref(getSessionId())

  const totalItems = computed(() => {
    return items.value.reduce((sum, item) => sum + item.quantity, 0)
  })

  const totalPrice = computed(() => {
    return items.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
  })

  // Get or create session ID for guest users
  function getSessionId() {
    let id = localStorage.getItem('cart_session_id')
    if (!id) {
      id = uuidv4()
      localStorage.setItem('cart_session_id', id)
    }
    return id
  }

  // Fetch cart from API
  const fetchCart = async () => {
    loading.value = true
    try {
      const response = await apiClient.get('/cart', {
        headers: {
          'X-Session-Id': sessionId.value
        }
      })
      
      if (response.data.success && response.data.data) {
        items.value = response.data.data.items || []
      }
    } catch (error) {
      console.error('Error fetching cart:', error)
    } finally {
      loading.value = false
    }
  }

  // Add item to cart
  const addItem = async (productId, quantity = 1) => {
    loading.value = true
    try {
      const response = await apiClient.post('/cart', {
        product_id: productId,
        quantity: quantity
      }, {
        headers: {
          'X-Session-Id': sessionId.value
        }
      })

      if (response.data.success) {
        items.value = response.data.data.items || []
        return response.data
      }
    } catch (error) {
      console.error('Error adding to cart:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  // Update item quantity
  const updateQuantity = async (itemId, quantity) => {
    loading.value = true
    try {
      const response = await apiClient.put(`/cart/${itemId}`, {
        quantity: quantity
      }, {
        headers: {
          'X-Session-Id': sessionId.value
        }
      })

      if (response.data.success) {
        items.value = response.data.data?.items || []
      }
    } catch (error) {
      console.error('Error updating cart:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  // Remove item from cart
  const removeItem = async (itemId) => {
    loading.value = true
    try {
      const response = await apiClient.delete(`/cart/${itemId}`, {
        headers: {
          'X-Session-Id': sessionId.value
        }
      })

      if (response.data.success) {
        items.value = response.data.data?.items || []
      }
    } catch (error) {
      console.error('Error removing from cart:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  // Clear cart
  const clearCart = async () => {
    loading.value = true
    try {
      const response = await apiClient.delete('/cart', {
        headers: {
          'X-Session-Id': sessionId.value
        }
      })

      if (response.data.success) {
        items.value = []
      }
    } catch (error) {
      console.error('Error clearing cart:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  // Merge guest cart to user cart after login
  const mergeCart = async () => {
    loading.value = true
    try {
      const response = await apiClient.post('/cart/merge', {
        session_id: sessionId.value
      })

      if (response.data.success) {
        items.value = response.data.data?.items || []
        // Clear guest session
        localStorage.removeItem('cart_session_id')
        sessionId.value = uuidv4()
        localStorage.setItem('cart_session_id', sessionId.value)
      }
    } catch (error) {
      console.error('Error merging cart:', error)
    } finally {
      loading.value = false
    }
  }

  return {
    items,
    loading,
    totalItems,
    totalPrice,
    fetchCart,
    addItem,
    updateQuantity,
    removeItem,
    clearCart,
    mergeCart,
  }
})
