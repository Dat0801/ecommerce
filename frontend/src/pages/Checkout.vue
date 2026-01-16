<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 py-8 sm:py-12">
    <div class="max-w-5xl mx-auto px-4">
      <!-- Header -->
      <div class="mb-8 bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6 flex items-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18M3 7h18M6 7v13a1 1 0 001 1h10a1 1 0 001-1V7" />
          </svg>
        </div>
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Checkout</h1>
          <p class="text-gray-600 text-sm mt-1">Complete your order and review your cart</p>
        </div>
      </div>

      <div v-if="cartStore.loading" class="bg-white/90 backdrop-blur-lg p-12 rounded-2xl shadow-xl border border-white/20 text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-indigo-600 mx-auto mb-4"></div>
        <p class="text-gray-600 font-medium">Loading your cart...</p>
      </div>

      <div v-else-if="!cartStore.items.length" class="bg-white/90 backdrop-blur-lg p-12 rounded-2xl shadow-xl border border-white/20 text-center">
        <h3 class="text-2xl font-bold text-gray-900 mb-2">Your cart is empty</h3>
        <p class="text-gray-600 mb-6">Add some items before checking out.</p>
        <router-link to="/products" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition-all">
          <span>Browse Products</span>
        </router-link>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Form -->
        <div class="lg:col-span-2 bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6 space-y-5">
          <div class="flex items-center gap-2">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center text-white font-bold">1</div>
            <div>
              <p class="text-xs uppercase tracking-wide text-indigo-600 font-semibold">Step 1</p>
              <h2 class="text-lg font-bold text-gray-900">Shipping Information</h2>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
              <input v-model="form.shipping_name" type="text" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Your name" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
              <input v-model="form.shipping_email" type="email" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="you@example.com" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Phone</label>
              <input v-model="form.shipping_phone" type="text" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="(+84)" />
            </div>
            <div class="sm:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-1">Address</label>
              <textarea v-model="form.shipping_address" rows="3" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Street, city, country"></textarea>
            </div>
          </div>

          <div class="flex items-center gap-2 pt-2">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-600 to-pink-600 flex items-center justify-center text-white font-bold">2</div>
            <div>
              <p class="text-xs uppercase tracking-wide text-purple-600 font-semibold">Step 2</p>
              <h2 class="text-lg font-bold text-gray-900">Payment & Notes</h2>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Payment Method</label>
              <select v-model="form.payment_method" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="cod">Cash on Delivery</option>
                <option value="online">Online Payment</option>
              </select>
            </div>
            <div class="sm:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-1">Order Note</label>
              <textarea v-model="form.note" rows="3" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="E.g., please deliver between 9am-5pm"></textarea>
            </div>
          </div>

          <div v-if="errorMessage" class="rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm">{{ errorMessage }}</div>
          <div v-if="successMessage" class="rounded-xl border border-green-200 bg-green-50 text-green-700 px-4 py-3 text-sm">{{ successMessage }}</div>

          <button 
            :disabled="submitting"
            @click="placeOrder"
            class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-4 rounded-xl hover:shadow-2xl transition-all font-bold text-lg transform hover:scale-105 disabled:opacity-70"
          >
            <svg v-if="submitting" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8h4z"></path>
            </svg>
            <span>{{ submitting ? 'Placing Order...' : 'Place Order' }}</span>
          </button>
        </div>

        <!-- Summary -->
        <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6">
          <div class="flex items-start justify-between gap-4 mb-4">
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-indigo-600">Order Summary</p>
              <h3 class="text-xl font-bold text-gray-900">{{ cartStore.totalItems }} items</h3>
            </div>
            <div class="shrink-0 px-4 py-3 rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 text-white font-bold text-base leading-tight text-right max-w-[50%]">
              <div class="text-[clamp(16px,3vw,20px)] break-words">${{ cartStore.totalPrice.toFixed(2) }}</div>
            </div>
          </div>

          <div class="divide-y divide-gray-100">
            <div v-for="item in cartStore.items" :key="item.id" class="py-3 flex gap-3">
              <div class="w-14 h-14 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl overflow-hidden flex-shrink-0">
                <img v-if="item.product?.image" :src="item.product.image" :alt="item.product?.name" class="w-full h-full object-cover">
              </div>
              <div class="flex-1">
                <p class="font-semibold text-gray-900">{{ item.product?.name || 'Product' }}</p>
                <p class="text-sm text-gray-500">Qty: {{ item.quantity }} • ${{ Number(item.price).toFixed(2) }}</p>
              </div>
              <div class="text-right font-bold text-gray-900">${{ Number(item.subtotal || item.price * item.quantity).toFixed(2) }}</div>
            </div>
          </div>

          <div class="mt-6 space-y-2 text-sm text-gray-700">
            <div class="flex justify-between">
              <span>Subtotal</span>
              <span class="font-semibold">${{ cartStore.totalPrice.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between">
              <span>Shipping</span>
              <span class="text-green-600 font-semibold">Free</span>
            </div>
            <div class="flex justify-between">
              <span>Tax</span>
              <span class="font-semibold">$0.00</span>
            </div>
            <div class="flex justify-between text-lg pt-2">
              <span class="font-bold text-gray-900">Total</span>
              <span class="text-2xl font-extrabold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">${{ cartStore.totalPrice.toFixed(2) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '../stores/cart'
import { useAuthStore } from '../stores/auth'
import { checkout } from '../services/order'

const cartStore = useCartStore()
const router = useRouter()
const authStore = useAuthStore()
const submitting = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const form = ref({
  shipping_name: '',
  shipping_email: '',
  shipping_phone: '',
  shipping_address: '',
  payment_method: 'cod',
  note: ''
})

onMounted(() => {
  cartStore.fetchCart()
})

const placeOrder = async () => {
  if (!cartStore.items.length) {
    errorMessage.value = 'Your cart is empty.'
    return
  }

  errorMessage.value = ''
  successMessage.value = ''
  submitting.value = true

  try {
    const sessionId = localStorage.getItem('cart_session_id')
    const { data } = await checkout(form.value, sessionId)

    if (data.success) {
      successMessage.value = 'Order placed successfully!'
      await cartStore.fetchCart()
      setTimeout(() => {
        if (authStore.isAuthenticated) {
          router.push('/orders')
        } else {
          router.push('/')
        }
      }, 600)
    } else {
      errorMessage.value = data.message || 'Checkout failed. Please try again.'
    }
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Checkout failed. Please try again.'
  } finally {
    submitting.value = false
  }
}
</script>
