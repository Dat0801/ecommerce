<template>
  <div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow">
      <h1 class="text-2xl font-bold text-gray-900 mb-6">Login</h1>
      
      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input 
            type="email" 
            id="email" 
            v-model="email"
            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none transition-colors"
            placeholder="user@example.com"
          >
        </div>
        
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
          <input 
            type="password" 
            id="password" 
            v-model="password"
            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none transition-colors"
            placeholder="••••••••"
          >
        </div>
        
        <button 
          type="submit"
          class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-medium py-2 rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all duration-200 transform hover:scale-[1.02]"
        >
          Login
        </button>
      </form>
      
      <p class="mt-6 text-center text-gray-600">
        Don't have an account? 
        <router-link to="/register" class="text-purple-600 hover:underline font-medium">Register</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')

const handleLogin = async () => {
  try {
    await authStore.login({
      email: email.value,
      password: password.value
    })
    
    // Check role and redirect accordingly
    if (authStore.user?.role === 'admin') {
      router.push('/admin')
    } else {
      router.push('/')
    }
  } catch (error) {
    console.error('Login failed:', error)
    alert(error.response?.data?.message || 'Login failed')
  }
}
</script>
