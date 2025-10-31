// composables/useChildren.ts
import { ref } from 'vue'
import { useApi } from '@/composables/useApi'
import type { Child } from '@/types/child'

export function useChildren() {
  const data = ref<Child[]>([])
  const isLoading = ref(false)
  const { api } = useApi()

  const API_CHILDREN_ENDPOINT = import.meta.env.VITE_API_CHILDREN_ENDPOINT

  const fetchChildren = async () => {
    try {
      isLoading.value = true

      const response = await api.get(API_CHILDREN_ENDPOINT, {
        params: { with: 'assessments' },
      })

      data.value = response.data
    } catch (error) {

      console.error('Error fetching children:', error)
    } finally {
      isLoading.value = false
    }
  }

  return { data, isLoading, fetchChildren }
}
