<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useApplicationStore } from '@/stores/applications'

import AppNav from '@/components/AppNav.vue'
import ApplicationStatusBadge from '@/components/ApplicationStatusBadge.vue'

const applicationStore = useApplicationStore()

const deletingId = ref<number | null>(null)

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

// Fetch applications when this page is first loaded
onMounted(() => {
  applicationStore.fetchApplications()
})

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
