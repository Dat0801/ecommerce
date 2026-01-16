<template>
  <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20">
    <div class="p-6 border-b-2 border-gradient-to-r from-indigo-100 to-purple-100 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
          </svg>
        </div>
        <div>
          <h3 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">Products</h3>
          <p class="text-sm text-gray-600">Manage your product inventory</p>
        </div>
      </div>
      <button 
        @click="openCreateModal"
        class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add Product
      </button>
    </div>
    <div class="p-6">
      <div v-if="loading" class="flex flex-col items-center justify-center py-16">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-purple-600 mb-4"></div>
        <p class="text-gray-600 font-medium">Loading products...</p>
      </div>
      <div v-else-if="error" class="text-center py-16">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <p class="text-red-600 font-semibold">{{ error }}</p>
      </div>
      <div v-else class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="w-full text-left">
          <thead>
            <tr class="bg-gradient-to-r from-purple-50 to-pink-50 border-b-2 border-purple-200">
              <th class="pb-4 pt-4 pl-4 text-sm font-bold text-gray-700 uppercase tracking-wider">ID</th>
              <th class="pb-4 pt-4 text-sm font-bold text-gray-700 uppercase tracking-wider">Product</th>
              <th class="pb-4 pt-4 text-sm font-bold text-gray-700 uppercase tracking-wider">Category</th>
              <th class="pb-4 pt-4 text-sm font-bold text-gray-700 uppercase tracking-wider">Price</th>
              <th class="pb-4 pt-4 text-sm font-bold text-gray-700 uppercase tracking-wider">Stock</th>
              <th class="pb-4 pt-4 text-sm font-bold text-gray-700 uppercase tracking-wider">Status</th>
              <th class="pb-4 pt-4 pr-4 text-sm font-bold text-gray-700 uppercase tracking-wider text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="text-gray-600 divide-y divide-gray-100">
            <tr v-for="product in products" :key="product.id" class="hover:bg-gradient-to-r hover:from-purple-50/50 hover:to-pink-50/50 transition-all">
              <td class="py-4 pl-4">
                <span class="inline-flex items-center justify-center w-8 h-8 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg text-sm font-bold text-gray-700">
                  {{ product.id }}
                </span>
              </td>
              <td class="py-4">
                <div>
                  <p class="font-bold text-gray-900">{{ product.name }}</p>
                  <p class="text-xs text-gray-500 mt-1">SKU: {{ product.sku }}</p>
                </div>
              </td>
              <td class="py-4">
                <span v-if="product.category" class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-sm font-medium">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                  </svg>
                  {{ product.category.name }}
                </span>
                <span v-else class="text-gray-400">-</span>
              </td>
              <td class="py-4">
                <span class="text-lg font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                  ${{ product.price }}
                </span>
              </td>
              <td class="py-4">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-sm font-bold" :class="product.stock > 10 ? 'bg-green-100 text-green-700' : product.stock > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                  </svg>
                  {{ product.stock }}
                </span>
              </td>
              <td class="py-4">
                <span 
                  :class="{
                    'bg-gradient-to-r from-green-500 to-emerald-500 text-white': product.status === 'active',
                    'bg-gradient-to-r from-gray-400 to-gray-500 text-white': product.status === 'inactive',
                    'bg-gradient-to-r from-yellow-400 to-orange-500 text-white': product.status === 'draft'
                  }"
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold shadow-md"
                >
                  {{ product.status }}
                </span>
              </td>
              <td class="py-4 pr-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button 
                    @click="openEditModal(product)"
                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all hover:shadow-md"
                    title="Edit"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>
                  </button>
                  <button 
                    @click="deleteProduct(product.id)"
                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all hover:shadow-md"
                    title="Delete"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="products.length === 0">
                <td colspan="7" class="py-16 text-center">
                  <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                  </div>
                  <p class="text-gray-500 font-semibold">No products found</p>
                </td>
            </tr>
          </tbody>
        </table>

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

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto p-4">
      <div class="bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl w-full max-w-3xl my-8 border border-white/20">
        <div class="p-6 border-b-2 border-gradient-to-r from-purple-100 to-pink-100">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </div>
            <h3 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
              {{ isEditing ? 'Edit Product' : 'Add New Product' }}
            </h3>
          </div>
        </div>
        
        <form @submit.prevent="submitForm" class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                Product Name
              </label>
              <input 
                v-model="form.name" 
                type="text" 
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                placeholder="Enter product name"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                </svg>
                SKU
              </label>
              <input 
                v-model="form.sku" 
                type="text" 
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                placeholder="Enter SKU"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                Category
              </label>
              <select 
                v-model="form.category_id" 
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                required
              >
                <option :value="null" disabled>Select Category</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Price
              </label>
              <input 
                v-model="form.price" 
                type="number" 
                step="0.01"
                min="0"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                placeholder="0.00"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Stock
              </label>
              <input 
                v-model="form.stock" 
                type="number" 
                min="0"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                placeholder="0"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Status
              </label>
              <select 
                v-model="form.status" 
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="draft">Draft</option>
              </select>
            </div>
          </div>

          <div class="mt-5">
            <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
              <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
              </svg>
              Description
            </label>
            <textarea 
              v-model="form.description" 
              rows="3"
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
              placeholder="Enter product description"
            ></textarea>
          </div>

          <div class="flex justify-end gap-3 mt-6 pt-6 border-t-2 border-gray-100">
            <button 
              type="button" 
              @click="closeModal"
              class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all font-semibold"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105"
              :disabled="submitting"
            >
              <svg v-if="!submitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              <span v-if="submitting" class="animate-spin mr-2">⏳</span>
              {{ submitting ? 'Saving...' : 'Save Product' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import productService from '../../services/product.service'
import categoryService from '../../services/category.service'

const products = ref([])
const categories = ref([])
const loading = ref(false)
const error = ref(null)
const showModal = ref(false)
const isEditing = ref(false)
const submitting = ref(false)
const page = ref(1)
const perPage = ref(10)
const meta = ref({ current_page: 1, last_page: 1, total: 0 })

const form = ref({
  id: null,
  name: '',
  sku: '',
  category_id: null,
  price: 0,
  stock: 0,
  status: 'active',
  description: ''
})

const fetchProducts = async () => {
  loading.value = true
  try {
    const response = await productService.getAllProducts({ page: page.value, per_page: perPage.value })
    const payload = response.data || {}
    products.value = payload.data || []
    meta.value = {
      current_page: payload.meta?.current_page || 1,
      last_page: payload.meta?.last_page || 1,
      total: payload.meta?.total || (payload.data ? payload.data.length : 0)
    }
  } catch (err) {
    error.value = 'Failed to load products'
    console.error(err)
  } finally {
    loading.value = false
  }
}

const goToPage = (p) => {
  if (p < 1 || p > meta.value.last_page) return
  page.value = p
  fetchProducts()
}

const fetchCategories = async () => {
  try {
    const response = await categoryService.getAllCategories()
    categories.value = response.data.data
  } catch (err) {
    console.error('Failed to load categories', err)
  }
}

const openCreateModal = () => {
  isEditing.value = false
  form.value = { 
    id: null, 
    name: '', 
    sku: '', 
    category_id: null, 
    price: 0, 
    stock: 0, 
    status: 'active', 
    description: '' 
  }
  showModal.value = true
}

const openEditModal = (product) => {
  isEditing.value = true
  
  // Resolve category_id from product object
  let catId = product.category_id
  if (!catId && product.category) {
    catId = product.category.id
  }

  // Ensure type consistency with categories list
  if (categories.value.length > 0 && catId) {
    const matchingCategory = categories.value.find(c => c.id == catId)
    if (matchingCategory) {
      catId = matchingCategory.id
    }
  }

  form.value = { 
    id: product.id, 
    name: product.name, 
    sku: product.sku,
    category_id: catId,
    price: product.price,
    stock: product.stock,
    status: product.status,
    description: product.description
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const submitForm = async () => {
  submitting.value = true
  try {
    if (isEditing.value) {
      await productService.updateProduct(form.value.id, form.value)
    } else {
      await productService.createProduct(form.value)
    }
    await fetchProducts()
    closeModal()
  } catch (err) {
    console.error(err)
    alert('Failed to save product')
  } finally {
    submitting.value = false
  }
}

const deleteProduct = async (id) => {
  if (!confirm('Are you sure you want to delete this product?')) return
  
  try {
    await productService.deleteProduct(id)
    await fetchProducts()
  } catch (err) {
    console.error(err)
    alert('Failed to delete product')
  }
}

onMounted(() => {
  fetchProducts()
  fetchCategories()
})
</script>
