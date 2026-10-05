import { ref } from 'vue'
import { defineStore } from 'pinia'

import api from '@/services/api'

import type {
  Application,
  ApplicationFormData,
  ApplicationPagination,
} from '@/types/application'

export const useApplicationStore = defineStore('applications', () => {
  // Applications belonging to the authenticated user
  const applications = ref<Application[]>([])

  const pagination = ref<ApplicationPagination>({
    current_page: 1,
    last_page: 1,
    per_page: 5,
    total: 0,
    from: null,
    to: null,
  })

  // Useful for showing loading indicators in the UI
  const loading = ref(false)

  // Holds an error message if fetching fails
  const error = ref<string | null>(null)

  // Returns all applications for the user
  async function fetchApplications(
    search = '',
    status = '',
    sort = 'newest',
    page = 1,
  ) {
    loading.value = true
    error.value = ''

    try {
      const response = await api.get('/applications', {
        params: {
          search: search || undefined,
          status: status || undefined,
          sort,
          page,
        },
      })

      // Application records for the current page
      applications.value = response.data.data

      // Pagination information returned by Laravel
      pagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        per_page: response.data.per_page,
        total: response.data.total,
        from: response.data.from,
        to: response.data.to,
      }
    } catch (err) {
      console.error('Failed to fetch applications:', err)

      error.value = 'Unable to load applications.'
    } finally {
      loading.value = false
    }
  }

  // Returns one application record
  async function fetchApplication(id: string) {
    // Fetch one application by its route/database ID
    const response = await api.get(`/applications/${id}`)

    return response.data.data
  }

  async function createApplication(data: ApplicationFormData) {
    const response = await api.post('/applications', data)

    applications.value.unshift(response.data.data)

    return response.data.data
  }

  async function updateApplication(
    id: string,
    data: ApplicationFormData,
  ) {
    const response = await api.patch(`/applications/${id}`, data)

    return response.data.data
  }

  async function deleteApplication(id: number) {
    // Laravel deletes the record from database
    await api.delete(`/applications/${id}`)

    // Remove the deleted application from Pinia's local list
    applications.value = applications.value.filter(
      (application) => application.id !== id,
    )
  }

  return {
    applications,
    pagination,
    loading,
    error,
    fetchApplications,
    fetchApplication,
    createApplication,
    updateApplication,
    deleteApplication,
  }
})
