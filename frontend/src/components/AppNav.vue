<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>

<template>
  <header class="app-nav">
    <div class="nav-container">
      <RouterLink to="/dashboard" class="brand">
        ApplyTrack
      </RouterLink>

      <nav class="nav-links">
        <RouterLink to="/dashboard">
          Dashboard
        </RouterLink>

        <RouterLink to="/applications">
          Applications
        </RouterLink>
      </nav>

      <button
        type="button"
        class="btn btn-secondary"
        @click="handleLogout"
      >
        Logout
      </button>
    </div>
  </header>
</template>

<!-- Exclusive css style for this component -->
<style scoped>
.app-nav {
  background: #ffffff;
  border-bottom: 1px solid #e5e7eb;
}

.nav-container {
  width: min(1180px, calc(100% - 40px));
  min-height: 64px;
  margin: 0 auto;

  display: flex;
  align-items: center;
  gap: 32px;
}

.brand {
  font-size: 20px;
  font-weight: 700;
}

.nav-links {
  display: flex;
  gap: 24px;
  flex: 1;
}

.nav-links a {
  color: #6b7280;
  font-weight: 500;
}

.nav-links a:hover,
.nav-links a.router-link-active {
  color: #2563eb;
}

@media (max-width: 640px) {
  .nav-container {
    width: calc(100% - 24px);
    gap: 16px;
  }

  .nav-links {
    gap: 12px;
  }
}
</style>
