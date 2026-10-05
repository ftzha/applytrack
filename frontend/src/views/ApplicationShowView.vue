<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'

import { useApplicationStore } from '@/stores/applications'

import AppNav from '@/components/AppNav.vue'
import ApplicationStatusBadge from '@/components/ApplicationStatusBadge.vue'

import type { Application } from '@/types/application'

import {
  formatDate,
  formatDateTime,
  formatSalaryRange,
} from '@/utils/formatters'

const route = useRoute()
const applicationStore = useApplicationStore()

const applicationId = route.params.id as string

const application = ref<Application | null>(null)
const loading = ref(true)
const error = ref('')

async function loadApplication() {
  loading.value = true
  error.value = ''

  try {
    application.value =
      await applicationStore.fetchApplication(applicationId)
  } catch {
    error.value = 'Unable to load application.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadApplication()
})
</script>

<template>
  <AppNav />

  <main class="page-container">
    <header class="page-header">
      <h1 class="page-title">Application Details</h1>

      <p class="page-description">
        View your job application information.
      </p>
    </header>
    <div
      v-if="application"
      class="page-actions"
    >
      <RouterLink
        to="/applications"
        class="btn btn-secondary"
      >
        Back to Applications
      </RouterLink>

      <RouterLink
        :to="`/applications/${application.id}/edit`"
        class="btn btn-primary"
      >
        Edit Application
      </RouterLink>
    </div>
    <div
      v-if="loading"
      class="card"
    >
      Loading application...
    </div>

    <div
      v-else-if="error"
      class="card"
    >
      {{ error }}
    </div>

    <div
      v-else-if="application"
      class="application-details"
    >
      <section class="card application-summary">
        <div class="summary-header">
          <div>
            <h2>{{ application.position }}</h2>
            <p class="company-name">
              {{ application.company_name }}
            </p>
          </div>

          <ApplicationStatusBadge :status="application.status" />
        </div>
      </section>
      <section class="card">
        <h3>Job Information</h3>

        <div class="details-grid">
          <div class="detail-item">
            <span class="detail-label">Location</span>
            <span>{{ application.location || 'Not specified' }}</span>
          </div>

          <div class="detail-item">
            <span class="detail-label">Employment Type</span>
            <span>{{ application.employment_type || 'Not specified' }}</span>
          </div>

          <div class="detail-item">
            <span class="detail-label">Work Mode</span>
            <span>{{ application.work_mode || 'Not specified' }}</span>
          </div>

          <div class="detail-item">
            <span class="detail-label">Salary Offered</span>
            <span>
              {{
                formatSalaryRange(
                  application.salary_min,
                  application.salary_max,
                  application.currency,
                )
              }}
            </span>
          </div>

          <div class="detail-item">
            <span class="detail-label">Source</span>
            <span>{{ application.source || 'Not specified' }}</span>
          </div>

          <div class="detail-item">
            <span class="detail-label">Date Applied</span>
            <span>{{ formatDate(application.applied_at) }}</span>
          </div>

          <div
            v-if="application.job_url"
            class="detail-item"
          >
            <span class="detail-label">Job Posting</span>

            <a
              :href="application.job_url"
              target="_blank"
              rel="noopener noreferrer"
            >
              Open original job posting
            </a>
          </div>
        </div>

        <div class="details-notes">
          <span class="detail-label">Notes</span>

          <p class="notes">
            {{ application.notes || 'No notes added.' }}
          </p>
        </div>
      </section>
      <section class="card">
        <h3>Status History</h3>

        <p
          v-if="!application.status_histories?.length"
          class="empty-text"
        >
          No status history yet.
        </p>

        <div
          v-else
          class="history-list"
        >
          <div
            v-for="history in application.status_histories"
            :key="history.id"
            class="history-item"
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
              {{ formatDateTime(history.created_at) }}
            </span>
          </div>
        </div>
      </section>
    </div>
  </main>
</template>

<style scoped>
.application-details {
  display: grid;
  gap: 16px;
}

.summary-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
}

.summary-header h2 {
  margin: 0;
}

.company-name {
  margin: 6px 0 0;
}

.card h3 {
  margin-top: 0;
}

.details-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 20px;
}

.details-notes {
  margin-top: 24px;
  padding-top: 20px;
  border-top: 1px solid #e5e7eb;
}

.detail-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.detail-label {
  font-size: 13px;
  color: #6b7280;
}

.notes {
  margin-bottom: 0;
  white-space: pre-wrap;
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

.history-transition {
  display: flex;
  align-items: center;
  gap: 8px;
}

.history-date {
  font-size: 13px;
  color: #6b7280;
}

.empty-text {
  margin-bottom: 0;
}

@media (max-width: 640px) {
  .details-grid {
    grid-template-columns: 1fr;
  }

  .summary-header,
  .history-item {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
