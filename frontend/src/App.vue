<template>
  <component :is="layout">
    <router-view />
  </component>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import DefaultLayout from './layouts/DefaultLayout.vue'
import AdminLayout from './layouts/AdminLayout.vue'

const route = useRoute()

const layout = computed(() => {
  return route.meta.layout === 'admin' ? AdminLayout : DefaultLayout
})
</script>
