<script setup lang="ts">
import { ref, onMounted } from 'vue'
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
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
import { Button } from '@/components/ui/button'
import { Loader2 } from 'lucide-vue-next'
import Header from '@/components/Header.vue'
const props = defineProps<{
  id: string
}>()
const childId = ref(Number(props.id))

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
  questions_count?: number
}

const router = useRouter()

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
    router.push('/login')
    throw new Error('No authentication token found')
  }
  return config
})

const handleApiError = (error: any, defaultMessage: string) => {
  console.error('API Error:', error)
  if (error.response?.status === 401) {
    toast.error('Session expired. Please login again.')
    router.push('/login')
  } else {
    const message = error.response?.data?.message || defaultMessage
    toast.error(message)
  }
}


const categories = ref<SkillCategory[]>([])
const questions = ref<Question[]>([])
const selectedAges = ref<Record<number, number>>({})
const formResponses = ref<Record<number, string>>({})
const availableAges = [1,2, 3, 4, 5,6,7,8,9,10,11,12]
const isLoading = ref(false)


const fetchData = async () => {
  try {
    isLoading.value = true
    
   
    const categoriesResponse = await api.get('/skill-categories')
    if (!categoriesResponse.data?.data || !Array.isArray(categoriesResponse.data.data)) {
      throw new Error('Categories data is not in expected format')
    }
    categories.value = categoriesResponse.data.data
    
   
    categories.value.forEach(category => {
      selectedAges.value[category.id] = 3
    })
    
    const questionsResponse = await api.get('/questions')
    
    let questionsData = []
    if (Array.isArray(questionsResponse.data)) {
      questionsData = questionsResponse.data
    } else if (questionsResponse.data?.data && Array.isArray(questionsResponse.data.data)) {
      questionsData = questionsResponse.data.data
    } else {
      throw new Error('Questions data is not in expected format')
    }
    
    questions.value = questionsData.map((question: any) => {
      const category = categories.value.find(cat => cat.id === question.skill_category_id)
      return {
        ...question,
        category
      }
    })
    

    
  } catch (error) {
    handleApiError(error, 'Failed to load assessment data')
  } finally {
    isLoading.value = false
  }
}

const getQuestionsForCategory = (categoryId: number) => {
  const age = selectedAges.value[categoryId]
  const filtered = questions.value.filter(q => 
    q.skill_category_id === categoryId && q.age === age
  )
  return filtered
}

const handleAgeChange = (categoryId: number, age: number) => {
  selectedAges.value[categoryId] = age
  const questionIds = getQuestionsForCategory(categoryId).map(q => q.id)
  questionIds.forEach(id => {
    delete formResponses.value[id]
  })
}

const submitAssessment = async () => {
  try {
    const responses = Object.entries(formResponses.value).map(([questionId, response]) => ({
      question_id: questionId,
      response: response
    }))
    
    await api.post(`/children/${props.id}/assessments`, {
      responses,
      selected_ages: selectedAges.value
    })
    
    toast.success('Assessment submitted successfully')
    formResponses.value = {}
  } catch (error) {
    handleApiError(error, 'Failed to submit assessment')
  }
}

onMounted(fetchData)
</script>

<template>
  <div class="min-h-screen">
    <div class="hidden">
  bg-green-700 bg-red-700 bg-blue-700 bg-yellow-700 bg-purple-700
</div>
  

    <main class="container mx-auto font-display py-8 border-1">
      <h1 class="text-center text-2xl text-primary font-bold mb-5">Child Development Assessment Form</h1>

      <div v-if="isLoading" class="flex justify-center py-8">
        <Loader2 class="h-8 w-8 animate-spin" />
        <span class="ml-2">Loading assessment form...</span>
      </div>

      <div v-else class="space-y-8 px-20">
        <Card 
          v-for="category in categories" 
          :key="category.id"
          class="py-0 gap-0 text-center"
        >
          <!-- Header with dynamic background color -->
          <CardHeader 
            class="pb-0 mb-0 p-4 border-b-0 rounded-t-lg"
            :class="category.color"
          >
            <div class="grid grid-cols-12 items-center">
              <div class="col-span-12">
                <CardTitle class="font-bold text-white">{{ category.name }} </CardTitle>
                     <div class=" flex justify-end items-center space-x-2 text-white">
                <Label class="whitespace-nowrap">Age:</Label>
                <Select 
                  :modelValue="selectedAges[category.id]" 
                  @update:modelValue="(value) => handleAgeChange(category.id, Number(value))"
                >
                  <SelectTrigger class="w-[100px] bg-white/20">
                    <SelectValue placeholder="Select age" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem 
                      v-for="age in availableAges" 
                      :key="age" 
                      :value="age"
                    >
                      {{ age }}
                    </SelectItem>
                  </SelectContent>
                </Select>
              </div>
              </div>
         
            </div>
          </CardHeader>
          
          <CardContent class="p-0">
            <div 
              v-if="getQuestionsForCategory(category.id).length === 0" 
              class="text-foreground p-4"
            >
              No questions available for age {{ selectedAges[category.id] }}
            </div>
            
            <div v-else>
              <div class="border rounded-b-lg overflow-hidden">
                <div class="grid grid-cols-12 bg-gray-50 p-4 font-bold border-b">
                  <div class="col-span-1 text-center text-green-600">CAN</div>
                  <div class="col-span-10 text-center text-foreground">QUESTION</div>
                  <div class="col-span-1 text-center text-red-600">CANNOT</div>
                </div>
                
                <div 
                  v-for="question in getQuestionsForCategory(category.id)" 
                  :key="question.id" 
                  class="grid grid-cols-12 p-4 border-t hover:bg-gray-50"
                >
                  <div class="col-span-1 flex justify-center">
                    <RadioGroup v-model="formResponses[question.id]">
                      <RadioGroupItem value="can" :id="`${question.id}-can`" />
                    </RadioGroup>
                  </div>
                  
                  <div class="col-span-10 flex items-center justify-center">
                    <Label :for="`${question.id}-can`" class="cursor-pointer ml-4">
                      {{ question.text }}
                    </Label>
                  </div>
                  
                  <div class="col-span-1 flex justify-center">
                    <RadioGroup v-model="formResponses[question.id]">
                      <RadioGroupItem value="cannot" :id="`${question.id}-cannot`" />
                    </RadioGroup>
                  </div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
      
      <div class="mt-8 flex justify-center">
        <Button 
          @click="submitAssessment"
          class="px-4 py-2 bg-primary text-primary-foreground rounded hover:bg-primary/90"
          :disabled="Object.keys(formResponses).length === 0"
        >
          Submit Assessment
        </Button>
      </div>
    </main>
  </div>
</template>