<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')

const loading = ref(false)
const error = ref('')

async function handleLogin() {
  loading.value = true
  error.value = ''

  try {
    await authStore.login(email.value, password.value)

    router.push('/dashboard')
  } catch {
    error.value = 'Invalid email or password.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <main class="login-page">
    <div class="login-container">
      <div class="login-heading">
        <h1>ApplyTrack</h1>

        <p>
          Track your job applications in one place.
        </p>
      </div>

      <form
        class="card login-card"
        @submit.prevent="handleLogin"
      >
        <div class="form-group">
          <label for="email">
            Email
          </label>

          <input
            id="email"
            v-model="email"
            type="email"
            placeholder="you@example.com"
            autocomplete="email"
            required
          />
        </div>

        <div class="form-group">
          <label for="password">
            Password
          </label>

          <input
            id="password"
            v-model="password"
            type="password"
            placeholder="Enter your password"
            autocomplete="current-password"
            required
          />
        </div>

        <p
          v-if="error"
          class="login-error"
          role="alert"
        >
          {{ error }}
        </p>

        <button
          type="submit"
          class="btn btn-primary login-button"
          :disabled="loading"
        >
          {{ loading ? 'Signing in...' : 'Sign in' }}
        </button>
      </form>
    </div>
  </main>
</template>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 32px 20px;
}

.login-container {
  width: 100%;
  max-width: 420px;
}

.login-heading {
  margin-bottom: 24px;
  text-align: center;
}

.login-heading h1 {
  margin: 0;
  font-size: 32px;
}

.login-heading p {
  margin: 8px 0 0;
  color: #6b7280;
}

.login-card {
  display: grid;
  gap: 20px;
}

.form-group {
  display: grid;
  gap: 8px;
}

.form-group label {
  font-weight: 500;
}

.form-group input {
  width: 100%;
  min-height: 44px;
  padding: 0 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  background: #ffffff;
  font: inherit;
}

.form-group input:focus {
  outline: 2px solid #2563eb;
  outline-offset: 1px;
  border-color: #2563eb;
}

.login-error {
  margin: 0;
  font-size: 14px;
  color: #dc2626;
}

.login-button {
  width: 100%;
}

@media (max-width: 640px) {
  .login-page {
    align-items: flex-start;
    padding: 64px 16px 32px;
  }

  .login-heading h1 {
    font-size: 28px;
  }
}
</style>
