import axios from 'axios'
import { useRouter } from 'vue-router'
import { toast } from 'vue-sonner'

export function useApi() {
  const router = useRouter()

  const api = axios.create({
    baseURL: 'http://localhost:8000/api',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
    },
  })

  // Request interceptor
  api.interceptors.request.use((config) => {
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    } else {
      router.push('/login')
      throw new Error('No authentication token found')
    }
    return config
  })

  // Response interceptor for error handling
  api.interceptors.response.use(
    (response) => response,
    (error) => {
      handleApiError(error, 'An error occurred')
      return Promise.reject(error)
    }
  )

  const handleApiError = (error: unknown, defaultMessage: string) => {
    if (
      typeof error === 'object' &&
      error !== null &&
      'response' in error &&
      typeof (error as { response?: { status?: number; data?: { message?: string } } }).response ===
        'object'
    ) {
      const errResp = (error as { response?: { status?: number; data?: { message?: string } } })
        .response
      if (errResp?.status === 401) {
        toast.error('Session expired. Please login again.')
        router.push('/login')
      } else {
        const message = errResp?.data?.message || defaultMessage
        toast.error(message)
      }
    } else {
      toast.error(defaultMessage)
    }
  }

  return {
    api,
    handleApiError
  }
}
