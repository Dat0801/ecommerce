<template>
  <div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-6xl mx-auto px-4">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>
      
      <div v-if="cartStore.loading" class="bg-white p-8 rounded-lg shadow text-center">
        <p class="text-gray-600">Loading cart...</p>
      </div>
      
      <div v-else-if="cartStore.items.length === 0" class="bg-white p-8 rounded-lg shadow text-center">
        <p class="text-gray-600 text-lg">Your cart is empty</p>
        <router-link to="/products" class="mt-4 inline-block text-blue-600 hover:underline">
          Continue Shopping
        </router-link>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
              <thead class="bg-gray-50 border-b">
                <tr>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Product</th>
                  <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">Quantity</th>
                  <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Price</th>
                  <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Subtotal</th>
                  <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="item in cartStore.items" :key="item.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4">
                    <div class="flex items-center space-x-4">
                      <div v-if="item.product?.image" class="w-12 h-12 bg-gray-200 rounded overflow-hidden">
                        <img :src="item.product.image" :alt="item.product.name" class="w-full h-full object-cover">
                      </div>
                      <div>
                        <p class="font-medium text-gray-900">{{ item.product?.name }}</p>
                        <p class="text-sm text-gray-500">{{ item.product?.category?.name }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center space-x-2">
                      <button 
                        @click="decrementQuantity(item)"
                        class="px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded"
                      >
                        -
                      </button>
                      <span class="w-8 text-center">{{ item.quantity }}</span>
                      <button 
                        @click="incrementQuantity(item)"
                        class="px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded"
                      >
                        +
                      </button>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-right text-gray-900">${{ (item.price).toFixed(2) }}</td>
                  <td class="px-6 py-4 text-right font-medium text-gray-900">${{ (item.subtotal).toFixed(2) }}</td>
                  <td class="px-6 py-4 text-center">
                    <button 
                      @click="removeFromCart(item.id)"
                      class="text-red-600 hover:text-red-800 font-medium"
                    >
                      Remove
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-6 flex justify-between items-center">
            <router-link 
              to="/products" 
              class="text-blue-600 hover:underline font-medium"
            >
              ← Continue Shopping
            </router-link>
            <button 
              @click="handleClearCart"
              class="text-red-600 hover:text-red-800 font-medium"
            >
              Clear Cart
            </button>
          </div>
        </div>

        <!-- Cart Summary -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow p-6 sticky top-20">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Order Summary</h2>
            
            <div class="space-y-4 border-b pb-4">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal:</span>
                <span class="text-gray-900">${{ (cartStore.totalPrice).toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Shipping:</span>
                <span class="text-gray-900">$0.00</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Tax:</span>
                <span class="text-gray-900">$0.00</span>
              </div>
            </div>

            <div class="mt-4 flex justify-between text-lg font-bold">
              <span>Total:</span>
              <span class="text-blue-600">${{ (cartStore.totalPrice).toFixed(2) }}</span>
            </div>

            <button 
              @click="goToCheckout"
              class="w-full mt-6 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-medium"
            >
              Proceed to Checkout
            </button>

            <p class="mt-4 text-xs text-gray-500 text-center">
              Free shipping on orders over $50
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useCartStore } from '../stores/cart'
import { useRouter } from 'vue-router'

const cartStore = useCartStore()
const router = useRouter()

onMounted(async () => {
  await cartStore.fetchCart()
})

const incrementQuantity = async (item) => {
  try {
    await cartStore.updateQuantity(item.id, item.quantity + 1)
  } catch (error) {
    console.error('Error updating quantity:', error)
  }
}

const decrementQuantity = async (item) => {
  try {
    if (item.quantity > 1) {
      await cartStore.updateQuantity(item.id, item.quantity - 1)
    } else {
      await removeFromCart(item.id)
    }
  } catch (error) {
    console.error('Error updating quantity:', error)
  }
}

const removeFromCart = async (itemId) => {
  try {
    await cartStore.removeItem(itemId)
  } catch (error) {
    console.error('Error removing item:', error)
  }
}

const handleClearCart = async () => {
  if (confirm('Are you sure you want to clear your cart?')) {
    try {
      await cartStore.clearCart()
    } catch (error) {
      console.error('Error clearing cart:', error)
    }
  }
}

const goToCheckout = () => {
  router.push('/checkout')
}
</script>
