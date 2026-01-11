import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token') || null)
  const isAuthenticated = computed(() => !!token.value)

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

  const logout = () => {
    user.value = null
    setToken(null)
  }

  return {
    user,
    token,
    isAuthenticated,
    setToken,
    setUser,
    logout,
  }
})
