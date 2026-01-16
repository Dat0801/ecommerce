<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 py-6 sm:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">
        <!-- Sidebar - Category Filter -->
        <div class="w-full lg:w-64 flex-shrink-0">
          <!-- Categories Card -->
          <div class="bg-white/90 backdrop-blur-lg p-4 sm:p-6 rounded-2xl shadow-2xl border border-white/20 sticky top-24">
            <!-- Categories Section -->
            <div class="mb-6">
              <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <h2 class="text-base sm:text-lg font-bold text-gray-900">Categories</h2>
              </div>
              <div v-if="loadingCategories" class="text-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
              </div>
              <ul v-else class="space-y-1">
                <li>
                  <button 
                    @click="selectCategory(null)"
                    :class="[
                      'w-full text-left px-3 py-2.5 rounded-lg transition-all duration-200 flex items-center justify-between group',
                      !selectedCategory 
                        ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md font-semibold' 
                        : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 font-medium'
                    ]"
                  >
                    <span class="flex items-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                      </svg>
                      All Categories
                    </span>
                    <svg v-if="!selectedCategory" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                  </button>
                </li>
                <li v-for="category in categories" :key="category.id">
                  <button 
                    @click="selectCategory(category.id)"
                    :class="[
                      'w-full text-left px-3 py-2.5 rounded-lg transition-all duration-200 flex items-center justify-between group',
                      selectedCategory === category.id 
                        ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md font-semibold' 
                        : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 font-medium'
                    ]"
                  >
                    <span class="flex items-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                      </svg>
                      {{ category.name }}
                    </span>
                    <svg v-if="selectedCategory === category.id" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                  </button>
                  <!-- Nested children -->
                  <ul v-if="category.children && category.children.length > 0" class="pl-6 mt-1 space-y-1 border-l-2 border-indigo-100">
                    <li v-for="child in category.children" :key="child.id">
                      <button 
                        @click="selectCategory(child.id)"
                        :class="[
                          'w-full text-left px-3 py-2 rounded-lg text-sm transition-all duration-200 flex items-center justify-between',
                          selectedCategory === child.id 
                            ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-sm font-semibold' 
                            : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600'
                        ]"
                      >
                        <span class="flex items-center gap-2">
                          <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                          {{ child.name }}
                        </span>
                        <svg v-if="selectedCategory === child.id" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                      </button>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>

            <!-- Search Section -->
            <div class="pt-6 border-t border-gray-200">
              <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h2 class="text-base sm:text-lg font-bold text-gray-900">Search</h2>
              </div>
              <div class="relative">
                <input 
                  v-model="searchQuery" 
                  @input="handleSearch"
                  type="text" 
                  placeholder="Search products..." 
                  class="w-full pl-10 pr-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                />
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content - Product List -->
        <div class="flex-1 flex flex-col min-h-[600px]">
          <!-- Header -->
          <div class="mb-8 bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">Our Products</h1>
                <p class="text-gray-600 flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                  <span class="font-semibold">{{ totalProducts }}</span> products available
                </p>
              </div>
              <div class="flex items-center gap-2 text-sm text-gray-500">
                <div class="flex items-center gap-1 px-3 py-2 bg-indigo-50 rounded-lg">
                  <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                  </svg>
                  <span class="font-medium text-indigo-600">Filtered</span>
                </div>
              </div>
            </div>
          </div>

          <div v-if="loadingProducts" class="flex justify-center items-center py-20">
            <div class="text-center">
              <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-indigo-600 mx-auto mb-4"></div>
              <p class="text-gray-600 font-medium">Loading amazing products...</p>
            </div>
          </div>

          <div v-else-if="products.length === 0" class="text-center py-20 bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20">
            <div class="w-20 h-20 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No Products Found</h3>
            <p class="text-gray-600 mb-6">Try adjusting your filters or search terms</p>
            <button @click="selectCategory(null)" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full font-semibold hover:shadow-lg transition-all">
              Clear Filters
            </button>
          </div>

          <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6 auto-rows-max">
            <div 
              v-for="product in products" 
              :key="product.id" 
              class="group bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 overflow-hidden hover:shadow-2xl transition-all duration-300 cursor-pointer transform hover:-translate-y-2 h-fit"
              @click="goToProductDetail(product.id)"
            >
              <div class="relative aspect-w-1 aspect-h-1 w-full overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200">
                <img 
                  :src="product.image || 'https://placehold.co/400x400?text=No+Image'" 
                  :alt="product.name" 
                  @error="$event.target.src = 'https://placehold.co/400x400?text=' + encodeURIComponent(product.name)"
                  class="h-48 w-full object-cover object-center group-hover:scale-110 transition-transform duration-700"
                />
                <!-- Badge -->
                <div class="absolute top-3 right-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                  NEW
                </div>
              </div>
              <div class="p-5">
                <h3 class="text-lg font-bold text-gray-900 mb-2 truncate group-hover:text-indigo-600 transition-colors">
                  {{ product.name }}
                </h3>
                <p class="text-sm text-gray-500 mb-1">{{ product.category?.name }}</p>
                <div class="mt-4 flex items-center justify-between gap-3">
                  <p class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">${{ product.price }}</p>
                  <button 
                    @click.stop="addToCart(product)"
                    :disabled="product.stock === 0"
                    class="flex items-center gap-1.5 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-bold rounded-xl hover:shadow-lg disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed transform hover:scale-105 transition-all duration-200 z-10 relative"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>{{ product.stock === 0 ? 'Sold Out' : 'Add' }}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="pagination.last_page > 1" class="mt-auto pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Mobile: Simple pagination -->
            <div class="sm:hidden flex items-center justify-center gap-2 w-full">
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="flex items-center justify-center w-10 h-10 rounded-lg border-2 border-indigo-300 bg-white text-indigo-600 font-semibold hover:bg-indigo-50 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
              </button>
              <span class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-semibold shadow-md">
                {{ pagination.current_page }} / {{ pagination.last_page }}
              </span>
              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="flex items-center justify-center w-10 h-10 rounded-lg border-2 border-indigo-300 bg-white text-indigo-600 font-semibold hover:bg-indigo-50 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>

            <!-- Desktop: Full pagination -->
            <div class="hidden sm:flex items-center justify-between w-full">
              <!-- Previous Button -->
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="flex items-center gap-2 px-4 py-2 rounded-lg border-2 border-indigo-300 bg-white text-indigo-600 font-semibold hover:bg-indigo-50 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="hidden md:inline">Previous</span>
              </button>

              <!-- Page Numbers -->
              <nav class="flex items-center gap-1" aria-label="Pagination">
                <button
                  v-for="page in getPageNumbers()"
                  :key="page"
                  @click="page !== '...' && changePage(page)"
                  :class="[
                    'min-w-[40px] h-10 px-3 rounded-lg font-semibold transition-all',
                    page === pagination.current_page
                      ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg scale-110'
                      : page === '...'
                      ? 'bg-transparent text-gray-400 cursor-default'
                      : 'bg-white border-2 border-gray-200 text-gray-700 hover:border-indigo-300 hover:text-indigo-600 hover:bg-indigo-50'
                  ]"
                  :disabled="page === '...'"
                >
                  {{ page }}
                </button>
              </nav>

              <!-- Next Button -->
              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="flex items-center gap-2 px-4 py-2 rounded-lg border-2 border-indigo-300 bg-white text-indigo-600 font-semibold hover:bg-indigo-50 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
              >
                <span class="hidden md:inline">Next</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import productService from '../services/product.service'
import categoryService from '../services/category.service'
import { useCartStore } from '../stores/cart'

const router = useRouter()
const cartStore = useCartStore()

const products = ref([])
const categories = ref([])
const loadingProducts = ref(true)
const loadingCategories = ref(true)
const selectedCategory = ref(null)
const searchQuery = ref('')
const totalProducts = ref(0)
const pagination = ref({
  current_page: 1,
  last_page: 1
})

// Debounce search
const debouncedSearch = (fn, delay) => {
  let timeoutId
  return (...args) => {
    clearTimeout(timeoutId)
    timeoutId = setTimeout(() => fn(...args), delay)
  }
}

const fetchCategories = async () => {
  try {
    const response = await categoryService.getAllCategories()
    categories.value = response.data.data
  } catch (error) {
    console.error('Error fetching categories:', error)
  } finally {
    loadingCategories.value = false
  }
}

const fetchProducts = async (page = 1) => {
  loadingProducts.value = true
  try {
    const params = {
      page,
      per_page: 12,
      category_id: selectedCategory.value,
      search: searchQuery.value
    }
    const response = await productService.getAllProducts(params)
    products.value = response.data.data
    pagination.value = {
      current_page: response.data.meta.current_page,
      last_page: response.data.meta.last_page
    }
    totalProducts.value = response.data.meta.total
  } catch (error) {
    console.error('Error fetching products:', error)
  } finally {
    loadingProducts.value = false
  }
}

const selectCategory = (id) => {
  selectedCategory.value = id
  fetchProducts(1)
}

const handleSearch = debouncedSearch(() => {
  fetchProducts(1)
}, 500)

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchProducts(page)
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const goToProductDetail = (productId) => {
  router.push({ name: 'ProductDetail', params: { id: productId } })
}

const addToCart = async (product) => {
  if (product.stock === 0) return
  
  try {
    await cartStore.addItem(product.id, 1)
    // Optional: Show a brief success message
    console.log('Added to cart:', product.name)
  } catch (error) {
    console.error('Error adding to cart:', error)
    alert('Failed to add item to cart')
  }
}

const getPageNumbers = () => {
  const pages = []
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  const delta = 3 // Show 3 pages before and after current page
  
  // Always show first page
  pages.push(1)
  
  // Add ellipsis if needed
  if (current > delta + 2) {
    pages.push('...')
  }
  
  // Show pages around current page (more range)
  for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
    if (!pages.includes(i)) {
      pages.push(i)
    }
  }
  
  // Add ellipsis if needed
  if (current < last - (delta + 1)) {
    pages.push('...')
  }
  
  // Always show last page if there's more than 1 page
  if (last > 1 && !pages.includes(last)) {
    pages.push(last)
  }
  
  return pages
}

onMounted(() => {
  fetchCategories()
  fetchProducts()
})
</script>
