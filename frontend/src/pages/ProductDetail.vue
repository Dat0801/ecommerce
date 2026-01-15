<template>
  <div class="min-h-screen bg-gray-50 py-12">
    <!-- Toast Notification -->
    <div v-if="showNotification" class="fixed top-4 right-4 z-50 animate-bounce">
      <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <div>
          <p class="font-semibold">Added to Cart!</p>
          <p class="text-sm">{{ notificationMessage }}</p>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      </div>
      
      <div v-else-if="!product" class="text-center py-12">
        <h2 class="text-2xl font-bold text-gray-900">Product not found</h2>
        <router-link to="/products" class="text-indigo-600 hover:text-indigo-500 mt-4 inline-block">
          Back to Products
        </router-link>
      </div>

      <div v-else class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">
          <!-- Image Section -->
          <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-200">
            <img 
              :src="product.image || 'https://placehold.co/600x600?text=No+Image'" 
              :alt="product.name" 
              @error="$event.target.src = 'https://placehold.co/600x600?text=' + encodeURIComponent(product.name)"
              class="h-full w-full object-cover object-center"
            />
          </div>

          <!-- Product Info -->
          <div class="flex flex-col">
            <nav class="flex mb-4" aria-label="Breadcrumb">
              <ol class="flex items-center space-x-2">
                <li>
                  <router-link to="/products" class="text-gray-500 hover:text-gray-700">Products</router-link>
                </li>
                <li v-if="product.category">
                  <span class="text-gray-400">/</span>
                  <span class="text-gray-500">{{ product.category.name }}</span>
                </li>
              </ol>
            </nav>

            <h1 class="text-3xl font-extrabold text-gray-900 mb-4">{{ product.name }}</h1>
            
            <div class="flex items-center justify-between mb-6">
              <p class="text-3xl font-bold text-indigo-600">${{ product.price }}</p>
              <span 
                :class="['px-3 py-1 rounded-full text-sm font-medium', 
                  product.stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']"
              >
                {{ product.stock > 0 ? 'In Stock' : 'Out of Stock' }}
              </span>
            </div>

            <div class="prose prose-sm text-gray-500 mb-8">
              <p>{{ product.description }}</p>
            </div>

            <div class="mt-auto border-t border-gray-200 pt-8">
              <div class="flex items-center space-x-4">
                <div class="w-32">
                  <label for="quantity" class="sr-only">Quantity</label>
                  <select 
                    id="quantity" 
                    v-model="quantity"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                    <option v-for="n in Math.min(10, product.stock)" :key="n" :value="n">
                      {{ n }}
                    </option>
                  </select>
                </div>
                <button 
                  @click="addToCart"
                  :disabled="product.stock === 0 || isAdding"
                  class="flex-1 bg-indigo-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:bg-gray-400 disabled:cursor-not-allowed transition"
                >
                  <span v-if="isAdding" class="animate-spin mr-2">⏳</span>
                  {{ isAdding ? 'Adding...' : 'Add to Cart' }}
                </button>
              </div>
            </div>
            
            <div class="mt-4 text-sm text-gray-500">
              <p>SKU: {{ product.sku }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import productService from '../services/product.service'
import { useCartStore } from '../stores/cart'

const route = useRoute()
const cartStore = useCartStore()
const product = ref(null)
const loading = ref(true)
const quantity = ref(1)
const isAdding = ref(false)
const showNotification = ref(false)
const notificationMessage = ref('')

const fetchProduct = async () => {
  try {
    const response = await productService.getProduct(route.params.id)
    product.value = response.data.data
  } catch (error) {
    console.error('Error fetching product:', error)
  } finally {
    loading.value = false
  }
}

const addToCart = async () => {
  isAdding.value = true
  try {
    await cartStore.addItem(product.value.id, parseInt(quantity.value))
    
    // Show notification
    notificationMessage.value = `${quantity.value} x ${product.value.name} added to cart! (Total: ${cartStore.totalItems} items)`
    showNotification.value = true
    
    // Hide notification after 3 seconds
    setTimeout(() => {
      showNotification.value = false
    }, 3000)
    
    // Reset quantity
    quantity.value = 1
  } catch (error) {
    console.error('Error adding to cart:', error)
    notificationMessage.value = 'Failed to add item to cart'
    showNotification.value = true
    setTimeout(() => {
      showNotification.value = false
    }, 3000)
  } finally {
    isAdding.value = false
  }
}

onMounted(() => {
  fetchProduct()
})
</script>
