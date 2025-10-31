import { ref } from 'vue'
import { useApi } from '@/composables/useApi'
import { type Question, type SkillCategory } from '@/types'

export function useAssessmentData() {
  const { api, handleApiError } = useApi()

  const categories = ref<SkillCategory[]>([])
  const questions = ref<Question[]>([])
  const isLoading = ref(false)

  const fetchData = async () => {
    try {
      isLoading.value = true

      const [categoriesResponse, questionsResponse] = await Promise.all([
        api.get('/skill-categories'),
        api.get('/questions')
      ])

      if (!categoriesResponse.data?.data || !Array.isArray(categoriesResponse.data.data)) {
        throw new Error('Categories data is not in expected format')
      }
      categories.value = categoriesResponse.data.data

      let questionsData = []
      if (Array.isArray(questionsResponse.data)) {
        questionsData = questionsResponse.data
      } else if (questionsResponse.data?.data && Array.isArray(questionsResponse.data.data)) {
        questionsData = questionsResponse.data.data
      } else {
        throw new Error('Questions data is not in expected format')
      }

      questions.value = questionsData
        .map((question: Question) => {
          const category = categories.value.find((cat) => cat.id === question.skill_category_id)


          if (!question.age) {
            console.warn(`Question ${question.id} is missing age property`)
          }

          if (!question.skill_category_id) {
            console.warn(`Question ${question.id} is missing skill_category_id`)
          }

          return {
            ...question,
            category,
          }
        })

        .sort((a, b) => (a.age || 0) - (b.age || 0))
    } catch (error) {
      handleApiError(error, 'Failed to load assessment data')
      throw error
    } finally {
      isLoading.value = false
    }
  }


  const getAvailableAgesForCategory = (categoryId: number) => {
    const categoryQuestions = questions.value.filter(q => q.skill_category_id === categoryId)
    const ages = [...new Set(categoryQuestions.map(q => q.age).filter(age => age != null))]
    return ages.sort((a, b) => a - b)
  }


  const getQuestionsByCategoryAndAge = (categoryId: number, age: number) => {
    return questions.value.filter(
      q => q.skill_category_id === categoryId && q.age === age
    )
  }

  return {
    categories,
    questions,
    isLoading,
    fetchData,
    getAvailableAgesForCategory,
    getQuestionsByCategoryAndAge
  }
}
