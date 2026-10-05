<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useApplicationStore } from '@/stores/applications'

import axios from 'axios'
import api from '@/services/api'
import AppNav from '@/components/AppNav.vue'
import ApplicationForm from '@/components/ApplicationForm.vue'
import ApplicationStatusBadge from '@/components/ApplicationStatusBadge.vue'

import type {
  ApplicationFormData,
  ApplicationStatusHistory,
  StatusOption,
} from '@/types/application'

const route = useRoute()
const router = useRouter()

const applicationStore = useApplicationStore()

const submitting = ref(false)
const validationErrors = ref<Record<string, string[]>>({})

const applicationId = route.params.id as string

const loading = ref(true)
const error = ref('')

const statusHistories = ref<ApplicationStatusHistory[]>([])

const statusOptions = ref<StatusOption[]>([])

async function fetchStatuses() {
  const response = await api.get('/application-statuses')

  statusOptions.value = response.data
}

// Same form structure as ApplicationCreateView page
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
  status: '',
  applied_at: '',
  notes: '',
})

async function loadApplication() {
  loading.value = true
  error.value = ''

  try {
    const application = await applicationStore.fetchApplication(applicationId)

    statusHistories.value = application.status_histories ?? []

    // Populate the form with the existing database values
    form.company_name = application.company_name
    form.position = application.position
    form.location = application.location ?? ''
    form.employment_type = application.employment_type ?? ''
    form.work_mode = application.work_mode ?? ''
    form.salary_min = application.salary_min ?? ''
    form.salary_max = application.salary_max ?? ''
    form.currency = application.currency ?? 'MYR'
    form.source = application.source ?? ''
    form.job_url = application.job_url ?? ''
    form.status = application.status
    form.applied_at = application.applied_at ?? ''
    form.notes = application.notes ?? ''
  } catch {
    error.value = 'Unable to load application.'
  } finally {
    loading.value = false
  }
}

async function handleSubmit() {
  submitting.value = true
  error.value = ''
  validationErrors.value = {}

  try {
    await applicationStore.updateApplication(applicationId, {
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
      notes: form.notes,
    })

    router.push('/applications')
  } catch (err) {
    if (axios.isAxiosError(err) && err.response?.status === 422) {
      validationErrors.value = err.response.data.errors
      return
    }

    error.value = 'Unable to update application.'
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  loadApplication()
  fetchStatuses()
})

function formatStatus(status: string) {
  return status.charAt(0).toUpperCase() + status.slice(1)
}

function formatHistoryDate(date: string) {
  return new Intl.DateTimeFormat('en-MY', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(new Date(date))
}
</script>

<template>
  <AppNav />

  <main class="page-container">
    <header class="page-header">
      <h1 class="page-title">Edit Application</h1>

      <p class="page-description">
        Editing application #{{ applicationId }}
      </p>
    </header>

    <div v-if="loading" class="card">
      Loading application...
    </div>

    <p v-else-if="error" class="page-error">
      {{ error }}
    </p>

    <ApplicationForm
      v-else
      :form="form"
      :status-options="statusOptions"
      :validation-errors="validationErrors"
      :submitting="submitting"
      submit-label="Save Changes"
      @submit="handleSubmit"
    />

    <section class="status-history">
      <h2>Status History</h2>

      <div
        v-if="statusHistories.length === 0"
        class="card"
      >
        No status history yet.
      </div>

      <div
        v-else
        class="history-list"
      >
        <div
          v-for="history in statusHistories"
          :key="history.id"
          class="card history-item"
        >
          <div class="history-transition">
            <ApplicationStatusBadge
              v-if="history.from_status"
              :status="history.from_status"
            />

            <span v-if="history.from_status">→</span>

            <ApplicationStatusBadge :status="history.to_status" />
          </div>

          <span class="history-date">
            {{ formatHistoryDate(history.created_at) }}
          </span>
        </div>
      </div>
    </section>
  </main>
</template>

<style scoped>
.status-history {
  margin-top: 32px;
}

.status-history h2 {
  margin-bottom: 16px;
  font-size: 20px;
}

.history-list {
  display: grid;
  gap: 12px;
}

.history-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.history-date {
  font-size: 13px;
  color: #6b7280;
}
</style>
