import { ref } from 'vue'
import { defineStore } from 'pinia'

import api from '@/services/api'

import type { DashboardStats } from '@/types/dashboard'

export const useDashboardStore = defineStore('dashboard', () => {
  // Dashboard statistics returned by Laravel
  const stats = ref<DashboardStats>({
    total: 0,
    interested: 0,
    applied: 0,
    screening: 0,
    interview: 0,
    offer: 0,
    rejected: 0,
    withdrawn: 0,
    recent_applications: [],
    recent_activity: [],
  })

  const loading = ref(false)
  const error = ref('')

  async function fetchDashboard() {
    loading.value = true
    error.value = ''

    try {
      const response = await api.get('/dashboard')

      stats.value = response.data.data
    } catch {
      error.value = 'Unable to load dashboard.'
    } finally {
      loading.value = false
    }
  }

  return {
    stats,
    loading,
    error,
    fetchDashboard,
  }
})
