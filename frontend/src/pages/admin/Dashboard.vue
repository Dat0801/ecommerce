<template>
  <div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
      <StatCard title="Total Sales" :value="formatCurrency(stats.totals.sales)" iconColor="from-green-500 to-emerald-600" />
      <StatCard title="Total Orders" :value="stats.totals.orders" iconColor="from-blue-500 to-indigo-600" />
      <StatCard title="Products" :value="stats.totals.products" iconColor="from-purple-500 to-pink-600" />
      <StatCard title="Categories" :value="stats.totals.categories" iconColor="from-amber-500 to-orange-600" />
      <StatCard title="Users" :value="stats.totals.users" iconColor="from-slate-600 to-gray-800" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white/90 backdrop-blur-lg p-6 rounded-2xl shadow-xl border border-white/20">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-gray-700 font-bold text-lg">Recent Orders</h3>
          <span class="text-sm text-gray-500">Last 5</span>
        </div>
        <div v-if="loading" class="py-10 text-center text-gray-500">Loading...</div>
        <div v-else-if="!recentOrders.length" class="py-10 text-center text-gray-500">No orders yet</div>
        <div v-else class="divide-y divide-gray-100">
          <div v-for="order in recentOrders" :key="order.id" class="py-3 flex items-center justify-between">
            <div>
              <p class="font-semibold text-gray-900">Order #{{ order.id }}</p>
              <p class="text-xs text-gray-500">{{ formatDate(order.created_at) }}</p>
            </div>
            <div class="flex items-center gap-3">
              <span :class="statusClass(order.status)" class="px-3 py-1 rounded-full text-xs font-semibold capitalize">{{ order.status }}</span>
              <span class="font-bold text-gray-900">{{ formatCurrency(order.total) }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white/90 backdrop-blur-lg p-6 rounded-2xl shadow-xl border border-white/20">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-gray-700 font-bold text-lg">Low Stock</h3>
          <span class="text-sm text-gray-500">Below 5</span>
        </div>
        <div v-if="loading" class="py-10 text-center text-gray-500">Loading...</div>
        <div v-else-if="!lowStock.length" class="py-10 text-center text-gray-500">All good</div>
        <div v-else class="space-y-3">
          <div v-for="item in lowStock" :key="item.id" class="p-3 rounded-xl bg-gradient-to-r from-red-50 to-orange-50 border border-red-100 flex items-center justify-between">
            <div>
              <p class="font-semibold text-gray-900">{{ item.name }}</p>
              <p class="text-xs text-gray-500">ID: {{ item.id }}</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">Stock: {{ item.stock }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineComponent, h, onMounted, reactive, ref } from 'vue'
import { getDashboard } from '../../services/admin'

const StatCard = defineComponent({
  name: 'StatCard',
  props: {
    title: { type: String, required: true },
    value: { type: [String, Number], required: true },
    iconColor: { type: String, required: true },
  },
  setup(props) {
    return () => h('div', { class: 'relative overflow-hidden bg-white shadow-xl rounded-2xl p-6 border border-gray-100/80 transition-all hover:-translate-y-0.5 hover:shadow-2xl flex flex-col gap-3 min-h-[150px]' }, [
      h('div', { class: 'flex items-center justify-between' }, [
        h('h3', { class: 'text-gray-600 text-sm font-bold tracking-wide uppercase' }, props.title),
        h('div', { class: `w-12 h-12 rounded-xl flex items-center justify-center shadow-lg bg-gradient-to-br ${props.iconColor}` }, [
          h('svg', { class: 'w-6 h-6 text-white', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M4 6h16M4 10h16M10 14h10M10 18h10M4 14h2m-2 4h2' }),
          ]),
        ]),
      ]),
      h('p', { class: 'text-3xl md:text-4xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 via-purple-500 to-fuchsia-500 leading-tight break-words' }, props.value),
      h('div', { class: 'absolute inset-0 pointer-events-none' }, [
        h('div', { class: 'absolute -right-6 -bottom-10 w-32 h-32 rounded-full bg-purple-500/10 blur-2xl' }),
        h('div', { class: 'absolute -left-6 -top-8 w-24 h-24 rounded-full bg-indigo-500/10 blur-2xl' }),
      ]),
    ])
  },
})

const loading = ref(false)
const stats = reactive({ totals: { sales: 0, orders: 0, products: 0, categories: 0, users: 0 } })
const recentOrders = ref([])
const lowStock = ref([])

const fetchDashboard = async () => {
  loading.value = true
  try {
    const { data } = await getDashboard()
    const payload = data?.data || {}
    stats.totals = payload.totals || { sales: 0, orders: 0, products: 0, categories: 0, users: 0 }
    recentOrders.value = payload.recent_orders || []
    lowStock.value = payload.low_stock || []
  } catch (error) {
    console.error('Error loading dashboard', error)
  } finally {
    loading.value = false
  }
}

const formatCurrency = (value) => `$${Number(value || 0).toFixed(2)}`
const formatDate = (dateStr) => new Date(dateStr).toLocaleString()
const statusClass = (status) => {
  switch (status) {
    case 'completed': return 'bg-green-100 text-green-700'
    case 'shipped': return 'bg-blue-100 text-blue-700'
    case 'processing': return 'bg-amber-100 text-amber-700'
    case 'cancelled': return 'bg-red-100 text-red-700'
    default: return 'bg-purple-100 text-purple-700'
  }
}

onMounted(fetchDashboard)
</script>
