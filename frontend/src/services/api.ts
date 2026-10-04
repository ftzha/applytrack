import axios from 'axios'

// Reusable Axios instance for all requests to the Laravel API
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    Accept: 'application/json',
  },
})

// Runs automatically before every request made using `api`
api.interceptors.request.use((config) => {
  // Retrieve the saved Sanctum token
  const token = localStorage.getItem('token')

  // Attach the token so Laravel's auth:sanctum can authenticate the request
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  return config
})

export default api
