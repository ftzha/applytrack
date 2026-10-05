import { ref } from 'vue'
import { defineStore } from 'pinia'

import api from '@/services/api'

import type {
  Application,
  ApplicationFormData,
} from '@/types/application'

export const useApplicationStore = defineStore('applications', () => {
  // Applications belonging to the authenticated user
  const applications = ref<Application[]>([])

  // Useful for showing loading indicators in the UI
  const loading = ref(false)

  // Holds an error message if fetching fails
  const error = ref<string | null>(null)

  // Returns all applications for the user
  async function fetchApplications(
    search = '',
    status = '',
  ) {
    loading.value = true
    error.value = ''

    try {
      const response = await api.get('/applications', {
        params: {
          search: search || undefined,
          status: status || undefined,
        },
      })

      applications.value = response.data.data
    } catch {
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
    loading,
    error,
    fetchApplications,
    fetchApplication,
    createApplication,
    updateApplication,
    deleteApplication,
  }
})
