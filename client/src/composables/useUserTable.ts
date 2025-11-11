import { ref } from 'vue'
import { useApi } from '@/composables/useApi'
import type { User } from '@/types'

export function useUserTable() {
  const users = ref<User[]>([])
  const isLoading = ref(false)
  const { api } = useApi()
  const API_BASE_URL = import.meta.env.VITE_API_BASE_URL
  const fetchUsers = async () => {
    try {
      isLoading.value = true
      const response = await api.get(API_BASE_URL + '/users')
      users.value = response.data
    } catch (error) {
      console.error('Error fetching users:', error)
    } finally {
      isLoading.value = false

    }
  }
  return {users, isLoading, fetchUsers}
}
