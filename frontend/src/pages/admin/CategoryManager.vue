<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
      <h3 class="text-lg font-semibold text-gray-800">Categories</h3>
      <button 
        @click="openCreateModal"
        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
      >
        Add Category
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
              <th class="pb-3">Slug</th>
              <th class="pb-3">Parent</th>
              <th class="pb-3 text-right pr-2">Actions</th>
            </tr>
          </thead>
          <tbody class="text-gray-600">
            <tr v-for="category in categories" :key="category.id" class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
              <td class="py-4 pl-2">{{ category.id }}</td>
              <td class="py-4 font-medium text-gray-800">{{ category.name }}</td>
              <td class="py-4">{{ category.slug }}</td>
              <td class="py-4">{{ category.parent_id || '-' }}</td>
              <td class="py-4 text-right pr-2">
                <button 
                  @click="openEditModal(category)"
                  class="text-blue-600 hover:text-blue-800 mr-3"
                >
                  Edit
                </button>
                <button 
                  @click="deleteCategory(category.id)"
                  class="text-red-600 hover:text-red-800"
                >
                  Delete
                </button>
              </td>
            </tr>
            <tr v-if="categories.length === 0">
                <td colspan="5" class="py-8 text-center text-gray-500">No categories found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
        <h3 class="text-xl font-bold mb-4">{{ isEditing ? 'Edit Category' : 'Add Category' }}</h3>
        
        <form @submit.prevent="submitForm">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input 
              v-model="form.name" 
              type="text" 
              class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
              required
            />
          </div>
          
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Parent Category (Optional)</label>
            <select 
              v-model="form.parent_id" 
              class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
            >
              <option :value="null">None</option>
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

          <div class="flex justify-end gap-3">
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
