import { ref } from 'vue'
import { useApi } from '@/composables/useApi'
import type { User } from '@/types'

export function useUserTable() {
  const users = ref<User[]>([])
  const isLoading = ref(false)
  const { api } = useApi()
  const API_USER_ENDPOINT = import.meta.env.VITE_API_USER_ENDPOINT
  const fetchUsers = async () => {
    try {
      isLoading.value = true
      const respone = await api.get(API_USER_ENDPOINT)
      users.value = respone.data
    } catch (error) {
      console.error('Error fetching users:', error)
    } finally {
      isLoading.value = false

    }
  }
  return {users, isLoading, fetchUsers}
}
