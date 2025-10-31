import { ref } from 'vue'
import { useApi } from '@/composables/useApi'
import { toast } from 'vue-sonner'
import type { Assessment, Evaluation } from '@/types'

export function useEvaluationForm() {
  const { api, handleApiError } = useApi()
  const assessment = ref<Assessment | null>(null)
  const isLoading = ref(false)
  const currentAssessmentId = ref<string | null>(null) // Track the current assessment ID

  const fetchAssessment = async (assessmentId: string) => {
    try {
      isLoading.value = true
      currentAssessmentId.value = assessmentId // Store the assessment ID

      const endpoint = import.meta.env.VITE_API_ASSESSMENT_ENDPOINT
      console.log('🔍 Fetching assessment from:', `${endpoint}/${assessmentId}/results`)

      const response = await api.get(`${endpoint}/${assessmentId}/results`)

      if (!response.data?.data) {
        throw new Error('Invalid response format - no data found')
      }

      assessment.value = response.data.data
      console.log('📊 Assessment data loaded:', assessment.value)
      console.log('🆔 Assessment ID:', assessment.value.id)

    } catch (error) {
      console.error('❌ Failed to load assessment:', error)
      handleApiError(error, 'Failed to load assessment data')
      throw error
    } finally {
      isLoading.value = false
    }
  }

  const submitEvaluation = async (evaluationData: Evaluation) => {
    try {
      console.log('🔄 Starting evaluation submission...')
      console.log('📋 Current assessment state:', assessment.value)
      console.log('🆔 Stored assessment ID:', currentAssessmentId.value)

      // Try multiple ways to get the assessment ID
      let assessmentIdToUse = assessment.value?.id || currentAssessmentId.value

      if (!assessmentIdToUse) {
        const errorMsg = 'No assessment loaded - please refresh the page and try again'
        console.error(errorMsg)
        toast.error(errorMsg)
        return false
      }

      const endpoint = import.meta.env.VITE_API_ASSESSMENT_ENDPOINT
      const payload = {
        assessment_id: assessmentIdToUse,
        ...evaluationData
      }

      console.log('📤 Sending evaluation to:', `${endpoint}/${assessmentIdToUse}/evaluations`)
      console.log('📦 Payload:', payload)

      const response = await api.post(`${endpoint}/${assessmentIdToUse}/evaluations`, payload)

      console.log('✅ Evaluation submitted successfully:', response.data)
      toast.success('Evaluation submitted successfully')
      return true

    } catch (error: any) {
      console.error('❌ Evaluation submission failed:', error)

      if (error.response) {
        console.error('Server responded with:', error.response.status, error.response.data)
        toast.error(`Server error: ${error.response.data?.message || 'Unknown error'}`)
      } else if (error.request) {
        console.error('No response received:', error.request)
        toast.error('No response from server. Please check your connection.')
      } else {
        console.error('Request setup error:', error.message)
        toast.error(`Failed to submit evaluation: ${error.message}`)
      }

      return false
    }
  }

  return {
    assessment,
    isLoading,
    fetchAssessment,
    submitEvaluation
  }
}
