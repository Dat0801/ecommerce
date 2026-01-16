<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 py-8 sm:py-12">
    <div class="max-w-6xl mx-auto px-4">
      <!-- Page Header -->
      <div class="mb-8 bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Shopping Cart</h1>
            <p class="text-gray-600 text-sm mt-1">{{ cartStore.items.length }} items in your cart</p>
          </div>
        </div>
      </div>
      
      <!-- Loading State -->
      <div v-if="cartStore.loading" class="bg-white/90 backdrop-blur-lg p-12 rounded-2xl shadow-xl border border-white/20 text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-indigo-600 mx-auto mb-4"></div>
        <p class="text-gray-600 font-medium">Loading your cart...</p>
      </div>
      
      <!-- Empty Cart State -->
      <div v-else-if="cartStore.items.length === 0" class="bg-white/90 backdrop-blur-lg p-12 rounded-2xl shadow-xl border border-white/20 text-center">
        <div class="w-24 h-24 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">Your cart is empty</h3>
        <p class="text-gray-600 mb-6">Looks like you haven't added any items yet</p>
        <router-link to="/products" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
          </svg>
          <span>Start Shopping</span>
        </router-link>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Desktop Table View -->
          <div class="hidden md:block bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <table class="w-full">
              <thead class="bg-gradient-to-r from-indigo-50 to-purple-50 border-b-2 border-indigo-200">
                <tr>
                  <th class="px-4 lg:px-6 py-4 text-left text-sm font-bold text-gray-900">Product</th>
                  <th class="px-4 lg:px-6 py-4 text-center text-sm font-bold text-gray-900">Quantity</th>
                  <th class="px-4 lg:px-6 py-4 text-right text-sm font-bold text-gray-900">Price</th>
                  <th class="px-4 lg:px-6 py-4 text-right text-sm font-bold text-gray-900">Subtotal</th>
                  <th class="px-4 lg:px-6 py-4 text-center text-sm font-bold text-gray-900">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="item in cartStore.items" :key="item.id" class="hover:bg-indigo-50/50 transition-colors">
                  <td class="px-6 py-5">
                    <div v-if="item.product" class="flex items-center space-x-4">
                      <div v-if="item.product.image" class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl overflow-hidden shadow-md flex-shrink-0">
                        <img :src="item.product.image" :alt="item.product.name" class="w-full h-full object-cover hover:scale-110 transition-transform">
                      </div>
                      <div>
                        <p class="font-bold text-gray-900">{{ item.product.name }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ item.product.category?.name }}</p>
                      </div>
                    </div>
                    <div v-else class="text-gray-500 italic">Product unavailable</div>
                  </td>
                  <td class="px-6 py-5 text-center">
                    <div class="flex items-center justify-center space-x-1">
                      <button 
                        @click="decrementQuantity(item)"
                        class="w-8 h-8 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 hover:from-indigo-100 hover:to-purple-100 rounded-lg font-bold text-gray-700 hover:text-indigo-600 transition-all shadow-sm hover:shadow-md"
                      >
                        -
                      </button>
                      <span class="w-10 text-center font-bold text-gray-900">{{ item.quantity }}</span>
                      <button 
                        @click="incrementQuantity(item)"
                        class="w-8 h-8 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 hover:from-indigo-100 hover:to-purple-100 rounded-lg font-bold text-gray-700 hover:text-indigo-600 transition-all shadow-sm hover:shadow-md"
                      >
                        +
                      </button>
                    </div>
                  </td>
                  <td class="px-6 py-5 text-right font-semibold text-gray-900">${{ Number(item.price).toFixed(2) }}</td>
                  <td class="px-6 py-5 text-right font-bold text-xl bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">${{ Number(item.subtotal).toFixed(2) }}</td>
                  <td class="px-4 lg:px-6 py-5 text-center">
                    <button 
                      @click="removeFromCart(item.id)"
                      class="p-2 text-red-600 hover:text-white hover:bg-red-600 rounded-lg font-medium text-sm transition-all"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Mobile Card View -->
          <div class="md:hidden space-y-4">
            <div v-for="item in cartStore.items" :key="item.id" class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-5">
              <div v-if="item.product" class="flex gap-4">
                <div v-if="item.product.image" class="w-24 h-24 flex-shrink-0 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl overflow-hidden shadow-md">
                  <img :src="item.product.image" :alt="item.product.name" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                  <h3 class="font-bold text-gray-900 line-clamp-2">{{ item.product.name }}</h3>
                  <p class="text-sm text-gray-500 mt-1">{{ item.product.category?.name }}</p>
                  <p class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mt-2">${{ Number(item.price).toFixed(2) }}</p>
                </div>
              </div>
              <div v-else class="text-gray-500 italic">Product unavailable</div>
              
              <div class="flex items-center justify-between mt-4 pt-4 border-t-2 border-gray-100">
                <div class="flex items-center space-x-1">
                  <button 
                    @click="decrementQuantity(item)"
                    class="w-9 h-9 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 hover:from-indigo-100 hover:to-purple-100 rounded-lg font-bold text-gray-700 hover:text-indigo-600 transition-all shadow-sm"
                  >
                    -
                  </button>
                  <span class="w-10 text-center font-bold text-gray-900">{{ item.quantity }}</span>
                  <button 
                    @click="incrementQuantity(item)"
                    class="w-9 h-9 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 hover:from-indigo-100 hover:to-purple-100 rounded-lg font-bold text-gray-700 hover:text-indigo-600 transition-all shadow-sm"
                  >
                    +
                  </button>
                </div>
                <div class="text-right">
                  <p class="text-xs text-gray-500 uppercase tracking-wide">Subtotal</p>
                  <p class="font-bold text-lg text-gray-900">${{ Number(item.subtotal).toFixed(2) }}</p>
                </div>
              </div>
              <button 
                @click="removeFromCart(item.id)"
                class="w-full mt-4 flex items-center justify-center gap-2 py-2.5 text-red-600 hover:text-white hover:bg-red-600 rounded-xl font-semibold text-sm transition-all border border-red-200 hover:border-red-600"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                <span>Remove Item</span>
              </button>
            </div>
          </div>

          <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-3">
            <router-link 
              to="/products" 
              class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/90 backdrop-blur-lg text-indigo-600 rounded-xl font-semibold hover:shadow-lg transition-all border border-indigo-200 hover:border-indigo-400"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
              </svg>
              <span>Continue Shopping</span>
            </router-link>
            <button 
              @click="handleClearCart"
              class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/90 backdrop-blur-lg text-red-600 rounded-xl font-semibold hover:shadow-lg transition-all border border-red-200 hover:border-red-400"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
              <span>Clear Cart</span>
            </button>
          </div>
        </div>

        <!-- Cart Summary -->
        <div class="lg:col-span-1">
          <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-6 lg:sticky lg:top-24">
            <div class="flex items-center gap-3 mb-6 pb-6 border-b-2 border-gray-100">
              <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
              </div>
              <h2 class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Order Summary</h2>
            </div>
            
            <div class="space-y-3 pb-4 border-b-2 border-gray-100">
              <div class="flex justify-between">
                <span class="text-gray-600 font-medium">Subtotal:</span>
                <span class="text-gray-900 font-semibold">${{ (cartStore.totalPrice).toFixed(2) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600 font-medium">Shipping:</span>
                <span class="text-green-600 font-semibold">Free</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600 font-medium">Tax:</span>
                <span class="text-gray-900 font-semibold">$0.00</span>
              </div>
            </div>

            <div class="mt-6 flex justify-between items-center">
              <span class="text-lg font-bold text-gray-900">Total:</span>
              <span class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">${{ (cartStore.totalPrice).toFixed(2) }}</span>
            </div>

            <button 
              @click="goToCheckout"
              class="w-full mt-6 flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-4 rounded-xl hover:shadow-2xl transition-all font-bold text-lg transform hover:scale-105"
            >
              <span>Proceed to Checkout</span>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
              </svg>
            </button>

            <!-- Benefits -->
            <div class="mt-6 space-y-3">
              <div class="flex items-center gap-3 text-sm text-gray-600">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <span class="font-medium">Free shipping on all orders</span>
              </div>
              <div class="flex items-center gap-3 text-sm text-gray-600">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                </div>
                <span class="font-medium">Secure payment guaranteed</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Confirmation Modal -->
    <div v-if="showConfirmModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 max-w-sm w-full animate-scale-up">
        <div class="p-6">
          <!-- Icon -->
          <div class="w-16 h-16 bg-gradient-to-br from-red-100 to-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M5 5h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"></path>
            </svg>
          </div>

          <!-- Title and Message -->
          <h3 class="text-xl font-bold text-gray-900 text-center mb-2">{{ confirmModalTitle }}</h3>
          <p class="text-gray-600 text-center mb-6">{{ confirmModalMessage }}</p>

          <!-- Buttons -->
          <div class="flex gap-3">
            <button
              @click="cancelConfirm"
              class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-900 font-semibold rounded-xl transition-colors"
            >
              Cancel
            </button>
            <button
              @click="confirmAction"
              class="flex-1 px-4 py-3 bg-gradient-to-r from-red-600 to-pink-600 hover:shadow-lg text-white font-semibold rounded-xl transition-all transform hover:scale-105"
            >
              Remove
            </button>
          </div>
        </div>
      </div>
    </div>  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useCartStore } from '../stores/cart'
import { useRouter } from 'vue-router'

const cartStore = useCartStore()
const router = useRouter()

const showConfirmModal = ref(false)
const confirmModalTitle = ref('')
const confirmModalMessage = ref('')
let pendingAction = null

onMounted(async () => {
  await cartStore.fetchCart()
})

const incrementQuantity = async (item) => {
  try {
    await cartStore.updateQuantity(item.id, item.quantity + 1)
  } catch (error) {
    console.error('Error updating quantity:', error)
  }
}

const decrementQuantity = async (item) => {
  try {
    if (item.quantity > 1) {
      await cartStore.updateQuantity(item.id, item.quantity - 1)
    } else {
      await removeFromCart(item.id)
    }
  } catch (error) {
    console.error('Error updating quantity:', error)
  }
}

const removeFromCart = (itemId) => {
  confirmModalTitle.value = 'Remove Item'
  confirmModalMessage.value = 'Are you sure you want to remove this item from your cart?'
  pendingAction = async () => {
    try {
      await cartStore.removeItem(itemId)
    } catch (error) {
      console.error('Error removing item:', error)
    }
  }
  showConfirmModal.value = true
}

const handleClearCart = () => {
  confirmModalTitle.value = 'Clear Cart'
  confirmModalMessage.value = 'Are you sure you want to remove all items from your cart?'
  pendingAction = async () => {
    try {
      await cartStore.clearCart()
    } catch (error) {
      console.error('Error clearing cart:', error)
    }
  }
  showConfirmModal.value = true
}

const confirmAction = async () => {
  if (pendingAction) {
    await pendingAction()
  }
  showConfirmModal.value = false
  pendingAction = null
}

const cancelConfirm = () => {
  showConfirmModal.value = false
  pendingAction = null
}

const goToCheckout = () => {
  router.push('/checkout')
}
</script>
