import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api, { initCsrf } from '@/api/axios'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const isAuthenticated = computed(() => user.value !== null)
  const isAdmin = computed(() => user.value?.is_admin === true || user.value?.is_admin === 1)

  async function fetchUser() {
    try {
      const { data } = await api.get('/user')
      user.value = data
    } catch {
      user.value = null
    }
  }

  async function login(credentials) {
    loading.value = true
    error.value = null
    try {
      await initCsrf()
      const { data } = await api.post('/login', credentials)
      user.value = data.user
      return data
    } catch (err) {
      error.value = err.response?.data?.message ?? 'Login failed.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function register(payload) {
    loading.value = true
    error.value = null
    try {
      await initCsrf()
      const { data } = await api.post('/register', payload)
      user.value = data.user
      return data
    } catch (err) {
      error.value = err.response?.data?.message ?? 'Registration failed.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try {
      await api.post('/logout')
    } catch {
      // clear local state regardless of API errors
    } finally {
      user.value = null
    }
  }

  return { user, loading, error, isAuthenticated, isAdmin, fetchUser, login, register, logout }
})
