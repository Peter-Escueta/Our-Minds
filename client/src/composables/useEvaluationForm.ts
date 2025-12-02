import { ref } from 'vue'
import { useApi } from '@/composables/useApi'
import { toast } from 'vue-sonner'
import type { Assessment, Evaluation } from '@/types'

export function useEvaluationForm() {
  const { api, handleApiError } = useApi()
  const assessment = ref<Assessment | null>(null)
  const isLoading = ref(false)
  const currentAssessmentId = ref<string | null>(null)


  const performSubmission = async (urlSuffix: string, data: any, successMessage: string) => {
    try {
      isLoading.value = true

      const id = assessment.value?.id || currentAssessmentId.value
      if (!id) {
        toast.error('No assessment loaded - please refresh.')
        return false
      }

      const endpoint = import.meta.env.VITE_API_ASSESSMENT_ENDPOINT
      const fullUrl = `${endpoint}/${id}/${urlSuffix}`




      await api.post(fullUrl, { assessment_id: id, ...data })

      toast.success(successMessage)
      return true

    } catch (error: any) {
      console.error(`❌ Submission failed for ${urlSuffix}:`, error)


      if (error.response?.status === 403) {
        toast.error('Permission Denied: You are not authorized to perform this action.')
      } else {

        const msg = error.response?.data?.message || error.message || 'Unknown error'
        toast.error(`Error: ${msg}`)
      }
      return false
    } finally {
      isLoading.value = false
    }
  }



  const fetchAssessment = async (assessmentId: string) => {
    try {
      isLoading.value = true
      currentAssessmentId.value = assessmentId
      const endpoint = import.meta.env.VITE_API_ASSESSMENT_ENDPOINT
      const response = await api.get(`${endpoint}/${assessmentId}/results`)

      if (!response.data?.data) throw new Error('No data found')

      assessment.value = response.data.data
    } catch (error) {
      handleApiError(error, 'Failed to load assessment')
    } finally {
      isLoading.value = false
    }
  }


  const submitBackground = async (data: Partial<Evaluation>) => {
    return await performSubmission(
      'background',
      { background_information: data.background_information },
      'Background information saved successfully'
    )
  }


  const submitEvaluation = async (data: Partial<Evaluation>) => {
    return await performSubmission(
      'finalize',
      {
        recommendations: data.recommendations,
        websites: data.websites,
        status: 'completed'
      },
      'Evaluation completed successfully'
    )
  }

  return {
    assessment,
    isLoading,
    fetchAssessment,
    submitBackground,
    submitEvaluation
  }
}
