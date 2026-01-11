<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
      <h3 class="text-lg font-semibold text-gray-800">Products</h3>
      <button 
        @click="openCreateModal"
        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
      >
        Add Product
      </button>
    </div>
    <div class="p-6">
      <div v-if="loading" class="text-center py-8 text-gray-500">Loading...</div>
      <div v-else-if="error" class="text-center py-8 text-red-500">{{ error }}</div>
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left">
          <thead>
            <tr class="border-b border-gray-100 text-gray-500 text-sm">
              <th class="pb-3 pl-2">ID</th>
              <th class="pb-3">Name</th>
              <th class="pb-3">SKU</th>
              <th class="pb-3">Category</th>
              <th class="pb-3">Price</th>
              <th class="pb-3">Stock</th>
              <th class="pb-3">Status</th>
              <th class="pb-3 text-right pr-2">Actions</th>
            </tr>
          </thead>
          <tbody class="text-gray-600">
            <tr v-for="product in products" :key="product.id" class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
              <td class="py-4 pl-2">{{ product.id }}</td>
              <td class="py-4 font-medium text-gray-800">{{ product.name }}</td>
              <td class="py-4">{{ product.sku }}</td>
              <td class="py-4">{{ product.category ? product.category.name : '-' }}</td>
              <td class="py-4">${{ product.price }}</td>
              <td class="py-4">{{ product.stock }}</td>
              <td class="py-4">
                <span 
                  :class="{
                    'bg-green-100 text-green-800': product.status === 'active',
                    'bg-gray-100 text-gray-800': product.status === 'inactive',
                    'bg-yellow-100 text-yellow-800': product.status === 'draft'
                  }"
                  class="px-2 py-1 rounded-full text-xs font-medium"
                >
                  {{ product.status }}
                </span>
              </td>
              <td class="py-4 text-right pr-2">
                <button 
                  @click="openEditModal(product)"
                  class="text-blue-600 hover:text-blue-800 mr-3"
                  title="Edit"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                  </svg>
                </button>
                <button 
                  @click="deleteProduct(product.id)"
                  class="text-red-600 hover:text-red-800"
                  title="Delete"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                  </svg>
                </button>
              </td>
            </tr>
            <tr v-if="products.length === 0">
                <td colspan="8" class="py-8 text-center text-gray-500">No products found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
      <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl p-6 my-8">
        <h3 class="text-xl font-bold mb-4">{{ isEditing ? 'Edit Product' : 'Add Product' }}</h3>
        
        <form @submit.prevent="submitForm">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
              <input 
                v-model="form.name" 
                type="text" 
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                required
              />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
              <input 
                v-model="form.sku" 
                type="text" 
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                required
              />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
              <select 
                v-model="form.category_id" 
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                required
              >
                <option :value="null" disabled>Select Category</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
              </select>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
              <input 
                v-model="form.price" 
                type="number" 
                step="0.01"
                min="0"
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                required
              />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
              <input 
                v-model="form.stock" 
                type="number" 
                min="0"
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                required
              />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select 
                v-model="form.status" 
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="draft">Draft</option>
              </select>
            </div>
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea 
              v-model="form.description" 
              rows="3"
              class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
            ></textarea>
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <button 
              type="button" 
              @click="closeModal"
              class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
              :disabled="submitting"
            >
              {{ submitting ? 'Saving...' : 'Save' }}
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
    const response = await productService.getAllProducts()
    products.value = response.data.data
  } catch (err) {
    error.value = 'Failed to load products'
    console.error(err)
  } finally {
    loading.value = false
  }
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
