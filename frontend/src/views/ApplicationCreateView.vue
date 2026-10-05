<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useApplicationStore } from '@/stores/applications'

import axios from 'axios'
import api from '@/services/api'
import AppNav from '@/components/AppNav.vue'
import ApplicationForm from '@/components/ApplicationForm.vue'

import type {
  ApplicationFormData,
  StatusOption,
} from '@/types/application'

const router = useRouter()
const applicationStore = useApplicationStore()

const submitting = ref(false)
const error = ref('')

const statusOptions = ref<StatusOption[]>([])

async function fetchStatuses() {
  const response = await api.get('/application-statuses')

  statusOptions.value = response.data
}

onMounted(() => {
  fetchStatuses()
})

// Laravel validation errors for individual form fields
const validationErrors = ref<Record<string, string[]>>({})

// Reactive object holding the values entered into the form
const form = reactive<ApplicationFormData>({
  company_name: '',
  position: '',
  location: '',
  employment_type: '',
  work_mode: '',
  salary_min: '',
  salary_max: '',
  currency: 'MYR',
  source: '',
  job_url: '',
  status: 'interested',
  applied_at: '',
  next_action: '',
  follow_up_at: '',
  notes: '',
})

async function handleSubmit() {
  submitting.value = true
  error.value = ''
  validationErrors.value = {}

  try {
    await applicationStore.createApplication({
      company_name: form.company_name,
      position: form.position,
      location: form.location,
      employment_type: form.employment_type,
      work_mode: form.work_mode,
      salary_min: form.salary_min,
      salary_max: form.salary_max,
      currency: form.currency,
      source: form.source,
      job_url: form.job_url,
      status: form.status,
      applied_at: form.applied_at,
      next_action: form.next_action,
      follow_up_at: form.follow_up_at,
      notes: form.notes,
    })

    router.push('/applications')
  } catch (err) {
    // Laravel validation failure
    if (axios.isAxiosError(err) && err.response?.status === 422) {
      validationErrors.value = err.response.data.errors
      return
    }

    // Unexpected/non-validation failure
    error.value = 'Unable to create application.'
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <AppNav />

  <main class="page-container">
    <header class="page-header">
      <h1 class="page-title">Add Application</h1>

      <p class="page-description">
        Add a new job application to your tracker.
      </p>
    </header>

    <p v-if="error" class="page-error">
      {{ error }}
    </p>

    <ApplicationForm
      :form="form"
      :status-options="statusOptions"
      :validation-errors="validationErrors"
      :submitting="submitting"
      submit-label="Save Application"
      @submit="handleSubmit"
    />
  </main>
</template>
