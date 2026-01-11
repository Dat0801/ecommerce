<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar - Category Filter -->
        <div class="w-full md:w-64 flex-shrink-0">
          <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Categories</h2>
            <div v-if="loadingCategories" class="text-gray-500">Loading...</div>
            <ul v-else class="space-y-2">
              <li>
                <button 
                  @click="selectCategory(null)"
                  :class="['w-full text-left px-2 py-1 rounded', !selectedCategory ? 'bg-indigo-50 text-indigo-600 font-medium' : 'text-gray-600 hover:bg-gray-50']"
                >
                  All Categories
                </button>
              </li>
              <li v-for="category in categories" :key="category.id">
                <button 
                  @click="selectCategory(category.id)"
                  :class="['w-full text-left px-2 py-1 rounded', selectedCategory === category.id ? 'bg-indigo-50 text-indigo-600 font-medium' : 'text-gray-600 hover:bg-gray-50']"
                >
                  {{ category.name }}
                </button>
                <!-- Nested children -->
                <ul v-if="category.children && category.children.length > 0" class="pl-4 mt-1 space-y-1">
                  <li v-for="child in category.children" :key="child.id">
                    <button 
                      @click="selectCategory(child.id)"
                      :class="['w-full text-left px-2 py-1 rounded text-sm', selectedCategory === child.id ? 'bg-indigo-50 text-indigo-600 font-medium' : 'text-gray-500 hover:bg-gray-50']"
                    >
                      {{ child.name }}
                    </button>
                  </li>
                </ul>
              </li>
            </ul>

            <!-- Price Filter (Optional bonus) -->
            <div class="mt-8">
               <h2 class="text-lg font-bold text-gray-900 mb-4">Search</h2>
               <input 
                 v-model="searchQuery" 
                 @input="handleSearch"
                 type="text" 
                 placeholder="Search products..." 
                 class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500"
               />
            </div>
          </div>
        </div>

        <!-- Main Content - Product List -->
        <div class="flex-1">
          <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Products</h1>
            <span class="text-gray-500">{{ totalProducts }} products found</span>
          </div>

          <div v-if="loadingProducts" class="flex justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
          </div>

          <div v-else-if="products.length === 0" class="text-center py-12 bg-white rounded-lg shadow-sm">
            <p class="text-gray-500">No products found.</p>
          </div>

          <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="product in products" :key="product.id" class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
              <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden bg-gray-200 xl:aspect-w-7 xl:aspect-h-8">
                <img 
                  :src="product.image || 'https://placehold.co/400x400?text=No+Image'" 
                  :alt="product.name" 
                  @error="$event.target.src = 'https://placehold.co/400x400?text=' + encodeURIComponent(product.name)"
                  class="h-48 w-full object-cover object-center group-hover:opacity-75"
                />
              </div>
              <div class="p-4">
                <h3 class="text-lg font-medium text-gray-900">
                  <router-link :to="{ name: 'ProductDetail', params: { id: product.id } }">
                    <span aria-hidden="true" class="absolute inset-0"></span>
                    {{ product.name }}
                  </router-link>
                </h3>
                <p class="mt-1 text-sm text-gray-500">{{ product.category?.name }}</p>
                <div class="mt-2 flex items-center justify-between">
                  <p class="text-lg font-bold text-indigo-600">${{ product.price }}</p>
                  <button class="px-3 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 z-10 relative">
                    Add to Cart
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="pagination.last_page > 1" class="mt-8 flex justify-center">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                Previous
              </button>
              <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                Page {{ pagination.current_page }} of {{ pagination.last_page }}
              </span>
              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                Next
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import productService from '../services/product.service'
import categoryService from '../services/category.service'

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

onMounted(() => {
  fetchCategories()
  fetchProducts()
})
</script>
