import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import apiClient from '../services/api'
import { useRouter } from 'vue-router'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token') || null)
  const isAuthenticated = computed(() => !!token.value)
  const router = useRouter()

  const setToken = (newToken) => {
    token.value = newToken
    if (newToken) {
      localStorage.setItem('auth_token', newToken)
    } else {
      localStorage.removeItem('auth_token')
    }
  }

  const setUser = (newUser) => {
    user.value = newUser
  }

  const login = async (credentials) => {
    try {
      const response = await apiClient.post('/login', credentials)
      setToken(response.data.token)
      setUser(response.data.user)
      return response.data
    } catch (error) {
      throw error
    }
  }

  const register = async (data) => {
    try {
      const response = await apiClient.post('/register', data)
      setToken(response.data.token)
      setUser(response.data.user)
      return response.data
    } catch (error) {
      throw error
    }
  }

  const fetchUser = async () => {
    if (!token.value) return
    try {
      const response = await apiClient.get('/user')
      setUser(response.data)
    } catch (error) {
      logout()
    }
  }

  const logout = async () => {
    try {
      await apiClient.post('/logout')
    } catch (error) {
      // Ignore error on logout
    } finally {
      user.value = null
      setToken(null)
      // We can redirect here or in component
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    setToken,
    setUser,
    login,
    register,
    fetchUser,
    logout,
  }
})
