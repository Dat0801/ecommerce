<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 py-8 sm:py-12">
    <div class="max-w-6xl mx-auto px-4">
      <div class="mb-8 bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6 flex items-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l2 2h5a2 2 0 012 2v5" />
          </svg>
        </div>
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">My Orders</h1>
          <p class="text-gray-600 text-sm mt-1">Track your purchases and their status</p>
        </div>
      </div>

      <div v-if="loading" class="bg-white/90 backdrop-blur-lg p-12 rounded-2xl shadow-xl border border-white/20 text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-indigo-600 mx-auto mb-4"></div>
        <p class="text-gray-600 font-medium">Loading orders...</p>
      </div>

      <div v-else-if="!orders.length" class="bg-white/90 backdrop-blur-lg p-12 rounded-2xl shadow-xl border border-white/20 text-center">
        <h3 class="text-2xl font-bold text-gray-900 mb-2">No orders yet</h3>
        <p class="text-gray-600 mb-6">Browse products and start shopping.</p>
        <router-link to="/products" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition-all">
          <span>Shop Now</span>
        </router-link>
      </div>

      <div v-else class="space-y-4">
        <div v-for="order in orders" :key="order.id" class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div class="space-y-1">
            <p class="text-sm text-gray-500">Order #{{ order.id }}</p>
            <h3 class="text-lg font-bold text-gray-900">${{ Number(order.total).toFixed(2) }}</h3>
            <p class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</p>
          </div>
          <div class="flex items-center gap-2">
            <span :class="statusClass(order.status)" class="px-3 py-1 rounded-full text-xs font-semibold">{{ order.status }}</span>
            <button @click="goToDetail(order.id)" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:shadow-lg text-sm font-semibold">View Details</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { getOrders } from '../services/order'

const orders = ref([])
const loading = ref(false)
const router = useRouter()

const fetchOrders = async () => {
  loading.value = true
  try {
    const { data } = await getOrders()
    orders.value = data?.data?.data || data?.data || []
  } catch (error) {
    console.error('Error fetching orders', error)
  } finally {
    loading.value = false
  }
}

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleDateString()
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

const goToDetail = (id) => {
  router.push(`/orders/${id}`)
}

onMounted(fetchOrders)
</script>
