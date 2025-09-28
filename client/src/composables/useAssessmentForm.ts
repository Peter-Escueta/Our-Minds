import { ref, computed } from 'vue'
import { useApi } from '@/composables/useApi'
import { toast } from 'vue-sonner'
import { type Question } from '@/types'

export function useAssessmentForm(childId: string) {
  const { api, handleApiError } = useApi()

  const selectedAges = ref<Record<number, number>>({})
  const formResponses = ref<Record<number, string>>({})
  const availableAges = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]

  const initializeAges = (categoryIds: number[], defaultAge: number = 3) => {
    categoryIds.forEach((categoryId) => {
      selectedAges.value[categoryId] = defaultAge
    })
  }

  const getQuestionsForCategory = (questions: Question[], categoryId: number) => {
    const age = selectedAges.value[categoryId]
    return questions.filter(
      (q) => q.skill_category_id === categoryId && q.age === age,
    )
  }

  const handleAgeChange = (categoryId: number, age: number, questions: Question[]) => {
    selectedAges.value[categoryId] = age
    const questionIds = getQuestionsForCategory(questions, categoryId).map((q) => q.id)
    questionIds.forEach((id) => {
      delete formResponses.value[id]
    })
  }

  const submitAssessment = async () => {
    try {
      const responses = Object.entries(formResponses.value).map(([questionId, response]) => ({
        question_id: parseInt(questionId),
        response: response,
      }))

      await api.post(`/children/${childId}/assessments`, {
        responses,
        selected_ages: selectedAges.value,
      })

      toast.success('Assessment submitted successfully')
      formResponses.value = {}
      return true
    } catch (error) {
      handleApiError(error, 'Failed to submit assessment')
      return false
    }
  }

  const hasResponses = computed(() => Object.keys(formResponses.value).length > 0)

  return {
    selectedAges,
    formResponses,
    availableAges,
    initializeAges,
    getQuestionsForCategory,
    handleAgeChange,
    submitAssessment,
    hasResponses
  }
}
