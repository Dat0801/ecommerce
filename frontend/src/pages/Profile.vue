<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 py-8 sm:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Page Header -->
      <div class="mb-8 bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">My Profile</h1>
            <p class="text-gray-600 text-sm mt-1">Manage your account information</p>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-20">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-indigo-600 mb-4"></div>
        <p class="text-gray-600 font-medium">Loading profile...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-12 text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <p class="text-red-600 font-semibold">{{ error }}</p>
      </div>

      <!-- Profile Content -->
      <div v-else class="space-y-6">
        <!-- Profile Card -->
        <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 overflow-hidden">
          <!-- Header with Avatar -->
          <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-center">
            <div class="w-24 h-24 mx-auto bg-white rounded-full flex items-center justify-center text-indigo-600 font-bold text-4xl shadow-2xl">
              {{ user?.name?.charAt(0).toUpperCase() || 'U' }}
            </div>
            <h2 class="mt-4 text-2xl font-bold text-white">{{ user?.name || 'User' }}</h2>
            <p class="text-indigo-100 mt-1">{{ user?.email || '' }}</p>
            <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white font-semibold">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
              {{ user?.role === 'admin' ? 'Administrator' : 'Customer' }}
            </div>
          </div>

          <!-- Profile Information -->
          <div class="p-6 sm:p-8 space-y-6">
            <div>
              <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Account Information
              </h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-4 rounded-xl border-2 border-indigo-100">
                  <label class="block text-sm font-bold text-gray-700 mb-2">Full Name</label>
                  <input 
                    v-model="form.name"
                    type="text"
                    :disabled="!isEditing"
                    class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all disabled:bg-gray-100 disabled:cursor-not-allowed"
                  />
                </div>

                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-4 rounded-xl border-2 border-indigo-100">
                  <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                  <input 
                    v-model="form.email"
                    type="email"
                    disabled
                    class="w-full px-4 py-3 bg-gray-100 border-2 border-gray-200 rounded-xl cursor-not-allowed"
                  />
                  <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
                </div>

                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-4 rounded-xl border-2 border-indigo-100">
                  <label class="block text-sm font-bold text-gray-700 mb-2">Account Role</label>
                  <div class="flex items-center gap-2 px-4 py-3 bg-white border-2 border-gray-200 rounded-xl">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span class="font-semibold text-gray-700">{{ user?.role === 'admin' ? 'Administrator' : 'Customer' }}</span>
                  </div>
                </div>

                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-4 rounded-xl border-2 border-indigo-100">
                  <label class="block text-sm font-bold text-gray-700 mb-2">Member Since</label>
                  <div class="flex items-center gap-2 px-4 py-3 bg-white border-2 border-gray-200 rounded-xl">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-semibold text-gray-700">{{ formatDate(user?.created_at) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t-2 border-gray-100">
              <button 
                v-if="!isEditing"
                @click="startEditing"
                class="flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Profile
              </button>
              
              <template v-else>
                <button 
                  @click="saveProfile"
                  :disabled="saving"
                  class="flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="!saving" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <span v-if="saving" class="animate-spin">⏳</span>
                  {{ saving ? 'Saving...' : 'Save Changes' }}
                </button>
                
                <button 
                  @click="cancelEditing"
                  :disabled="saving"
                  class="flex items-center justify-center gap-2 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all font-bold disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                  Cancel
                </button>
              </template>
            </div>
          </div>
        </div>

        <!-- Additional Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Security Card -->
          <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-bold text-gray-900">Security</h3>
            </div>
            <p class="text-gray-600 mb-4">Keep your account secure</p>
            <button class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:shadow-lg transition-all font-semibold">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
              </svg>
              Change Password
            </button>
          </div>

          <!-- Orders Card -->
          <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-bold text-gray-900">My Orders</h3>
            </div>
            <p class="text-gray-600 mb-4">View your order history</p>
            <button class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:shadow-lg transition-all font-semibold">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
              View Orders
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const user = ref(null)
const loading = ref(true)
const error = ref(null)
const isEditing = ref(false)
const saving = ref(false)

const form = ref({
  name: '',
  email: ''
})

const fetchProfile = async () => {
  loading.value = true
  error.value = null
  try {
    await authStore.fetchUser()
    user.value = authStore.user
    form.value = {
      name: user.value?.name || '',
      email: user.value?.email || ''
    }
  } catch (err) {
    error.value = 'Failed to load profile'
    console.error(err)
  } finally {
    loading.value = false
  }
}

const startEditing = () => {
  isEditing.value = true
  form.value = {
    name: user.value?.name || '',
    email: user.value?.email || ''
  }
}

const cancelEditing = () => {
  isEditing.value = false
  form.value = {
    name: user.value?.name || '',
    email: user.value?.email || ''
  }
}

const saveProfile = async () => {
  saving.value = true
  try {
    // TODO: Implement API call to update profile
    // await authStore.updateProfile(form.value)
    
    // For now, just update local state
    user.value = { ...user.value, name: form.value.name }
    isEditing.value = false
    
    // Show success message (you can implement a toast notification)
    alert('Profile updated successfully!')
  } catch (err) {
    console.error('Failed to update profile:', err)
    alert('Failed to update profile')
  } finally {
    saving.value = false
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  })
}

onMounted(() => {
  fetchProfile()
})
</script>
