import { ref, computed, reactive } from 'vue'
import { useApi } from '@/composables/useApi'
import { toast } from 'vue-sonner'
import { type Question } from '@/types'

export function useAssessmentForm(childId: string) {
  const { api, handleApiError } = useApi()

  const selectedAges = ref<Record<number, number[]>>({})
  const formResponses = reactive<Record<string, string>>({})
  const availableAges = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]

  const initializeAges = (categoryIds: number[]) => {
    categoryIds.forEach((categoryId) => {

      if (!selectedAges.value[categoryId]) {
        selectedAges.value[categoryId] = []
      }
    })
  }

  const getQuestionsForCategory = (questions: Question[], categoryId: number, age?: number) => {
    if (age) {

      return questions.filter(
        (q) => q.skill_category_id === categoryId && q.age === age,
      )
    } else {

      const ages = selectedAges.value[categoryId] || []
      return questions.filter(
        (q) => q.skill_category_id === categoryId && ages.includes(q.age),
      )
    }
  }

  const handleAgeChange = (categoryId: number, age: number) => {
    if (!selectedAges.value[categoryId]) {
      selectedAges.value[categoryId] = []
    }


    if (!selectedAges.value[categoryId].includes(age)) {
      selectedAges.value[categoryId].push(age)
    }
  }

const handleAgeRemove = (categoryId: number, age: number) => {
  if (selectedAges.value[categoryId]) {
    selectedAges.value[categoryId] = selectedAges.value[categoryId].filter(a => a !== age)


    Object.keys(formResponses).forEach(key => {
      const parts = key.split('-').map(Number)
      if (parts.length === 2 && parts[1] === age) {

        const [questionId, responseAge] = parts
        const question = questions.value.find(q => q.id === questionId)
        if (question && question.skill_category_id === categoryId) {
          delete formResponses[key]
        }
      }
    })
  }
}
const handleCategoryRemove = (categoryId: number) => {
  if (selectedAges.value[categoryId]) {
    const agesToRemove = selectedAges.value[categoryId]
    delete selectedAges.value[categoryId]

    Object.keys(formResponses).forEach(key => {
      const parts = key.split('-').map(Number)
      if (parts.length === 2) {
        const [questionId, responseAge] = parts
        const question = questions.value.find(q => q.id === questionId)
        if (question && question.skill_category_id === categoryId && agesToRemove.includes(responseAge)) {
          delete formResponses[key]
        }
      }
    })
  }

}
const submitAssessment = async () => {
  try {
    const responses = Object.entries(formResponses).map(([compositeKey, value]) => {
      const parts = compositeKey.split('-').map(Number)

      if (parts.length !== 2) {
        console.error('Invalid key format:', compositeKey)
        return null
      }

      const [questionId, age] = parts

      if (age < 1 || age > 12) {
        console.error(`Invalid age: ${age} in key: ${compositeKey}`)
        return null
      }

      return {
        question_id: questionId,
        age: age,
        response: value
      }
    }).filter(Boolean)

    if (responses.length === 0) {
      toast.error('No valid responses to submit')
      return false
    }

    await api.post(`/children/${childId}/assessments`, {
      responses,
      selected_ages: selectedAges.value,
    })

    toast.success('Assessment submitted successfully')


    Object.keys(formResponses).forEach(key => {
      delete formResponses[key]
    })
    return true
  } catch (error) {
    handleApiError(error, 'Failed to submit assessment')
    return false
  }
}
  const hasResponses = computed(() => Object.keys(formResponses).length > 0)

  return {
    selectedAges,
    formResponses,
    availableAges,
    initializeAges,
    getQuestionsForCategory,
    handleAgeChange,
    handleAgeRemove,
    submitAssessment,
    handleCategoryRemove,
    hasResponses
  }
}
