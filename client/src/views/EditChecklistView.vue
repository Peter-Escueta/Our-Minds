<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { toast } from 'vue-sonner'
import { useRouter } from 'vue-router'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Plus, Trash2, Pencil, Loader2 } from 'lucide-vue-next'
import Header from '@/components/Header.vue'

const router = useRouter()

interface Question {
  id: number
  text: string
  age: number
  skill_category_id: number
  category?: {
    id: number
    name: string
    slug: string
    color: string
  }
}

interface SkillCategory {
  id: number
  name: string
  slug: string
  color: string
  questions_count: number
}

const categories = ref<SkillCategory[]>([])
const questions = ref<Question[]>([])
const availableAges = Array.from({ length: 12 }, (_, i) => i + 1)
const selectedCategory = ref<number | null>(null)
const selectedAge = ref<number>(1)
const newQuestionText = ref('')
const editingQuestionId = ref<number | null>(null)
const isLoading = ref(false)
const isCategoriesLoading = ref(false)

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
})

api.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  } else {
    router.push('/')
    throw new Error('No authentication token found')
  }
  return config
})

const handleApiError = (error: any, defaultMessage: string) => {
  console.error('API Error:', error)
  
  if (error.response?.status === 401) {
    toast.error('Session expired. Please login again.')
    router.push('/')
  } else {
    const message = error.response?.data?.message || defaultMessage
    toast.error(message)
  }
}

const findCategory = (id: number | null) => {
  return categories.value.find(c => c.id === id) ?? null
}

const fetchCategories = async () => {
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
    handleApiError(error, 'Failed to fetch categories')
    categories.value = []
  } finally {
    isCategoriesLoading.value = false
  }
}

const fetchQuestions = async () => {
  if (!selectedCategory.value) return
  
  try {
    isLoading.value = true
    const response = await api.get('/questions', {
      params: { category_id: selectedCategory.value }
    })
    
    questions.value = response.data
  } catch (error) {
    handleApiError(error, 'Failed to fetch questions')
    questions.value = []
  } finally {
    isLoading.value = false
  }
}

const getQuestionsForAge = (age: number) => {
  return questions.value.filter(q => q.age === age)
}

const saveQuestion = async () => {
  if (!newQuestionText.value.trim() || !selectedCategory.value) {
    toast.warning('Please enter question text and select a category')
    return
  }

  try {
    if (editingQuestionId.value) {
      await api.put(`/questions/${editingQuestionId.value}`, {
        text: newQuestionText.value,
        age: selectedAge.value
      })
      toast.success('Question updated successfully')
    } else {
      await api.post('/questions', {
        skill_category_id: selectedCategory.value,
        text: newQuestionText.value,
        age: selectedAge.value
      })
      toast.success('Question added successfully')
    }
    
    newQuestionText.value = ''
    editingQuestionId.value = null
    await fetchQuestions()
  } catch (error) {
    handleApiError(error, 'Failed to save question')
  }
}

const editQuestion = (question: Question) => {
  editingQuestionId.value = question.id
  newQuestionText.value = question.text
  selectedAge.value = question.age
}

const deleteQuestion = async (questionId: number) => {
  try {
    await api.delete(`/questions/${questionId}`)
    toast.success('Question deleted successfully')
    await fetchQuestions()
  } catch (error) {
    handleApiError(error, 'Failed to delete question')
  }
}

const cancelEdit = () => {
  editingQuestionId.value = null
  newQuestionText.value = ''
}

onMounted(async () => {
  try {
    await fetchCategories()
    await fetchQuestions()
  } catch (error) {
    handleApiError(error, 'Failed to initialize component')
  }
})

watch(selectedCategory, fetchQuestions)
</script>

<template>
  <div class="min-h-screen">

      <h1 class="text-center text-2xl text-primary font-bold mb-8">Manage Assessment Questions</h1>

      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Left sidebar - Category selection -->
        <div class="lg:col-span-1 space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Categories</CardTitle>
            </CardHeader>
            <CardContent>
              <div v-if="isCategoriesLoading" class="flex justify-center py-4">
                <Loader2 class="h-6 w-6 animate-spin" />
                <span class="ml-2">Loading...</span>
              </div>
              <div v-else class="space-y-2">
                <Button
                  v-for="category in categories"
                  :key="category.id"
                  variant="ghost"
                  class="w-full justify-start"
                  :class="{ 'bg-accent': selectedCategory === category.id }"
                  @click="selectedCategory = category.id"
                >
                  <span class="truncate">{{ category.name }}</span>
                  <Badge class="ml-2">{{ category.questions_count }}</Badge>
                </Button>
                <div v-if="categories.length === 0" class="text-center text-gray-500 py-2">
                  No categories available
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Main content - Question management -->
        <div class="lg:col-span-3 space-y-6">
          <!-- Add/Edit Question Form -->
          <Card>
            <CardHeader>
              <CardTitle>
                {{ editingQuestionId ? 'Edit Question' : 'Add New Question' }}
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <Label for="age">Age Group</Label>
                    <Select v-model="selectedAge">
                      <SelectTrigger>
                        <SelectValue placeholder="Select age group" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem 
                          v-for="age in availableAges" 
                          :key="age" 
                          :value="age"
                        >
                          Age {{ age }}
                        </SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                  <div>
                    <Label for="category">Skill Category</Label>
                    <Select 
                      v-model="selectedCategory" 
                      :disabled="isCategoriesLoading || categories.length === 0"
                    >
                      <SelectTrigger>
                        <SelectValue>
                          {{ findCategory(selectedCategory)?.name || 'Select category' }}
                        </SelectValue>
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem 
                          v-for="category in categories" 
                          :key="category.id" 
                          :value="category.id"
                        >
                          {{ category.name }}
                        </SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                </div>

                <div>
                  <Label for="question">Question Text</Label>
                  <Input
                    id="question"
                    v-model="newQuestionText"
                    placeholder="Enter the assessment question"
                    @keyup.enter="saveQuestion"
                  />
                </div>

                <div class="flex justify-end space-x-2">
                  <Button
                    v-if="editingQuestionId"
                    variant="outline"
                    @click="cancelEdit"
                  >
                    Cancel
                  </Button>
                  <Button
                    @click="saveQuestion"
                    :disabled="!newQuestionText.trim() || !selectedCategory"
                  >
                    <Plus class="mr-2 h-4 w-4" />
                    {{ editingQuestionId ? 'Update Question' : 'Add Question' }}
                  </Button>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Questions List -->
          <Card>
            <CardHeader>
              <CardTitle>
                Questions for {{ findCategory(selectedCategory)?.name || 'Selected Category' }}
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div v-if="isLoading" class="flex justify-center py-8">
                <Loader2 class="h-8 w-8 animate-spin" />
                <span class="ml-2">Loading questions...</span>
              </div>

              <div v-else class="space-y-6">
                <div v-for="age in availableAges" :key="age">
                  <div v-if="getQuestionsForAge(age).length > 0" class="space-y-2">
                    <h3 class="text-lg font-semibold">Age {{ age }}</h3>
                    <Table>
                      <TableHeader>
                        <TableRow>
                          <TableHead class="w-[50px]">#</TableHead>
                          <TableHead>Question</TableHead>
                          <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                      </TableHeader>
                      <TableBody>
                        <TableRow 
                          v-for="(question, index) in getQuestionsForAge(age)" 
                          :key="question.id"
                        >
                          <TableCell>{{ index + 1 }}</TableCell>
                          <TableCell>{{ question.text }}</TableCell>
                          <TableCell>
                            <div class="flex space-x-2">
                              <Button
                                variant="ghost"
                                size="sm"
                                @click="editQuestion(question)"
                              >
                                <Pencil class="h-4 w-4" />
                              </Button>
                              <Button
                                variant="ghost"
                                size="sm"
                                @click="deleteQuestion(question.id)"
                              >
                                <Trash2 class="h-4 w-4 text-red-600" />
                              </Button>
                            </div>
                          </TableCell>
                        </TableRow>
                      </TableBody>
                    </Table>
                  </div>
                </div>

                <div 
                  v-if="questions.length === 0" 
                  class="text-center text-gray-500 py-8"
                >
                  No questions found for this category. Add your first question above.
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
  </div>
</template>