import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  withCredentials: true,
  withXSRFToken: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
})

let csrfInitialized = false

export async function initCsrf() {
  if (!csrfInitialized) {
    await axios.get('/sanctum/csrf-cookie', { withCredentials: true })
    csrfInitialized = true
  }
}

// Auto-redirect to login on 401
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      import('@/router').then(({ default: router }) => {
        router.push('/login')
      })
    }
    return Promise.reject(error)
  }
)

export default api
