import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { toast } from 'vue-sonner'
import { useApi } from '@/composables/useApi'
import { useAuth } from '@/composables/useAuth'
import type { Question, SkillCategory } from '@/types'

export function useChecklist() {
  const categories = ref<SkillCategory[]>([])
  const questions = ref<Question[]>([])
  const availableAges = Array.from({ length: 12 }, (_, i) => i + 1)
  const selectedCategory = ref<number | null>(null)
  const selectedAge = ref<number>(1)
  const newQuestionText = ref('')
  const editingQuestionId = ref<number | null>(null)
  const isLoading = ref(false)
  const isCategoriesLoading = ref(false)
  const { api, handleApiError } = useApi()
  const { checkAuth } = useAuth()

  const findCategory = (id: number | null) => {
    return categories.value.find((c) => c.id === id) ?? null
  }

  const fetchCategories = async () => {
    if (!checkAuth()) return

    try {
      isCategoriesLoading.value = true
      const response = await api.get('/skill-categories')

      if (!Array.isArray(response.data.data)) {
        throw new Error('Invalid categories data format')
      }

      categories.value = response.data.data

      if (categories.value.length > 0 && !selectedCategory.value) {
        selectedCategory.value = categories.value[0].id
      }
    } catch (error) {
      if (axios.isAxiosError(error) && error.response?.status !== 401) {
        handleApiError(error, 'Failed to fetch categories')
      }
      categories.value = []
    } finally {
      isCategoriesLoading.value = false
    }
  }

  const fetchQuestions = async () => {
    if (!selectedCategory.value || !checkAuth()) return

    try {
      isLoading.value = true
      const response = await api.get('/questions', {
        params: { category_id: selectedCategory.value },
      })

      questions.value = response.data
    } catch (error) {
      if (axios.isAxiosError(error) && error.response?.status !== 401) {
        handleApiError(error, 'Failed to fetch questions')
      }
      questions.value = []
    } finally {
      isLoading.value = false
    }
  }

  const getQuestionsForAge = (age: number) => {
    return questions.value.filter((q) => q.age === age)
  }

  const saveQuestion = async () => {
    if (!newQuestionText.value.trim() || !selectedCategory.value || !checkAuth()) {
      toast.warning('Please enter question text and select a category')
      return
    }

    try {
      if (editingQuestionId.value) {
        await api.put(`/questions/${editingQuestionId.value}`, {
          text: newQuestionText.value,
          age: selectedAge.value,
        })
        toast.success('Question updated successfully')
      } else {
        await api.post('/questions', {
          skill_category_id: selectedCategory.value,
          text: newQuestionText.value,
          age: selectedAge.value,
        })
        toast.success('Question added successfully')
      }

      newQuestionText.value = ''
      editingQuestionId.value = null
      await fetchQuestions()
    } catch (error) {
      if (axios.isAxiosError(error) && error.response?.status !== 401) {
        handleApiError(error, 'Failed to save question')
      }
    }
  }

  const editQuestion = (question: Question) => {
    if (!checkAuth()) return
    editingQuestionId.value = question.id
    newQuestionText.value = question.text
    selectedAge.value = question.age
  }

  const deleteQuestion = async (questionId: number) => {
    if (!checkAuth()) return

    try {
      await api.delete(`/questions/${questionId}`)
      toast.success('Question deleted successfully')
      await fetchQuestions()
    } catch (error) {
      if (axios.isAxiosError(error) && error.response?.status !== 401) {
        handleApiError(error, 'Failed to delete question')
      }
    }
  }

  const cancelEdit = () => {
    editingQuestionId.value = null
    newQuestionText.value = ''
  }

  onMounted(async () => {
    if (!checkAuth()) return

    try {
      await fetchCategories()
      await fetchQuestions()
    } catch (error) {
      if (axios.isAxiosError(error) && error.response?.status !== 401) {
        handleApiError(error, 'Failed to initialize component')
      }
    }
  })

  watch(selectedCategory, fetchQuestions)

  return {
    categories,
    questions,
    availableAges,
    selectedCategory,
    selectedAge,
    newQuestionText,
    editingQuestionId,
    isLoading,
    isCategoriesLoading,
    findCategory,
    fetchCategories,
    fetchQuestions,
    getQuestionsForAge,
    saveQuestion,
    editQuestion,
    deleteQuestion,
    cancelEdit,
  }
}
