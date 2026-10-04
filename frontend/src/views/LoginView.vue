<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// Form fields
const email = ref('')
const password = ref('')

// UI state
const loading = ref(false)
const error = ref('')

async function handleLogin() {
  loading.value = true
  error.value = ''

  try {
    // Pinia handles the actual API login request
    await authStore.login(email.value, password.value)

    // Login successful; send user to dashboard
    router.push('/dashboard')
  } catch {
    // Error handling
    error.value = 'Invalid email or password.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <main>
    <h1>ApplyTrack</h1>
    <p>Track your job applications in one place.</p>

    <form @submit.prevent="handleLogin">
      <div>
        <label for="email">Email</label>
        <input
          id="email"
          v-model="email"
          type="email"
          placeholder="you@example.com"
          required
        />
      </div>

      <div>
        <label for="password">Password</label>
        <input
          id="password"
          v-model="password"
          type="password"
          placeholder="Enter your password"
          required
        />
      </div>

      <p v-if="error">
        {{ error }}
      </p>

      <button type="submit" :disabled="loading">
        {{ loading ? 'Signing in...' : 'Sign in' }}
      </button>
    </form>
  </main>
</template>
