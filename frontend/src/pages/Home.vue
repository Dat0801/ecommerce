<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50">
    <!-- Hero Section -->
    <div class="relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 opacity-10 animate-gradient"></div>
      <div class="max-w-7xl mx-auto px-4 py-20 sm:py-28 relative">
        <div class="text-center space-y-6 animate-fade-in">
          <h1 class="text-5xl sm:text-6xl md:text-7xl font-extrabold">
            <span class="bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 bg-clip-text text-transparent">
              Welcome to E-Commerce
            </span>
          </h1>
          <p class="text-xl sm:text-2xl text-gray-700 max-w-2xl mx-auto font-medium">
            Trải nghiệm mua sắm hiện đại và tiện lợi
          </p>
          <div class="flex justify-center gap-4 pt-4">
            <router-link 
              to="/register" 
              class="px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-full font-semibold hover:shadow-xl transform hover:scale-105 transition-all duration-300"
            >
              Bắt đầu ngay
            </router-link>
            <router-link 
              to="/login" 
              class="px-8 py-4 bg-white text-purple-600 rounded-full font-semibold hover:shadow-xl transform hover:scale-105 transition-all duration-300 border-2 border-purple-600"
            >
              Đăng nhập
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Features Section -->
    <div class="max-w-7xl mx-auto px-4 py-16">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Login Card -->
        <router-link 
          to="/login" 
          class="group relative p-8 bg-white/100 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden border border-gray-100"
        >
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full -mr-16 -mt-16 opacity-10 group-hover:opacity-20 transition-opacity"></div>
          <div class="relative z-10">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Đăng nhập</h2>
            <p class="text-gray-600">Truy cập tài khoản của bạn</p>
            <div class="mt-4 flex items-center text-blue-600 font-semibold">
              <span>Đăng nhập</span>
              <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </div>
          </div>
        </router-link>
        
        <!-- Register Card -->
        <router-link 
          to="/register" 
          class="group relative p-8 bg-white/100 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden border border-gray-100"
        >
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full -mr-16 -mt-16 opacity-10 group-hover:opacity-20 transition-opacity"></div>
          <div class="relative z-10">
            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Đăng ký</h2>
            <p class="text-gray-600">Tạo tài khoản mới miễn phí</p>
            <div class="mt-4 flex items-center text-purple-600 font-semibold">
              <span>Đăng ký ngay</span>
              <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </div>
          </div>
        </router-link>
        
        <!-- Cart Card -->
        <router-link 
          to="/cart" 
          class="group relative p-8 bg-white/100 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden border border-gray-100"
        >
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-pink-400 to-pink-600 rounded-full -mr-16 -mt-16 opacity-10 group-hover:opacity-20 transition-opacity"></div>
          <div class="relative z-10">
            <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Giỏ hàng</h2>
            <p class="text-gray-600">Xem giỏ hàng của bạn</p>
            <div class="mt-4 flex items-center text-pink-600 font-semibold">
              <span>Xem giỏ hàng</span>
              <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </div>
          </div>
        </router-link>
      </div>

      <!-- API Status -->
      <div class="mt-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-green-400 to-blue-500 rounded-3xl transform rotate-1"></div>
        <div class="relative bg-white/100 p-8 rounded-3xl shadow-xl border border-gray-100">
          <div class="flex items-center justify-center gap-3 mb-4">
            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">API Status - Online</h2>
          </div>
          <p class="text-center text-gray-700">
            Backend API đang hoạt động tại: 
            <code class="inline-block mt-2 px-4 py-2 bg-gray-100 text-gray-800 rounded-lg font-mono text-sm">{{ apiBaseUrl }}</code>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes gradient {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

.animate-fade-in {
  animation: fade-in 1s ease-out;
}

.animate-gradient {
  background-size: 200% 200%;
  animation: gradient 15s ease infinite;
}
</style>
