<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import { useApplicationStore } from '@/stores/applications'

import api from '@/services/api'
import AppNav from '@/components/AppNav.vue'
import ApplicationStatusBadge from '@/components/ApplicationStatusBadge.vue'

import type {
  StatusOption,
} from '@/types/application'

const applicationStore = useApplicationStore()

const search = ref('')
const statusFilter = ref('')

const deletingId = ref<number | null>(null)

// Status options provided by Laravel's ApplicationStatus enum
const statusOptions = ref<StatusOption[]>([])

async function fetchStatuses() {
  const response = await api.get('/application-statuses')

  statusOptions.value = response.data
}

// Fetch applications and statuses when this page is first loaded
onMounted(() => {
  applicationStore.fetchApplications()
  fetchStatuses()
})

async function handleDelete(id: number) {
  const confirmed = window.confirm(
    'Are you sure you want to delete this application?',
  )

  if (!confirmed) {
    return
  }

  deletingId.value = id

  try {
    await applicationStore.deleteApplication(id)
  } catch {
    window.alert('Unable to delete application.')
  } finally {
    deletingId.value = null
  }
}

watch(statusFilter, () => {
  applicationStore.fetchApplications(
    search.value,
    statusFilter.value,
  )
})

function handleSearch() {
  applicationStore.fetchApplications(
    search.value,
    statusFilter.value,
  )
}

// Display salary as Malaysian-style currency formatting
function formatSalary(
  amount: string | null,
  currency: string | null,
) {
  if (!amount) return null

  return new Intl.NumberFormat('en-MY', {
    style: 'currency',
    currency: currency ?? 'MYR',
    maximumFractionDigits: 0,
  }).format(Number(amount))
}

function formatSalaryRange(
  min: string | null,
  max: string | null,
  currency: string | null,
) {
  if (min && max) {
    return `${formatSalary(min, currency)} ~ ${formatSalary(max, currency)}`
  }

  if (min) {
    return `From ${formatSalary(min, currency)}`
  }

  if (max) {
    return `Up to ${formatSalary(max, currency)}`
  }

  return 'Not specified'
}

// Convert API dates into a friendlier display format
function formatDate(date: string | null) {
  if (!date) return 'No Information'

  return new Intl.DateTimeFormat('en-MY', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  }).format(new Date(date))
}
</script>

<template>
  <AppNav />

  <main class="page-container">
  <header class="page-header applications-header">
    <div>
      <h1 class="page-title">Applications</h1>

      <p class="page-description">
        Track and manage your job applications.
      </p>
    </div>

    <RouterLink
      to="/applications/create"
      class="btn btn-primary"
    >
      + Add Application
    </RouterLink>
  </header>

  <div class="application-filters">
    <form
      class="search-form"
      @submit.prevent="handleSearch"
    >
      <input
        v-model="search"
        type="search"
        placeholder="Search company or position..."
        class="filter-input"
      />

      <button
        type="submit"
        class="btn btn-primary"
      >
        Search
      </button>
    </form>

    <select
      v-model="statusFilter"
      class="filter-select"
    >
      <option value="">All statuses</option>

      <option
        v-for="status in statusOptions"
        :key="status.value"
        :value="status.value"
      >
        {{ status.label }}
      </option>
    </select>
  </div>

    <p v-if="applicationStore.loading">
      Loading applications...
    </p>

    <p v-else-if="applicationStore.error">
      {{ applicationStore.error }}
    </p>

    <div
      v-else-if="applicationStore.applications.length === 0"
      class="card"
    >
      No applications yet.
    </div>

    <div v-else class="application-list">
      <div
        v-for="application in applicationStore.applications"
        :key="application.id"
        class="card application-card"
      >
        <h2>{{ application.position }}</h2>
        <p>{{ application.company_name }}</p>

        <p v-if="application.location">
          {{ application.location }}
        </p>

        <p>
          Salary Offered:
          {{ formatSalaryRange(
            application.salary_min,
            application.salary_max,
            application.currency
          ) }}
        </p>

        <p>
          Date Applied: {{ formatDate(application.applied_at) }}
        </p>

        <p>
          Status:
          <ApplicationStatusBadge :status="application.status" />
        </p>

        <div class="application-actions">
          <RouterLink
            :to="`/applications/${application.id}/edit`"
            class="btn btn-secondary"
          >
            Edit
          </RouterLink>

          <button
            type="button"
            class="btn btn-danger"
            :disabled="deletingId === application.id"
            @click="handleDelete(application.id)"
          >
            {{ deletingId === application.id ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
.application-filters {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
}

.search-form {
  display: flex;
  flex: 1;
  gap: 8px;
}

.filter-input,
.filter-select {
  min-height: 42px;
  padding: 0 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  background: #ffffff;
}

.filter-input {
  flex: 1;
}

.filter-select {
  min-width: 180px;
}

@media (max-width: 640px) {
  .application-filters {
    align-items: stretch;
    flex-direction: column;
  }

  .filter-select {
    width: 100%;
  }
}

.application-list {
  display: grid;
  gap: 16px;
}

.application-card {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.application-card h2 {
  margin: 0;
}

.application-card p {
  margin: 0;
}

.applications-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
}

@media (max-width: 640px) {
  .applications-header {
    align-items: stretch;
    flex-direction: column;
  }
}

.application-actions {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  margin-top: 8px;
}
</style>
