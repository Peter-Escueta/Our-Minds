<template>
  <Header />
  <div class="max-w-4xl mx-auto p-6">
    <Card>
      <CardHeader>
        <CardTitle class="text-2xl font-bold">
          Assessment Evaluation for {{ assessment.child_name }}
        </CardTitle>
        <CardDescription>
          Assessment Date: {{ formatDate(assessment.assessment_date) }}
        </CardDescription>
      </CardHeader>

      <CardContent class="space-y-8">
        <!-- Background Information Section -->
        <section>
          <h2 class="text-xl font-semibold mb-4">Background Information</h2>
          <div class="space-y-4">
            <div>
              <Textarea
                id="background-info"
                v-model="evaluation.background_information"
                placeholder="Example: 'Chas is a 6-year-old who currently struggles with...'"
                class="min-h-[150px]"
              />
            </div>
          </div>
        </section>

        <!-- Developmental Areas Section -->
        <section>
          <h2 class="text-xl font-semibold mb-4">Developmental Areas</h2>
          <div class="space-y-6">
            <Card v-for="category in assessment.categories" :key="category.name" class="p-6">
              <CardHeader class="p-0 pb-4">
                <CardTitle class="text-xl font-semibold text-primary">
                  {{ category.name.toUpperCase() }} (Age {{ category.age }})
                </CardTitle>
              </CardHeader>
              <CardContent class="p-0">
                <ul class="space-y-2">
                  <li 
                    v-for="(response, index) in category.responses" 
                    :key="index"
                    class="flex items-start"
                  >
                    <span 
                      class="mr-2 mt-1 h-2 w-2 rounded-full" 
                      :class="response.startsWith('can') ? 'bg-green-500' : 'bg-amber-500'"
                    />
                    <span>{{ response }}</span>
                  </li>
                </ul>
                <p class="mt-4 text-sm text-muted-foreground italic">
                  {{ category.competency.includes('below') ? 
                    `Below the expected range for age ${category.age}` : 
                    `Within the expected range for age ${category.age}`
                  }}
                </p>
              </CardContent>
            </Card>
          </div>
        </section>

        <!-- Recommendations Section -->
        <section>
          <h2 class="text-xl font-semibold mb-4">Recommendations</h2>
          
          <!-- Recommendation Selection -->
          <div class="space-y-4 mb-6">
            <div>
              <Label>Select Standard Recommendations</Label>
              <Select v-model="selectedRecommendations" multiple>
                <SelectTrigger>
                  <SelectValue placeholder="Select recommendations..." />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    <SelectItem 
                      v-for="(rec, index) in standardRecommendations" 
                      :key="index" 
                      :value="rec"
                    >
                      {{ rec }}
                    </SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
            </div>
            
            <Button 
              type="button" 
              variant="outline" 
              size="sm" 
              @click="addSelectedRecommendations"
            >
              <PlusCircle class="w-4 h-4 mr-2" />
              Add Selected
            </Button>
          </div>
          
          <!-- Custom Recommendations -->
          <div class="space-y-4">
            <div v-for="(rec, index) in evaluation.recommendations" :key="index" class="flex items-start gap-2">
              <Textarea 
                v-model="evaluation.recommendations[index]" 
                placeholder="Enter recommendation..."
                class="flex-1"
              />
              <Button 
                type="button" 
                variant="ghost" 
                size="icon" 
                @click="removeRecommendation(index)"
              >
                <Trash2 class="w-4 h-4 text-destructive" />
              </Button>
            </div>
            
            <Button 
              type="button" 
              variant="outline" 
              @click="addRecommendation"
            >
              <PlusCircle class="w-4 h-4 mr-2" />
              Add Custom Recommendation
            </Button>
          </div>
        </section>

        <!-- Evaluation Summary -->
        <section>
          <Label for="summary-notes">Evaluation Summary</Label>
          <Textarea 
            id="summary-notes" 
            v-model="evaluation.summary_notes" 
            placeholder="Enter your professional evaluation summary..."
            class="min-h-[200px]"
          />
        </section>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-4 pt-4">
          <Button variant="outline" @click="resetForm">Reset</Button>
          <Button @click="submitEvaluation">Submit Evaluation</Button>
        </div>
      </CardContent>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { toast } from 'vue-sonner'
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea/index'
import { Button } from '@/components/ui/button'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { PlusCircle, Trash2 } from 'lucide-vue-next'
import Header from '@/components/Header.vue'

interface Assessment {
  id: string
  child_name: string
  assessment_date: string
  categories: {
    name: string
    age: number
    responses: string[]
    competency: string
  }[]
}

interface Evaluation {
  background_information: string
  recommendations: string[]
  summary_notes: string
}

const route = useRoute()
const assessmentId = route.params.id
const assessment = ref<Assessment>({
  id: '',
  child_name: '',
  assessment_date: '',
  categories: []
})

const evaluation = ref<Evaluation>({
  background_information: '',
  recommendations: [],
  summary_notes: ''
})

const selectedRecommendations = ref<string[]>([])
const standardRecommendations = [
  'Educational/school placement - Special Education Special Class (Minimum of 3 hours, 5 times a week)',
  'Design and implement a whole-year Individualized Educational Program',
  'Create a Team of professionals (SpED Team) to address cognitive and behavioral difficulties',
  'Occupational/Behavioral therapy - minimum of 2 times a week',
  'Speech Therapy - focus on expressive language pragmatics',
  'Parent & Immediate caregiver Direct Involvement in the Special Education program',
  'Family reorientation concerning methodologies for home-based behavior modifications',
  'Increase self-help skills through partnership of auxiliary therapist/services & family intervention',
  'Increase psychosocial exposure like going to the mall and increase interaction with children'
]

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const fetchAssessment = async () => {
  try {
    const response = await api.get(`/assessments/${assessmentId}/results`)
    
    if (!response.data?.data) {
      throw new Error('Invalid response format')
    }
    
    assessment.value = response.data.data
  } catch (error) {
    console.error('Error fetching assessment:', error)
    toast.error('Failed to load assessment data')
  }
}

const addRecommendation = () => {
  evaluation.value.recommendations.push('')
}

const removeRecommendation = (index: number) => {
  evaluation.value.recommendations.splice(index, 1)
}

const addSelectedRecommendations = () => {
  selectedRecommendations.value.forEach(rec => {
    if (!evaluation.value.recommendations.includes(rec)) {
      evaluation.value.recommendations.push(rec)
    }
  })
  selectedRecommendations.value = []
}

const resetForm = () => {
  evaluation.value = {
    background_information: '',
    recommendations: [],
    summary_notes: ''
  }
}

const submitEvaluation = async () => {
  try {
    const payload = {
      assessment_id: assessmentId,
      ...evaluation.value
    }
    
       await api.post(`/assessments/${assessmentId}/evaluations`, payload)
    toast.success('Evaluation submitted successfully')
  } catch (error) {
    console.error('Error submitting evaluation:', error)
    toast.error('Failed to submit evaluation')
  }
}

// Configure axios instance
const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
})

// Add auth interceptor
api.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

onMounted(() => {
  fetchAssessment()
})
</script>