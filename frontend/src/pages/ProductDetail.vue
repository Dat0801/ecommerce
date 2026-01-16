<template>
  <div class="min-h-screen bg-gray-50 py-12">
    <!-- Toast Notification -->
    <div v-if="showNotification" class="fixed top-4 right-4 z-50 animate-bounce">
      <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 relative">
        <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <div class="flex-1">
          <p class="font-semibold">Added to Cart!</p>
          <p class="text-sm">{{ notificationMessage }}</p>
        </div>
        <button 
          @click="showNotification = false"
          class="flex-shrink-0 ml-2 hover:bg-green-600 rounded-full p-1 transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
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

      <div v-else class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 p-4 sm:p-6 md:p-8">
          <!-- Image Section -->
          <div class="relative group">
            <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 shadow-lg">
              <img 
                :src="product.image || 'https://placehold.co/600x600?text=No+Image'" 
                :alt="product.name" 
                @error="$event.target.src = 'https://placehold.co/600x600?text=' + encodeURIComponent(product.name)"
                class="h-full w-full object-cover object-center group-hover:scale-110 transition-transform duration-500"
              />
            </div>
            <!-- Badge -->
            <div class="absolute top-4 right-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg">
              Featured
            </div>
          </div>

          <!-- Product Info -->
          <div class="flex flex-col">
            <nav class="flex mb-4" aria-label="Breadcrumb">
              <ol class="flex items-center space-x-2 text-sm">
                <li>
                  <router-link to="/products" class="text-gray-500 hover:text-indigo-600 transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Home
                  </router-link>
                </li>
                <li>
                  <span class="text-gray-400">/</span>
                </li>
                <li v-if="product.category">
                  <span class="text-indigo-600 font-medium">{{ product.category.name }}</span>
                </li>
              </ol>
            </nav>

            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 mb-4 leading-tight">{{ product.name }}</h1>
            
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl">
              <div>
                <p class="text-sm text-gray-600 mb-1">Price</p>
                <p class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">${{ product.price }}</p>
              </div>
              <span 
                :class="['px-4 py-2 rounded-full text-sm font-bold shadow-md', 
                  product.stock > 0 ? 'bg-green-500 text-white' : 'bg-red-500 text-white']"
              >
                {{ product.stock > 0 ? '✓ In Stock (' + product.stock + ')' : '✕ Out of Stock' }}
              </span>
            </div>

            <div class="mb-6">
              <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Description
              </h3>
              <p class="text-gray-600 leading-relaxed">{{ product.description }}</p>
            </div>

            <div class="mt-auto border-t border-gray-200 pt-6">
              <div class="flex flex-col gap-6">
                <!-- Quantity Selector -->
                <div>
                  <label class="block text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                    </svg>
                    Quantity
                  </label>
                  <div class="flex items-center gap-4">
                    <div class="flex items-center bg-white border-2 border-gray-200 rounded-xl overflow-hidden shadow-md hover:border-indigo-400 transition-colors">
                      <button 
                        @click="decrementQuantity"
                        :disabled="quantity <= 1"
                        class="w-12 h-12 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 hover:from-indigo-50 hover:to-purple-50 text-gray-700 hover:text-indigo-600 font-bold text-xl disabled:opacity-50 disabled:cursor-not-allowed transition-all border-r-2 border-gray-200"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path>
                        </svg>
                      </button>
                      <input 
                        type="number" 
                        v-model.number="quantity"
                        @input="validateQuantity"
                        min="1"
                        :max="product.stock"
                        class="w-20 h-12 text-center text-xl font-bold text-gray-900 border-0 focus:ring-0 focus:outline-none [-moz-appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                      />
                      <button 
                        @click="incrementQuantity"
                        :disabled="quantity >= product.stock"
                        class="w-12 h-12 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 hover:from-indigo-50 hover:to-purple-50 text-gray-700 hover:text-indigo-600 font-bold text-xl disabled:opacity-50 disabled:cursor-not-allowed transition-all border-l-2 border-gray-200"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                        </svg>
                      </button>
                    </div>
                    <div class="flex-1 text-sm text-gray-600">
                      <p class="font-medium">Available: <span class="text-indigo-600 font-bold">{{ product.stock }}</span> units</p>
                    </div>
                  </div>
                </div>

                <!-- Add to Cart Button -->
                <button 
                  @click="addToCart"
                  :disabled="product.stock === 0 || isAdding"
                  class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl py-4 px-8 flex items-center justify-center text-lg font-bold hover:shadow-2xl transform hover:scale-[1.02] disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed disabled:transform-none transition-all duration-200 gap-3"
                >
                  <svg v-if="!isAdding" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                  <span v-if="isAdding" class="animate-spin mr-2">⏳</span>
                  {{ isAdding ? 'Adding...' : 'Add to Cart' }}
                </button>
              </div>
            </div>
            
            <div class="mt-6 p-4 bg-gray-50 rounded-xl">
              <div class="flex items-center gap-4 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                  <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                  </svg>
                  <span class="font-medium">SKU:</span> <span>{{ product.sku }}</span>
                </div>
              </div>
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

const incrementQuantity = () => {
  if (quantity.value < product.value.stock) {
    quantity.value++
  }
}

const decrementQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--
  }
}

const validateQuantity = () => {
  // Ensure quantity is a valid number
  if (isNaN(quantity.value) || quantity.value < 1) {
    quantity.value = 1
  } else if (quantity.value > product.value.stock) {
    quantity.value = product.value.stock
  } else {
    // Round to integer
    quantity.value = Math.floor(quantity.value)
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
