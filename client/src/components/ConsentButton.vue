<template>
  <Button
    @click="downloadConsent"
    :disabled="isLoading"
    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
  >
    <Loader2 v-if="isLoading" class="w-4 h-4 mr-2 animate-spin" />
    {{ isLoading ? 'Downloading...' : 'Download Consent Form' }}
  </Button>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Loader2 } from 'lucide-vue-next'
import { useApi } from '@/composables/useApi'
import { toast } from 'vue-sonner'

const props = defineProps({
  childId: {
    type: Number,
    required: true,
  },
})

const { api, handleApiError } = useApi()
const isLoading = ref(false)

const downloadConsent = async () => {
  try {
    isLoading.value = true

    const response = await api.get(`/children/${props.childId}/consent`, {
      responseType: 'blob',
    })

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `consent-form-${props.childId}.pdf`)
    document.body.appendChild(link)
    link.click()

    link.parentNode?.removeChild(link)
    window.URL.revokeObjectURL(url)

    toast.success('Consent form downloaded successfully')
  } catch (error) {
    console.error('Error downloading consent form:', error)
    handleApiError(error, 'Failed to download consent form')
  } finally {
    isLoading.value = false
  }
}
</script>
