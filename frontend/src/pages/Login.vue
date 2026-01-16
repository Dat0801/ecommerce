<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-100 via-pink-50 to-indigo-100 py-8 sm:py-12 px-4 flex items-center justify-center">
    <div class="max-w-md w-full">
      <!-- Card -->
      <div class="bg-white/90 backdrop-blur-lg p-8 sm:p-10 rounded-2xl shadow-2xl border border-white/20">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
          </div>
          <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">Welcome Back</h1>
          <p class="text-gray-600">Sign in to continue shopping</p>
        </div>
        
        <form @submit.prevent="handleLogin" class="space-y-5">
          <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                </svg>
              </div>
              <input 
                type="email" 
                id="email" 
                v-model="email"
                required
                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all"
                placeholder="you@example.com"
              >
            </div>
          </div>
          
          <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
              </div>
              <input 
                type="password" 
                id="password" 
                v-model="password"
                required
                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all"
                placeholder="••••••••"
              >
            </div>
          </div>
          
          <button 
            type="submit"
            class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-3 rounded-xl hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
            </svg>
            Sign In
          </button>
        </form>
        
        <div class="mt-6 text-center">
          <p class="text-gray-600">
            Don't have an account? 
            <router-link to="/register" class="text-purple-600 hover:text-purple-700 font-semibold hover:underline transition-colors">Create Account</router-link>
          </p>
        </div>
      </div>
      
      <!-- Bottom decoration -->
      <div class="text-center mt-6 text-gray-600 text-sm">
        <p>© 2026 E-Commerce. All rights reserved.</p>
      </div>
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
