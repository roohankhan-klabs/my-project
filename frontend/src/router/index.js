import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/LoginView.vue'),
    meta: { requiresGuest: true },
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('@/views/DashboardView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/',
    redirect: '/dashboard',
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/views/NotFoundView.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

let sessionChecked = false

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  // On first navigation, try to restore session from server
  if (!sessionChecked) {
    sessionChecked = true
    await auth.fetchUser()
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login' }
  }

  // Admin users must use Nova — block them from the Vue SPA entirely
  if (to.meta.requiresAuth && auth.isAdmin) {
    window.location.href = '/nova'
    return false
  }

  if (to.meta.requiresGuest && auth.isAuthenticated) {
    if (auth.isAdmin) {
      window.location.href = '/nova'
      return false
    }
    return { name: 'dashboard' }
  }
})

export default router
