import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

import LoginView from '@/views/LoginView.vue'
import DashboardView from '@/views/DashboardView.vue'
import ApplicationsView from '@/views/ApplicationsView.vue'
import ApplicationCreateView from '@/views/ApplicationCreateView.vue'
import ApplicationEditView from '@/views/ApplicationEditView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),

  routes: [
    {
      path: '/',
      redirect: '/login',
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardView,
      meta: {
        requiresAuth: true,
      },
    },
    {
      path: '/applications',
      name: 'applications',
      component: ApplicationsView,
      meta: {
        requiresAuth: true,
      },
    },
    {
      path: '/applications/create',
      name: 'application-create',
      component: ApplicationCreateView,
      meta: {
        requiresAuth: true,
      },
    },
    {
      path: '/applications/:id/edit',
      name: 'application-edit',
      component: ApplicationEditView,
      meta: {
        requiresAuth: true,
      },
    },
  ],
})

router.beforeEach(async (to) => {
  const authStore = useAuthStore()

  // Check whether the destination requires authentication
  if (to.meta.requiresAuth) {
    // No saved token; user must log in
    if (!authStore.token) {
      return '/login'
    }

    // Token exists but Pinia lost the user after a browser refresh
    if (!authStore.user) {
      try {
        // Validate the saved token and restore the user from Laravel
        await authStore.fetchUser()
      } catch {
        // Saved token is invalid/expired; remove it
        localStorage.removeItem('token')
        authStore.token = null

        return '/login'
      }
    }
  }
})

export default router
