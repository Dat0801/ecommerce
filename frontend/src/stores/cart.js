import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCartStore = defineStore('cart', () => {
  const items = ref([])

  const totalItems = computed(() => items.value.length)

  const addItem = (item) => {
    const existingItem = items.value.find((i) => i.id === item.id)
    if (existingItem) {
      existingItem.quantity += item.quantity || 1
    } else {
      items.value.push({ ...item, quantity: item.quantity || 1 })
    }
  }

  const removeItem = (itemId) => {
    items.value = items.value.filter((i) => i.id !== itemId)
  }

  const clearCart = () => {
    items.value = []
  }

  return {
    items,
    totalItems,
    addItem,
    removeItem,
    clearCart,
  }
})
