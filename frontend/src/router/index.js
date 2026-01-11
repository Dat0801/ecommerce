import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import Home from '../pages/Home.vue'
import Login from '../pages/Login.vue'
import Register from '../pages/Register.vue'
import Cart from '../pages/Cart.vue'
import ProductList from '../pages/ProductList.vue'
import ProductDetail from '../pages/ProductDetail.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
  },
  {
    path: '/products',
    name: 'ProductList',
    component: ProductList,
  },
  {
    path: '/products/:id',
    name: 'ProductDetail',
    component: ProductDetail,
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { guest: true }
  },
  {
    path: '/cart',
    name: 'Cart',
    component: Cart,
    meta: { requiresAuth: true }
  },
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: () => import('../pages/admin/Dashboard.vue'),
    meta: { requiresAuth: true, role: 'admin', layout: 'admin' }
  },
  {
    path: '/admin/products',
    name: 'AdminProducts',
    component: () => import('../pages/admin/ProductManager.vue'),
    meta: { requiresAuth: true, role: 'admin', layout: 'admin' }
  },
  {
    path: '/admin/categories',
    name: 'AdminCategories',
    component: () => import('../pages/admin/CategoryManager.vue'),
    meta: { requiresAuth: true, role: 'admin', layout: 'admin' }
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.meta.guest && authStore.isAuthenticated) {
    if (authStore.user?.role === 'admin') {
      next('/admin')
    } else {
      next('/')
    }
  } else {
    // Check role if required
    if (to.meta.role && authStore.user?.role !== to.meta.role) {
       // If user info is not loaded, we might want to fetch it first.
       // For simplicity, assuming user is loaded or we redirect.
       // Ideally we should await authStore.fetchUser() if user is null but token exists.
       if (!authStore.user && authStore.isAuthenticated) {
         authStore.fetchUser().then(() => {
           if (authStore.user?.role !== to.meta.role) {
             next('/')
           } else {
             next()
           }
         }).catch(() => next('/login'))
       } else {
         next('/')
       }
    } else {
      next()
    }
  }
})

export default router
