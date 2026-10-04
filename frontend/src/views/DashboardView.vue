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
  <main>
    <h1>Dashboard</h1>

    <p v-if="authStore.user">
      Welcome, {{ authStore.user.name }}!
    </p>

    <button @click="handleLogout">
      Logout
    </button>
  </main>
</template>
