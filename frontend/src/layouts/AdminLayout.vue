<template>
  <div class="min-h-screen bg-gray-100 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-white min-h-screen flex-shrink-0 fixed left-0 top-0 bottom-0 z-50">
      <div class="p-6 border-b border-gray-800">
        <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
          Admin Panel
        </h1>
      </div>
      <nav class="mt-6 px-4">
        <div class="space-y-2">
          <router-link 
            to="/admin" 
            class="block px-4 py-2 rounded-lg hover:bg-purple-500 transition-colors"
            active-class="bg-purple-600 text-white"
          >
            Dashboard
          </router-link>
          <router-link 
            to="/admin/products" 
            class="block px-4 py-2 rounded-lg hover:bg-purple-500 transition-colors"
            active-class="bg-purple-600 text-white"
          >
            Products
          </router-link>
          <router-link 
            to="/admin/categories" 
            class="block px-4 py-2 rounded-lg hover:bg-purple-500 transition-colors"
            active-class="bg-purple-600 text-white"
          >
            Categories
          </router-link>
        </div>
      </nav>
      <div class="absolute bottom-0 w-64 p-4 border-t border-gray-800">
        <button 
          @click="handleLogout" 
          class="w-full flex items-center justify-center gap-2 px-4 py-2 text-gray-400 hover:text-white hover:bg-purple-500 rounded-lg transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          Logout
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64 min-h-screen flex flex-col">
      <header class="bg-white shadow-sm h-16 flex items-center justify-between px-8 sticky top-0 z-40">
        <h2 class="text-xl font-semibold text-gray-800">{{ currentRouteName }}</h2>
        <div class="flex items-center gap-4">
          <span class="text-sm text-gray-600">Welcome, Admin</span>
          <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold">
            A
          </div>
        </div>
      </header>
      <div class="p-8 flex-1">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const currentRouteName = computed(() => {
  switch (route.name) {
    case 'AdminDashboard': return 'Dashboard'
    case 'AdminProducts': return 'Product Management'
    case 'AdminCategories': return 'Category Management'
    default: return 'Admin'
  }
})

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>
