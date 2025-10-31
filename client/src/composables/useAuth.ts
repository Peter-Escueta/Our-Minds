import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import { useApi } from './useApi'

const API_LOGIN_ENDPOINT = import.meta.env.VITE_API_LOGIN_ENDPOINT
const API_LOGOUT_ENDPOINT = import.meta.env.VITE_API_LOGOUT_ENDPOINT

const user = ref(null)
const isLoading = ref(false)
const errors = ref<Record<string, string>>({})
let isInitialized = false

export function useAuth() {
  const router = useRouter()
  const { api, handleApiError } = useApi()

  const initializeUser = () => {

    if (isInitialized) return

    const token = sessionStorage.getItem('auth_token')
    const userData = sessionStorage.getItem('user_data')

    if (token && userData) {
      try {
        user.value = JSON.parse(userData)
        console.log('User initialized from session storage')
      } catch (error) {
        console.error('Failed to parse user data:', error)
        clearAuthData()
      }
    } else {
      user.value = null
    }

    isInitialized = true
  }

  const clearAuthData = () => {
    sessionStorage.removeItem('auth_token')
    sessionStorage.removeItem('user_data')
    sessionStorage.removeItem('user_role')
    user.value = null
    isInitialized = false
  }

  const checkAuth = () => {
  if (!isAuthenticated()) {
    toast.error('Please log in to access this page')
    router.push('/home')
    return false
  }
  return true
}


  const login = async (email: string, password: string) => {
    try {
      isLoading.value = true
      errors.value = {}

      const response = await api.post(API_LOGIN_ENDPOINT, { email, password })
      const { token, user: userData } = response.data


      const authToken = typeof token === 'object' ? token.id?.toString() : token

      if (!authToken) {
        throw new Error('No authentication token received')
      }

      sessionStorage.setItem('auth_token', authToken)
      sessionStorage.setItem('user_data', JSON.stringify(userData))
      sessionStorage.setItem('user_role', userData.role)

      user.value = userData
      isInitialized = true

      toast.success('Login successful')

      const routeName = userData.role === 'assessor' ? 'children'
        : userData.role === 'consultant' ? 'edit-checklist'
        : 'home'

      router.push({ name: routeName })

    } catch (error) {
      handleApiError(error, 'Login failed')
      throw error
    } finally {
      isLoading.value = false
    }
  }

  const logout = async () => {
    try {
      const token = getUserToken()
      if (token) {
        await api.post(API_LOGOUT_ENDPOINT, {}, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        })
      }
    } catch (error) {
      console.error('Logout API call failed:', error)
           router.push({ name: 'home' })
    } finally {
      clearAuthData()
      toast.success('Logged out successfully')
      router.push({ name: 'home' })
    }
  }

  const isAuthenticated = () => {
    return !!sessionStorage.getItem('auth_token') && !!user.value
  }

  const getUserRole = () => {
    return sessionStorage.getItem('user_role')
  }

  const getUserToken = () => {
    return sessionStorage.getItem('auth_token')
  }


const isConsultant = computed(() => getUserRole() === 'consultant')

  if (!isInitialized) {
    initializeUser()
  }

  return {
    login,
    logout,
    getUserToken,
    user,
    isLoading,
    isConsultant,
    errors,
    isAuthenticated,
    getUserRole,
    initializeUser,
    checkAuth,
  }
}
