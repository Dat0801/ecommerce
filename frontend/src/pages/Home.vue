<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50">
    <!-- Hero Section -->
    <div class="relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 opacity-10 animate-gradient"></div>
      <div class="max-w-7xl mx-auto px-4 py-20 sm:py-28 relative">
        <div class="text-center space-y-6 animate-fade-in">
          <h1 class="text-5xl sm:text-6xl md:text-7xl font-extrabold">
            <span class="bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 bg-clip-text text-transparent">
              Welcome to E-Commerce
            </span>
          </h1>
          <p class="text-xl sm:text-2xl text-gray-700 max-w-2xl mx-auto font-medium">
            Experience modern and convenient shopping
          </p>
          <div class="flex justify-center gap-4 pt-4">
            <router-link 
              to="/products" 
              class="px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-full font-semibold hover:shadow-xl transform hover:scale-105 transition-all duration-300"
            >
              Shop Now
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Featured Products Section -->
    <div class="max-w-7xl mx-auto px-4 py-16">
      <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Featured Products</h2>
      <div v-if="loading" class="text-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto"></div>
      </div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        <div v-for="product in products" :key="product.id" class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
          <div class="h-48 overflow-hidden bg-gray-200">
            <img 
              :src="product.image" 
              :alt="product.name" 
              @error="$event.target.src = 'https://placehold.co/600x400?text=' + encodeURIComponent(product.name)"
              class="w-full h-full object-cover transform hover:scale-110 transition-transform duration-500"
            >
          </div>
          <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2 truncate">{{ product.name }}</h3>
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ product.description }}</p>
            <div class="flex items-center justify-between">
              <span class="text-xl font-bold text-purple-600">${{ product.price }}</span>
              <router-link 
                :to="`/products/${product.id}`"
                class="px-4 py-2 bg-purple-100 text-purple-600 rounded-lg text-sm font-semibold hover:bg-purple-200 transition-colors"
              >
                View
              </router-link>
            </div>
          </div>
        </div>
      </div>
      <div class="text-center mt-12 mb-16">
        <router-link 
          to="/products" 
          class="inline-flex items-center text-purple-600 font-semibold hover:text-purple-700"
        >
          View All Products
          <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
          </svg>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import apiClient from '../services/api'

const authStore = useAuthStore()
const products = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const response = await apiClient.get('/products')
    products.value = response.data.data.slice(0, 4) // Show first 4 products
  } catch (error) {
    console.error('Error fetching products:', error)
  } finally {
    loading.value = false
  }
})
</script>