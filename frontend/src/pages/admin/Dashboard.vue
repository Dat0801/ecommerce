<template>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Total Sales Card -->
    <div class="bg-white/90 backdrop-blur-lg p-6 rounded-2xl shadow-xl border border-white/20 hover:shadow-2xl transition-all group">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-gray-600 text-sm font-bold uppercase tracking-wide">Total Sales</h3>
        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
      </div>
      <p class="text-4xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">$12,345</p>
      <div class="mt-4 flex items-center gap-2">
        <div class="flex items-center gap-1 px-2 py-1 bg-green-100 rounded-lg">
          <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
          </svg>
          <span class="text-green-600 text-sm font-bold">+12%</span>
        </div>
        <span class="text-gray-500 text-sm">from last month</span>
      </div>
    </div>

    <!-- Active Users Card -->
    <div class="bg-white/90 backdrop-blur-lg p-6 rounded-2xl shadow-xl border border-white/20 hover:shadow-2xl transition-all group">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-gray-600 text-sm font-bold uppercase tracking-wide">Active Users</h3>
        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
        </div>
      </div>
      <p class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">1,234</p>
      <div class="mt-4 flex items-center gap-2">
        <div class="flex items-center gap-1 px-2 py-1 bg-blue-100 rounded-lg">
          <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
          </svg>
          <span class="text-blue-600 text-sm font-bold">+5%</span>
        </div>
        <span class="text-gray-500 text-sm">from last month</span>
      </div>
    </div>

    <!-- Total Products Card -->
    <div class="bg-white/90 backdrop-blur-lg p-6 rounded-2xl shadow-xl border border-white/20 hover:shadow-2xl transition-all group">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-gray-600 text-sm font-bold uppercase tracking-wide">Total Products</h3>
        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
          </svg>
        </div>
      </div>
      <p class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">156</p>
      <div class="mt-4 flex items-center gap-2">
        <div class="flex items-center gap-1 px-2 py-1 bg-purple-100 rounded-lg">
          <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
          <span class="text-purple-600 text-sm font-bold">In Stock</span>
        </div>
      </div>
    </div>
  </div>
</template>
