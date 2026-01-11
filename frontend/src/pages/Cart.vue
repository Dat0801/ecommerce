<template>
  <div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>
      
      <div v-if="cartStore.totalItems === 0" class="bg-white p-8 rounded-lg shadow text-center">
        <p class="text-gray-600 text-lg">Your cart is empty</p>
        <router-link to="/" class="mt-4 inline-block text-blue-600 hover:underline">
          Continue Shopping
        </router-link>
      </div>

      <div v-else class="space-y-6">
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Product</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Quantity</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Price</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="item in cartStore.items" :key="item.id">
                <td class="px-6 py-4">{{ item.name }}</td>
                <td class="px-6 py-4">{{ item.quantity }}</td>
                <td class="px-6 py-4">${{ item.price }}</td>
                <td class="px-6 py-4">
                  <button 
                    @click="cartStore.removeItem(item.id)"
                    class="text-red-600 hover:underline"
                  >
                    Remove
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="flex justify-between items-center">
          <button 
            @click="cartStore.clearCart()"
            class="text-red-600 hover:underline"
          >
            Clear Cart
          </button>
          <button 
            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition"
          >
            Checkout
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCartStore } from '../stores/cart'

const cartStore = useCartStore()
</script>
