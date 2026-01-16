import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import Home from '../pages/Home.vue'
import Login from '../pages/Login.vue'
import Register from '../pages/Register.vue'
import Cart from '../pages/Cart.vue'
import ProductList from '../pages/ProductList.vue'
import ProductDetail from '../pages/ProductDetail.vue'
import Checkout from '../pages/Checkout.vue'
import Orders from '../pages/Orders.vue'
import OrderDetail from '../pages/OrderDetail.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: { layout: 'default', title: 'Home - E-Commerce' }
  },
  {
    path: '/products',
    name: 'ProductList',
    component: ProductList,
    meta: { layout: 'default', title: 'Products - E-Commerce' }
  },
  {
    path: '/products/:id',
    name: 'ProductDetail',
    component: ProductDetail,
    meta: { layout: 'default', title: 'Product Details - E-Commerce' }
  },
  {
    path: '/checkout',
    name: 'Checkout',
    component: Checkout,
    meta: { layout: 'default', title: 'Checkout - E-Commerce' }
  },
  {
    path: '/orders',
    name: 'Orders',
    component: Orders,
    meta: { requiresAuth: true, layout: 'default', title: 'My Orders - E-Commerce' }
  },
  {
    path: '/orders/:id',
    name: 'OrderDetail',
    component: OrderDetail,
    meta: { requiresAuth: true, layout: 'default', title: 'Order Details - E-Commerce' }
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guest: true, layout: 'default', title: 'Login - E-Commerce' }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { guest: true, layout: 'default', title: 'Register - E-Commerce' }
  },
  {
    path: '/cart',
    name: 'Cart',
    component: Cart,
    meta: { layout: 'default', title: 'Shopping Cart - E-Commerce' }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('../pages/Profile.vue'),
    meta: { requiresAuth: true, layout: 'default', title: 'My Profile - E-Commerce' }
  },
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: () => import('../pages/admin/Dashboard.vue'),
    meta: { requiresAuth: true, role: 'admin', layout: 'admin', title: 'Dashboard - Admin' }
  },
  {
    path: '/admin/products',
    name: 'AdminProducts',
    component: () => import('../pages/admin/ProductManager.vue'),
    meta: { requiresAuth: true, role: 'admin', layout: 'admin', title: 'Manage Products - Admin' }
  },
  {
    path: '/admin/categories',
    name: 'AdminCategories',
    component: () => import('../pages/admin/CategoryManager.vue'),
    meta: { requiresAuth: true, role: 'admin', layout: 'admin', title: 'Manage Categories - Admin' }
  },
  {
    path: '/admin/orders',
    name: 'AdminOrders',
    component: () => import('../pages/admin/Orders.vue'),
    meta: { requiresAuth: true, role: 'admin', layout: 'admin', title: 'Orders - Admin' }
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

router.afterEach((to) => {
  document.title = to.meta.title || 'E-Commerce'
})

export default router
