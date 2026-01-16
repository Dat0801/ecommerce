<template>
  <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20">
    <div class="p-6 border-b-2 border-gradient-to-r from-indigo-100 to-purple-100 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
          </svg>
        </div>
        <div>
          <h3 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Categories</h3>
          <p class="text-sm text-gray-600">Manage product categories</p>
        </div>
      </div>
      <button 
        @click="openCreateModal"
        class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add Category
      </button>
    </div>
    <div class="p-6">
      <div v-if="loading" class="flex flex-col items-center justify-center py-16">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-indigo-600 mb-4"></div>
        <p class="text-gray-600 font-medium">Loading categories...</p>
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
            <tr class="bg-gradient-to-r from-indigo-50 to-purple-50 border-b-2 border-indigo-200">
              <th class="pb-4 pt-4 pl-4 text-sm font-bold text-gray-700 uppercase tracking-wider">ID</th>
              <th class="pb-4 pt-4 text-sm font-bold text-gray-700 uppercase tracking-wider">Name</th>
              <th class="pb-4 pt-4 text-sm font-bold text-gray-700 uppercase tracking-wider">Slug</th>
              <th class="pb-4 pt-4 text-sm font-bold text-gray-700 uppercase tracking-wider">Parent</th>
              <th class="pb-4 pt-4 pr-4 text-sm font-bold text-gray-700 uppercase tracking-wider text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="text-gray-600 divide-y divide-gray-100">
            <tr v-for="category in categories" :key="category.id" class="hover:bg-gradient-to-r hover:from-indigo-50/50 hover:to-purple-50/50 transition-all">
              <td class="py-4 pl-4">
                <span class="inline-flex items-center justify-center w-8 h-8 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg text-sm font-bold text-gray-700">
                  {{ category.id }}
                </span>
              </td>
              <td class="py-4">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                  </div>
                  <span class="font-bold text-gray-900">{{ category.name }}</span>
                </div>
              </td>
              <td class="py-4">
                <span class="text-sm font-mono text-gray-600 bg-gray-100 px-3 py-1 rounded-lg">{{ category.slug }}</span>
              </td>
              <td class="py-4">
                <span v-if="category.parent_id" class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                  </svg>
                  {{ category.parent_id }}
                </span>
                <span v-else class="text-gray-400 font-medium">-</span>
              </td>
              <td class="py-4 pr-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button 
                    @click="openEditModal(category)"
                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all hover:shadow-md"
                    title="Edit"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <button 
                    @click="deleteCategory(category.id)"
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
            <tr v-if="categories.length === 0">
                <td colspan="5" class="py-16 text-center">
                  <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                  </div>
                  <p class="text-gray-500 font-semibold">No categories found</p>
                </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl w-full max-w-md border border-white/20">
        <div class="p-6 border-b-2 border-gradient-to-r from-indigo-100 to-purple-100">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
              </svg>
            </div>
            <h3 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
              {{ isEditing ? 'Edit Category' : 'Add New Category' }}
            </h3>
          </div>
        </div>
        
        <form @submit.prevent="submitForm" class="p-6">
          <div class="mb-5">
            <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
              <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
              </svg>
              Category Name
            </label>
            <input 
              v-model="form.name" 
              type="text" 
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
              placeholder="Enter category name"
              required
            />
          </div>
          
          <div class="mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
              <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
              </svg>
              Parent Category
              <span class="text-xs font-normal text-gray-500">(Optional)</span>
            </label>
            <select 
              v-model="form.parent_id" 
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
            >
              <option :value="null">None - Root Category</option>
              <option 
                v-for="cat in categories" 
                :key="cat.id" 
                :value="cat.id"
                :disabled="cat.id === form.id"
              >
                {{ cat.name }}
              </option>
            </select>
          </div>

          <div class="flex justify-end gap-3 pt-6 border-t-2 border-gray-100">
            <button 
              type="button" 
              @click="closeModal"
              class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all font-semibold"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105"
              :disabled="submitting"
            >
              <svg v-if="!submitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              <span v-if="submitting" class="animate-spin mr-2">⏳</span>
              {{ submitting ? 'Saving...' : 'Save Category' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import categoryService from '../../services/category.service'

const categories = ref([])
const loading = ref(false)
const error = ref(null)
const showModal = ref(false)
const isEditing = ref(false)
const submitting = ref(false)

const form = ref({
  id: null,
  name: '',
  parent_id: null
})

const fetchCategories = async () => {
  loading.value = true
  try {
    const response = await categoryService.getAllCategories()
    categories.value = response.data.data
  } catch (err) {
    error.value = 'Failed to load categories'
    console.error(err)
  } finally {
    loading.value = false
  }
}

const openCreateModal = () => {
  isEditing.value = false
  form.value = { id: null, name: '', parent_id: null }
  showModal.value = true
}

const openEditModal = (category) => {
  isEditing.value = true
  form.value = { 
    id: category.id, 
    name: category.name, 
    parent_id: category.parent_id 
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
      await categoryService.updateCategory(form.value.id, {
        name: form.value.name,
        parent_id: form.value.parent_id
      })
    } else {
      await categoryService.createCategory({
        name: form.value.name,
        parent_id: form.value.parent_id
      })
    }
    await fetchCategories()
    closeModal()
  } catch (err) {
    console.error(err)
    alert('Failed to save category')
  } finally {
    submitting.value = false
  }
}

const deleteCategory = async (id) => {
  if (!confirm('Are you sure you want to delete this category?')) return
  
  try {
    await categoryService.deleteCategory(id)
    await fetchCategories()
  } catch (err) {
    console.error(err)
    alert('Failed to delete category')
  }
}

onMounted(() => {
  fetchCategories()
})
</script>
