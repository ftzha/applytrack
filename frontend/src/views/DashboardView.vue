<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

import AppNav from '@/components/AppNav.vue'

const authStore = useAuthStore()
</script>

<template>
  <AppNav />

  <main>
    <h1>Dashboard</h1>

    <p v-if="authStore.user">
      Welcome, {{ authStore.user.name }}!
    </p>
  </main>
</template>
