<template>
  <div class="min-h-screen">
    <nav class="bg-white/80 backdrop-blur-lg shadow-lg border-b border-gray-200/50 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 sm:h-20 gap-4">
          <!-- Logo -->
          <div class="flex items-center flex-shrink-0">
            <router-link to="/" class="flex items-center gap-2 sm:gap-3 group">
              <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center transform group-hover:rotate-12 transition-transform duration-300">
                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
              <span class="hidden lg:block text-lg sm:text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                E-Commerce
              </span>
            </router-link>
          </div>

          <!-- Search Bar (Desktop) -->
          <div class="hidden md:flex flex-1 max-w-md">
            <div class="relative w-full">
              <input 
                v-model="searchQuery"
                @keyup.enter="handleSearch"
                type="text" 
                placeholder="Search products..." 
                class="w-full pl-10 pr-4 py-2 border-2 border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
              />
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
          </div>

          <!-- Desktop Navigation -->
          <div class="hidden md:flex items-center space-x-1 lg:space-x-2">
            <router-link 
              to="/" 
              class="px-3 lg:px-4 py-2 rounded-lg text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-all duration-200 font-medium text-sm lg:text-base"
              active-class="bg-purple-100 text-purple-600 font-semibold"
            >
              Home
            </router-link>
            <router-link 
              to="/products" 
              class="px-3 lg:px-4 py-2 rounded-lg text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-all duration-200 font-medium text-sm lg:text-base"
              active-class="bg-purple-100 text-purple-600 font-semibold"
            >
              Products
            </router-link>
            
            <!-- Login Button (if not authenticated) -->
            <router-link 
              v-if="!authStore.isAuthenticated"
              to="/login" 
              class="px-3 lg:px-4 py-2 rounded-lg text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-all duration-200 font-medium text-sm lg:text-base"
              active-class="bg-purple-100 text-purple-600 font-semibold"
            >
              Login
            </router-link>
            
            <!-- User Menu Dropdown (if authenticated) -->
            <div v-else class="relative">
              <button 
                @click="userMenuOpen = !userMenuOpen"
                class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-purple-50 transition-all duration-200"
              >
                <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                  {{ authStore.user?.name?.charAt(0).toUpperCase() || 'U' }}
                </div>
                <span class="hidden lg:block text-sm font-medium text-gray-700">{{ authStore.user?.name || 'User' }}</span>
                <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': userMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
              
              <!-- Dropdown Menu -->
              <div 
                v-if="userMenuOpen"
                class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-200 py-2 z-50"
              >
                <div class="px-4 py-3 border-b border-gray-100">
                  <p class="text-sm font-semibold text-gray-900">{{ authStore.user?.name || 'User' }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ authStore.user?.email || '' }}</p>
                </div>
                
                <router-link 
                  to="/profile"
                  @click="userMenuOpen = false"
                  class="flex items-center gap-3 px-4 py-2.5 hover:bg-purple-50 transition-all text-gray-700 hover:text-purple-600"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                  <span class="font-medium text-sm">My Profile</span>
                </router-link>

                <router-link 
                  to="/orders"
                  @click="userMenuOpen = false"
                  class="flex items-center gap-3 px-4 py-2.5 hover:bg-indigo-50 transition-all text-gray-700 hover:text-indigo-600"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l2 2h5a2 2 0 012 2v5" />
                  </svg>
                  <span class="font-medium text-sm">My Orders</span>
                </router-link>
                
                <router-link 
                  v-if="authStore.user?.role === 'admin'"
                  to="/admin"
                  @click="userMenuOpen = false"
                  class="flex items-center gap-3 px-4 py-2.5 hover:bg-indigo-50 transition-all text-gray-700 hover:text-indigo-600"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                  </svg>
                  <span class="font-medium text-sm">Admin Panel</span>
                </router-link>
                
                <div class="border-t border-gray-100 mt-2 pt-2">
                  <button 
                    @click="handleLogout"
                    class="flex items-center gap-3 px-4 py-2.5 hover:bg-red-50 transition-all text-gray-700 hover:text-red-600 w-full"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="font-medium text-sm">Logout</span>
                  </button>
                </div>
              </div>
            </div>
            
            <router-link 
              to="/cart" 
              class="relative px-3 lg:px-4 py-2 rounded-lg text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-all duration-200 font-medium group text-sm lg:text-base"
              active-class="bg-purple-100 text-purple-600 font-semibold"
            >
              <span class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="hidden lg:inline">Cart</span>
              </span>
              <span v-if="cartStore.totalItems > 0" class="absolute -top-1 -right-1 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold shadow-lg">
                {{ cartStore.totalItems }}
              </span>
            </router-link>
          </div>

          <!-- Mobile Menu Button & Cart -->
          <div class="flex md:hidden items-center space-x-2">
            <router-link 
              to="/cart" 
              class="relative p-2 rounded-lg text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-all duration-200"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
              <span v-if="cartStore.totalItems > 0" class="absolute -top-1 -right-1 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold shadow-lg">
                {{ cartStore.totalItems }}
              </span>
            </router-link>
            <button 
              @click="mobileMenuOpen = !mobileMenuOpen"
              class="p-2 rounded-lg text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-all duration-200"
            >
              <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              </svg>
              <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Menu -->
        <div v-if="mobileMenuOpen" class="md:hidden py-4 space-y-2 border-t border-gray-200">
          <!-- Search Bar (Mobile) -->
          <div class="px-4 mb-3">
            <div class="relative w-full">
              <input 
                v-model="searchQuery"
                @keyup.enter="handleSearch"
                type="text" 
                placeholder="Search products..." 
                class="w-full pl-10 pr-4 py-2 border-2 border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
              />
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
          </div>
          
          <router-link 
            to="/" 
            @click="mobileMenuOpen = false"
            class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-all duration-200 font-medium"
            active-class="bg-purple-100 text-purple-600 font-semibold"
          >
            Home
          </router-link>
          <router-link 
            to="/products" 
            @click="mobileMenuOpen = false"
            class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-all duration-200 font-medium"
            active-class="bg-purple-100 text-purple-600 font-semibold"
          >
            Products
          </router-link>
          
          <!-- Not Authenticated -->
          <router-link 
            v-if="!authStore.isAuthenticated"
            to="/login" 
            @click="mobileMenuOpen = false"
            class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-all duration-200 font-medium"
            active-class="bg-purple-100 text-purple-600 font-semibold"
          >
            Login
          </router-link>
          
          <!-- Authenticated User Menu -->
          <template v-else>
            <div class="px-4 py-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg mx-4">
              <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                  {{ authStore.user?.name?.charAt(0).toUpperCase() || 'U' }}
                </div>
                <div>
                  <p class="text-sm font-semibold text-gray-900">{{ authStore.user?.name || 'User' }}</p>
                  <p class="text-xs text-gray-600">{{ authStore.user?.email || '' }}</p>
                </div>
              </div>
              
              <div class="space-y-1">
                <router-link 
                  to="/profile"
                  @click="mobileMenuOpen = false"
                  class="flex items-center gap-2 px-3 py-2 bg-white rounded-lg hover:bg-purple-100 transition-all text-gray-700"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                  <span class="text-sm font-medium">My Profile</span>
                </router-link>

                <router-link 
                  to="/orders"
                  @click="mobileMenuOpen = false"
                  class="flex items-center gap-2 px-3 py-2 bg-white rounded-lg hover:bg-indigo-100 transition-all text-gray-700"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l2 2h5a2 2 0 012 2v5" />
                  </svg>
                  <span class="text-sm font-medium">My Orders</span>
                </router-link>
                
                <router-link 
                  v-if="authStore.user?.role === 'admin'"
                  to="/admin"
                  @click="mobileMenuOpen = false"
                  class="flex items-center gap-2 px-3 py-2 bg-white rounded-lg hover:bg-indigo-100 transition-all text-gray-700"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                  </svg>
                  <span class="text-sm font-medium">Admin Panel</span>
                </router-link>
                
                <button 
                  @click="handleLogout"
                  class="flex items-center gap-2 px-3 py-2 bg-white rounded-lg hover:bg-red-100 transition-all text-gray-700 w-full"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                  </svg>
                  <span class="text-sm font-medium">Logout</span>
                </button>
              </div>
            </div>
          </template>
        </div>
      </div>
    </nav>

    <main class="min-h-screen">
      <slot />
    </main>

    <footer class="bg-gradient-to-r from-gray-900 via-purple-900 to-gray-900 text-white py-12">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <span class="text-xl font-bold">E-Commerce</span>
          </div>
          <p class="text-gray-400">© 2026 E-Commerce Platform. All rights reserved.</p>
          <div class="flex gap-4">
            <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-all duration-300 hover:scale-110">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </a>
            <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-all duration-300 hover:scale-110">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
              </svg>
            </a>
            <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-all duration-300 hover:scale-110">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useCartStore } from '../stores/cart'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'

const cartStore = useCartStore()
const authStore = useAuthStore()
const { isAuthenticated } = storeToRefs(authStore)
const router = useRouter()
const mobileMenuOpen = ref(false)
const userMenuOpen = ref(false)
const searchQuery = ref('')

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ path: '/products', query: { search: searchQuery.value } })
    searchQuery.value = ''
    mobileMenuOpen.value = false
  }
}

const handleLogout = async () => {
  mobileMenuOpen.value = false
  userMenuOpen.value = false
  await authStore.logout()
  router.push('/login')
}
</script>
