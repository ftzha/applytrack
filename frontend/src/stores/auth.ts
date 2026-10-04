import { ref } from 'vue'
import { defineStore } from 'pinia'
import api from '@/services/api'

// Shape of the user object returned by Laravel
interface User {
  id: number
  name: string
  email: string
}

export const useAuthStore = defineStore('auth', () => {
  // Global authenticated user state
  const user = ref<User | null>(null)

  // Restore the token from browser storage when Vue starts/reloads
  const token = ref<string | null>(localStorage.getItem('token'))

  // Authenticate user through Laravel's /login API
  async function login(email: string, password: string) {
    const response = await api.post('/login', {
      email,
      password,
    })

    // Keep authenticated user and token in Pinia state
    user.value = response.data.user
    token.value = response.data.token

    // Persist token so authentication survives a browser refresh
    localStorage.setItem('token', response.data.token)

    return response.data
  }

  async function fetchUser() {
    // Ask Laravel which user owns the saved token
    const response = await api.get('/user')

    user.value = response.data

    return response.data
  }

  async function logout() {
    try {
      // Revoke the current Sanctum token in Laravel
      await api.post('/logout')
    } finally {
      // Always clear frontend authentication state
      user.value = null
      token.value = null
      localStorage.removeItem('token')
    }
  }

  // Expose state/actions so Vue components can use them
  return {
    user,
    token,
    login,
    fetchUser,
    logout,
  }
})
