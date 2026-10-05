<script setup lang="ts">
import { onMounted } from 'vue'
import { useDashboardStore } from '@/stores/dashboard'

import AppNav from '@/components/AppNav.vue'
import ApplicationStatusBadge from '@/components/ApplicationStatusBadge.vue'

import {
  formatDate,
  formatDateTime,
} from '@/utils/formatters'

const dashboardStore = useDashboardStore()

// Fetch the authenticated user's statistics when Dashboard opens
onMounted(() => {
  dashboardStore.fetchDashboard()
})
</script>

<template>
  <AppNav />

  <main class="page-container">
    <header class="page-header">
      <h1 class="page-title">Dashboard</h1>

      <p class="page-description">
        Track your job search progress at a glance.
      </p>
    </header>

    <div
      v-if="dashboardStore.loading"
      class="card"
    >
      Loading dashboard...
    </div>

    <p
      v-else-if="dashboardStore.error"
      class="page-error"
    >
      {{ dashboardStore.error }}
    </p>

    <div v-else>
      <div class="stats-grid">
        <div class="card stat-card">
          <span class="stat-label">Total Applications</span>
          <strong class="stat-value">
            {{ dashboardStore.stats.total }}
          </strong>
        </div>

        <div class="card stat-card">
          <span class="stat-label">Interested</span>
          <strong class="stat-value">
            {{ dashboardStore.stats.interested }}
          </strong>
        </div>

        <div class="card stat-card">
          <span class="stat-label">Applied</span>
          <strong class="stat-value">
            {{ dashboardStore.stats.applied }}
          </strong>
        </div>

        <div class="card stat-card">
          <span class="stat-label">Screening</span>
          <strong class="stat-value">
            {{ dashboardStore.stats.screening }}
          </strong>
        </div>

        <div class="card stat-card">
          <span class="stat-label">Interviews</span>
          <strong class="stat-value">
            {{ dashboardStore.stats.interview }}
          </strong>
        </div>

        <div class="card stat-card">
          <span class="stat-label">Offers</span>
          <strong class="stat-value">
            {{ dashboardStore.stats.offer }}
          </strong>
        </div>

        <div class="card stat-card">
          <span class="stat-label">Rejected</span>
          <strong class="stat-value">
            {{ dashboardStore.stats.rejected }}
          </strong>
        </div>

        <div class="card stat-card">
          <span class="stat-label">Withdrawn</span>
          <strong class="stat-value">
            {{ dashboardStore.stats.withdrawn }}
          </strong>
        </div>
      </div>

      <section class="recent-section">
        <div class="section-header">
          <h2>Recent Applications</h2>

          <RouterLink
            to="/applications"
            class="view-all-link"
          >
            View all
          </RouterLink>
        </div>

        <div
          v-if="dashboardStore.stats.recent_applications.length === 0"
          class="card"
        >
          No applications yet.
        </div>

        <div
          v-else
          class="recent-list"
        >
          <RouterLink
            v-for="application in dashboardStore.stats.recent_applications"
            :key="application.id"
            :to="`/applications/${application.id}`"
            class="card recent-application"
          >
            <div class="recent-info">
              <strong>
                {{ application.position }}
              </strong>

              <p>
                {{ application.company_name }}
              </p>

              <span class="recent-date">
                Applied: {{ formatDate(application.applied_at) }}
              </span>
            </div>

            <ApplicationStatusBadge :status="application.status" />
          </RouterLink>
        </div>
      </section>

      <section class="recent-section">
        <div class="section-header">
          <h2>Recent Activity</h2>
        </div>

        <div
          v-if="dashboardStore.stats.recent_activity.length === 0"
          class="card"
        >
          No recent activity yet.
        </div>

        <div
          v-else
          class="recent-list"
        >
          <RouterLink
            v-for="activity in dashboardStore.stats.recent_activity"
            :key="activity.id"
            :to="`/applications/${activity.application.id}`"
            class="card activity-item"
          >
            <div class="activity-content">
              <strong>
                {{ activity.application.position }}
              </strong>

              <span class="activity-company">
                {{ activity.application.company_name }}
              </span>

              <div class="activity-transition">
                <ApplicationStatusBadge
                  v-if="activity.from_status"
                  :status="activity.from_status"
                />

                <span v-if="activity.from_status">
                  →
                </span>

                <ApplicationStatusBadge :status="activity.to_status" />
              </div>
            </div>

            <span class="activity-date">
              {{ formatDateTime(activity.created_at) }}
            </span>
          </RouterLink>
        </div>
      </section>
    </div>
  </main>
</template>

<style scoped>
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
}

.stat-card {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.stat-label {
  font-size: 14px;
  color: #6b7280;
}

.stat-value {
  font-size: 32px;
  line-height: 1;
}

@media (max-width: 900px) {
  .stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 520px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
}

.recent-section {
  margin-top: 32px;
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 16px;
}

.section-header h2 {
  margin: 0;
  font-size: 20px;
}

.view-all-link {
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
}

.recent-list {
  display: grid;
  gap: 12px;
}

/* Recent Applications */
.recent-application {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  text-decoration: none;
  color: inherit;
}

.recent-application p {
  margin: 4px 0 0;
  color: #6b7280;
}

.recent-info {
  min-width: 0;
}

.recent-date {
  display: block;
  margin-top: 6px;
  font-size: 13px;
  color: #6b7280;
}

/* Recent Activity */
.activity-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  text-decoration: none;
  color: inherit;
}

.activity-content {
  display: flex;
  flex-direction: column;
  gap: 6px;
  min-width: 0;
}

.activity-company {
  font-size: 14px;
  color: #6b7280;
}

.activity-transition {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 4px;
}

.activity-date {
  flex-shrink: 0;
  font-size: 13px;
  color: #6b7280;
}

/* Mobile */
@media (max-width: 640px) {
  .recent-application,
  .activity-item {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
