<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import { useApplicationStore } from '@/stores/applications'

import api from '@/services/api'
import AppNav from '@/components/AppNav.vue'
import ApplicationStatusBadge from '@/components/ApplicationStatusBadge.vue'

import type {
  StatusOption,
} from '@/types/application'

import {
  formatDate,
  formatSalaryRange,
} from '@/utils/formatters'

const applicationStore = useApplicationStore()

const search = ref('')
const statusFilter = ref('')
const sort = ref('newest')

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
    sort.value,
  )
})

function handleSearch() {
  applicationStore.fetchApplications(
    search.value,
    statusFilter.value,
    sort.value,
  )
}

watch(sort, () => {
  applicationStore.fetchApplications(
    search.value,
    statusFilter.value,
    sort.value,
  )
})

function goToPage(page: number) {
  applicationStore.fetchApplications(
    search.value,
    statusFilter.value,
    sort.value,
    page,
  )
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

    <select
      v-model="sort"
      class="filter-select"
    >
      <option value="newest">Newest</option>
      <option value="oldest">Oldest</option>
      <option value="recently_applied">Recently Applied</option>
      <option value="company_az">Company A–Z</option>
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

    <div v-else>
      <p
        v-if="applicationStore.pagination.total > 0"
        class="results-info"
      >
        Showing
        {{ applicationStore.pagination.from }}
        –
        {{ applicationStore.pagination.to }}
        of
        {{ applicationStore.pagination.total }}
        applications
      </p>

      <div class="application-list">
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
              :to="`/applications/${application.id}`"
              class="btn btn-primary"
            >
              View
            </RouterLink>

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

      <div
        v-if="applicationStore.pagination.last_page > 1"
        class="pagination"
      >
        <button
          type="button"
          class="btn btn-secondary"
          :disabled="applicationStore.pagination.current_page === 1"
          @click="goToPage(applicationStore.pagination.current_page - 1)"
        >
          Previous
        </button>

        <span class="pagination-info">
          Page
          {{ applicationStore.pagination.current_page }}
          of
          {{ applicationStore.pagination.last_page }}
        </span>

        <button
          type="button"
          class="btn btn-secondary"
          :disabled="
            applicationStore.pagination.current_page ===
            applicationStore.pagination.last_page
          "
          @click="goToPage(applicationStore.pagination.current_page + 1)"
        >
          Next
        </button>
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

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  margin-top: 24px;
}

.pagination-info {
  font-size: 14px;
  color: #6b7280;
}

.results-info {
  margin: 0 0 12px;
  font-size: 14px;
  color: #6b7280;
}
</style>
