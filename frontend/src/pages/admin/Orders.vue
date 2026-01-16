<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 py-8 sm:py-12">
    <div class="max-w-6xl mx-auto px-4">
      <div class="mb-8 bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
            </svg>
          </div>
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Orders</h1>
            <p class="text-gray-600 text-sm mt-1">Manage customer orders and statuses</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <div class="relative">
            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-indigo-200 via-purple-200 to-pink-200 blur"></div>
            <select 
              v-model="statusFilter" 
              @change="handleFilterChange" 
              class="relative px-4 py-2.5 rounded-xl border-2 border-white/70 bg-white/80 backdrop-blur focus:ring-2 focus:ring-indigo-500 shadow-sm"
            >
              <option value="">All Statuses</option>
              <option v-for="status in statusOptions" :key="status.value" :value="status.value">
                {{ status.label }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <div v-if="loading" class="bg-white/90 backdrop-blur-lg p-12 rounded-2xl shadow-xl border border-white/20 text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-indigo-600 mx-auto mb-4"></div>
        <p class="text-gray-600 font-medium">Loading orders...</p>
      </div>

      <div v-else class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-gradient-to-r from-indigo-50 to-purple-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-700">Order</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-700">Customer</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-700">Total</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-700">Status</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-700">Date</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-700">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="order in orders" :key="order.id" class="hover:bg-indigo-50/40 transition-colors">
                <td class="px-4 py-4">
                  <p class="font-semibold text-gray-900">#{{ order.id }}</p>
                  <p class="text-xs text-gray-500">{{ order.total_items }} items</p>
                </td>
                <td class="px-4 py-4">
                  <p class="font-semibold text-gray-900">{{ order.shipping_name }}</p>
                  <p class="text-xs text-gray-500">{{ order.shipping_email || '—' }}</p>
                </td>
                <td class="px-4 py-4 font-bold text-gray-900">${{ Number(order.total).toFixed(2) }}</td>
                <td class="px-4 py-4">
                  <span :class="statusClass(order.status)" class="px-3 py-1 rounded-full text-xs font-semibold">{{ order.status }}</span>
                </td>
                <td class="px-4 py-4 text-sm text-gray-600">{{ formatDate(order.created_at) }}</td>
                <td class="px-4 py-4">
                  <div class="flex items-center gap-2">
                    <button @click="openOrder(order)" class="px-3 py-2 bg-white border border-gray-200 rounded-lg hover:border-indigo-300 hover:text-indigo-600 text-sm font-semibold shadow-sm">View</button>
                    <div class="relative">
                      <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-indigo-200 via-purple-200 to-pink-200 blur"></div>
                      <select 
                        v-model="order.status" 
                        class="relative pl-3 pr-9 py-2 rounded-lg border-2 border-white/70 bg-white/80 backdrop-blur focus:ring-2 focus:ring-indigo-500 text-sm font-semibold capitalize shadow-sm"
                      >
                        <option v-for="status in statusOptions" :key="status.value" :value="status.value">
                          {{ status.label }}
                        </option>
                      </select>
                    </div>
                    <button @click="updateStatus(order)" class="px-3 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:shadow-lg text-sm font-semibold whitespace-nowrap">Save</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-200 bg-white">
          <div class="text-sm text-gray-600">Page {{ meta.current_page }} of {{ meta.last_page }} • Total {{ meta.total }}</div>
          <div class="flex items-center gap-2">
            <button 
              @click="goToPage(meta.current_page - 1)"
              :disabled="meta.current_page <= 1"
              class="px-3 py-2 rounded-lg border border-gray-200 text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >Prev</button>
            <button 
              @click="goToPage(meta.current_page + 1)"
              :disabled="meta.current_page >= meta.last_page"
              class="px-3 py-2 rounded-lg border border-gray-200 text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >Next</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Detail Modal -->
  <div v-if="showDetail && selectedOrder" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/30 max-w-3xl w-full max-h-[90vh] overflow-y-auto">
      <div class="p-6 flex items-start justify-between gap-4 border-b border-gray-100">
        <div>
          <p class="text-sm text-gray-500">Order #{{ selectedOrder.id }}</p>
          <h3 class="text-2xl font-bold text-gray-900">${{ Number(selectedOrder.total).toFixed(2) }}</h3>
          <div class="flex items-center gap-2 mt-2">
            <span :class="statusClass(selectedOrder.status)" class="px-3 py-1 rounded-full text-xs font-semibold capitalize">{{ selectedOrder.status }}</span>
            <span class="text-sm text-gray-500">{{ formatDate(selectedOrder.created_at) }}</span>
          </div>
        </div>
        <button @click="closeDetail" class="p-2 rounded-full hover:bg-gray-100 text-gray-500">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
          <div>
            <p class="text-xs uppercase text-gray-500 font-semibold">Customer</p>
            <p class="text-lg font-bold text-gray-900">{{ selectedOrder.shipping_name }}</p>
            <p class="text-sm text-gray-600">{{ selectedOrder.shipping_email || '—' }}</p>
            <p class="text-sm text-gray-600">{{ selectedOrder.shipping_phone || '—' }}</p>
          </div>
          <div>
            <p class="text-xs uppercase text-gray-500 font-semibold">Shipping Address</p>
            <p class="text-sm font-semibold text-gray-900">{{ selectedOrder.shipping_address }}</p>
            <p v-if="selectedOrder.note" class="text-sm text-gray-600 mt-2">Note: {{ selectedOrder.note }}</p>
          </div>
          <div class="grid grid-cols-2 gap-3 text-sm text-gray-700">
            <div>
              <p class="text-xs uppercase text-gray-500 font-semibold">Items</p>
              <p class="font-bold">{{ selectedOrder.total_items }}</p>
            </div>
            <div>
              <p class="text-xs uppercase text-gray-500 font-semibold">Payment</p>
              <p class="font-bold capitalize">{{ selectedOrder.payment_method }}</p>
            </div>
            <div>
              <p class="text-xs uppercase text-gray-500 font-semibold">Payment Status</p>
              <p class="font-bold capitalize">{{ selectedOrder.payment_status }}</p>
            </div>
            <div>
              <p class="text-xs uppercase text-gray-500 font-semibold">Current Status</p>
              <p class="font-bold capitalize">{{ selectedOrder.status }}</p>
            </div>
          </div>
        </div>

        <div class="bg-gray-50 rounded-xl border border-gray-100 p-4 space-y-3">
          <h4 class="text-sm font-bold text-gray-800">Items</h4>
          <div v-for="item in selectedOrder.items || []" :key="item.id" class="flex items-center gap-3 p-3 rounded-lg bg-white shadow-sm border border-gray-100">
            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden flex-shrink-0">
              <img v-if="item.product_image" :src="item.product_image" :alt="item.product_name" class="w-full h-full object-cover" />
            </div>
            <div class="flex-1">
              <p class="font-semibold text-gray-900">{{ item.product_name }}</p>
              <p class="text-xs text-gray-500">Qty: {{ item.quantity }} • ${{ Number(item.product_price).toFixed(2) }}</p>
            </div>
            <div class="text-right font-bold text-gray-900">${{ Number(item.subtotal).toFixed(2) }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { adminGetOrders, adminUpdateOrderStatus } from '../../services/order'

const orders = ref([])
const loading = ref(false)
const page = ref(1)
const perPage = ref(10)
const meta = ref({ current_page: 1, last_page: 1, total: 0 })
const statusOptions = [
  { value: 'pending', label: 'Pending — Awaiting payment' },
  { value: 'processing', label: 'Processing — Preparing order' },
  { value: 'paid', label: 'Paid — Payment received' },
  { value: 'shipped', label: 'Shipped — On the way' },
  { value: 'completed', label: 'Completed — Delivered' },
  { value: 'cancelled', label: 'Cancelled — Closed' },
]
const statusFilter = ref('')
const showDetail = ref(false)
const selectedOrder = ref(null)

const fetchOrders = async () => {
  loading.value = true
  try {
    const { data } = await adminGetOrders({ status: statusFilter.value, page: page.value, per_page: perPage.value })
    const payload = data?.data || {}
    orders.value = payload.data || payload?.data || []
    meta.value = {
      current_page: payload.current_page || 1,
      last_page: payload.last_page || 1,
      total: payload.total || (payload.data ? payload.data.length : 0)
    }
  } catch (error) {
    console.error('Error fetching admin orders', error)
  } finally {
    loading.value = false
  }
}

const goToPage = (p) => {
  if (p < 1 || p > meta.value.last_page) return
  page.value = p
  fetchOrders()
}

const handleFilterChange = () => {
  page.value = 1
  fetchOrders()
}

const updateStatus = async (order) => {
  try {
    await adminUpdateOrderStatus(order.id, order.status)
  } catch (error) {
    console.error('Error updating status', error)
  }
}

const openOrder = (order) => {
  selectedOrder.value = order
  showDetail.value = true
}

const closeDetail = () => {
  showDetail.value = false
  selectedOrder.value = null
}

const formatDate = (dateStr) => new Date(dateStr).toLocaleString()

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

onMounted(fetchOrders)
</script>
