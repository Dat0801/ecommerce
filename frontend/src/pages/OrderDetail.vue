<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 py-8 sm:py-12">
    <div class="max-w-5xl mx-auto px-4">
      <div class="mb-8 bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6 flex items-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Order Details</h1>
          <p class="text-gray-600 text-sm mt-1">Order #{{ order?.id }}</p>
        </div>
      </div>

      <div v-if="loading" class="bg-white/90 backdrop-blur-lg p-12 rounded-2xl shadow-xl border border-white/20 text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-indigo-600 mx-auto mb-4"></div>
        <p class="text-gray-600 font-medium">Loading order...</p>
      </div>

      <div v-else-if="!order" class="bg-white/90 backdrop-blur-lg p-12 rounded-2xl shadow-xl border border-white/20 text-center">
        <h3 class="text-2xl font-bold text-gray-900 mb-2">Order not found</h3>
        <router-link to="/orders" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition-all">
          Back to Orders
        </router-link>
      </div>

      <div v-else class="space-y-6">
        <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <p class="text-sm text-gray-500">Total</p>
            <p class="text-2xl font-bold text-gray-900">${{ Number(order.total).toFixed(2) }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-500">Status</p>
            <span :class="statusClass(order.status)" class="inline-flex px-3 py-1 rounded-full text-xs font-semibold">{{ order.status }}</span>
          </div>
          <div>
            <p class="text-sm text-gray-500">Date</p>
            <p class="text-lg font-semibold text-gray-900">{{ formatDate(order.created_at) }}</p>
          </div>
        </div>

        <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Shipping Information</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-700">
            <div>
              <p class="text-sm text-gray-500">Name</p>
              <p class="font-semibold">{{ order.shipping_name }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Phone</p>
              <p class="font-semibold">{{ order.shipping_phone || '—' }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Email</p>
              <p class="font-semibold">{{ order.shipping_email || '—' }}</p>
            </div>
            <div class="sm:col-span-2">
              <p class="text-sm text-gray-500">Address</p>
              <p class="font-semibold">{{ order.shipping_address }}</p>
            </div>
            <div class="sm:col-span-2" v-if="order.note">
              <p class="text-sm text-gray-500">Note</p>
              <p class="font-semibold">{{ order.note }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Items</h3>
          <div class="divide-y divide-gray-100">
            <div v-for="item in order.items" :key="item.id" class="py-4 flex items-center gap-4">
              <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden flex-shrink-0">
                <img v-if="item.product_image" :src="item.product_image" :alt="item.product_name" class="w-full h-full object-cover" />
              </div>
              <div class="flex-1">
                <p class="font-semibold text-gray-900">{{ item.product_name }}</p>
                <p class="text-sm text-gray-500">Qty: {{ item.quantity }} • ${{ Number(item.product_price).toFixed(2) }}</p>
              </div>
              <div class="text-right font-bold text-gray-900">${{ Number(item.subtotal).toFixed(2) }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { getOrderDetail } from '../services/order'

const route = useRoute()
const order = ref(null)
const loading = ref(false)

const fetchOrder = async () => {
  loading.value = true
  try {
    const { data } = await getOrderDetail(route.params.id)
    order.value = data?.data || null
  } catch (error) {
    console.error('Error fetching order detail', error)
    order.value = null
  } finally {
    loading.value = false
  }
}

const statusClass = (status) => {
  switch (status) {
    case 'completed':
      return 'bg-green-100 text-green-700'
    case 'shipped':
      return 'bg-blue-100 text-blue-700'
    case 'processing':
      return 'bg-amber-100 text-amber-700'
    case 'cancelled':
      return 'bg-red-100 text-red-700'
    default:
      return 'bg-purple-100 text-purple-700'
  }
}

const formatDate = (dateStr) => new Date(dateStr).toLocaleString()

onMounted(fetchOrder)
</script>
