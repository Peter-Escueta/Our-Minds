import axios from 'axios'
import { useRouter } from 'vue-router'
import { toast } from 'vue-sonner'

export function useApi() {
  const router = useRouter()
  const API_BASE_URL = import.meta.env.VITE_API_BASE_URL
  const API_LOGIN_ENDPOINT = import.meta.env.VITE_API_LOGIN_ENDPOINT
  const API_LOGOUT_ENDPOINT = import.meta.env.VITE_API_LOGOUT_ENDPOINT

  const api = axios.create({
    baseURL: API_BASE_URL,
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
    },
    withCredentials: true,
  })

  const publicEndpoints = [
    API_LOGIN_ENDPOINT,
    API_LOGOUT_ENDPOINT,
  ]

  api.interceptors.request.use(
    (config) => {
      const token = sessionStorage.getItem('auth_token')

      const requestUrl = config.url
      const isPublicEndpoint = publicEndpoints.some(endpoint =>
        requestUrl === endpoint || requestUrl?.includes(endpoint)
      )

      if (token && !isPublicEndpoint) {
        config.headers.Authorization = `Bearer ${token}`
      }

      return config
    },
    (error) => {
      return Promise.reject(error)
    }
  )

  api.interceptors.response.use(
    (response) => {
      return response
    },
    (error) => {
      handleApiError(error)
      return Promise.reject(error)
    }
  )

  const handleApiError = (error: unknown, defaultMessage: string = 'An error occurred') => {
    if (axios.isAxiosError(error)) {
      const status = error.response?.status
      const data = error.response?.data
      const serverMessage = data?.message || data?.error || defaultMessage

      switch (status) {
        case 401:
          sessionStorage.removeItem('auth_token')
          sessionStorage.removeItem('user_data')
          sessionStorage.removeItem('user_role')

          const currentRoute = router.currentRoute.value.path
          if (!currentRoute.includes(API_LOGIN_ENDPOINT)) {
            toast.error('Session expired. Please login again.')
            router.push('')
          }
          break

        case 403:
          toast.error('You do not have permission to perform this action.')
          break

        case 404:
          toast.error('The requested resource was not found.')
          break

        case 422:
          break

        case 429:
          toast.error('Too many requests. Please try again later.')
          break

        case 500:
          toast.error('Server error. Please try again later.')
          break

        case 502:
        case 503:
        case 504:
          toast.error('Service temporarily unavailable. Please try again later.')
          break

        default:
          if (!error.response) {
            toast.error('Network error. Please check your connection.')
          } else {
            toast.error(serverMessage)
          }
      }
    }
    else if (error instanceof Error) {
      toast.error(error.message || defaultMessage)
    }
    else {
      toast.error(defaultMessage)
    }
  }

  return {
    api,
    handleApiError
  }
}
