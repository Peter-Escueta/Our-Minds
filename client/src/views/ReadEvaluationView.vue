<template>
  <Header />
  <div class="max-w-4xl mx-auto p-6">
    <Card>
      <CardHeader>
        <CardTitle class="text-2xl font-bold">
          Evaluation for {{ evaluation.assessment.child_name }}
          <EvaluationButton :evaluation-id="evaluation.id" />
        </CardTitle>
        <CardDescription>
          Evaluated on: {{ formatDate(evaluation.created_at) }}
        </CardDescription>
      </CardHeader>

      <CardContent class="space-y-8">
        <section>
          <h2 class="text-xl font-semibold mb-4">Background Information</h2>
          <div class="bg-gray-50 p-4 rounded-lg">
            <p class="whitespace-pre-line">{{ evaluation.background_information }}</p>
          </div>
        </section>

        <!-- Developmental Areas Section -->
        <section>
          <h2 class="text-xl font-semibold mb-4">Assessment Results</h2>
          <div class="space-y-6">
            <Card v-for="category in evaluation.assessment.categories" :key="category.name" class="p-6">
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
          <div class="space-y-3">
            <div v-for="(recommendation, index) in evaluation.recommendations" :key="index" class="flex items-start gap-3">
              <span class="flex-shrink-0 mt-1 h-2 w-2 rounded-full bg-primary" />
              <p class="flex-1">{{ recommendation }}</p>
            </div>
          </div>
        </section>

        <!-- Evaluation Summary -->
        <section>
          <h2 class="text-xl font-semibold mb-4">Evaluation Summary</h2>
          <div class="bg-gray-50 p-4 rounded-lg">
            <p class="whitespace-pre-line">{{ evaluation.summary_notes }}</p>
          </div>
        </section>
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
import Header from '@/components/Header.vue'
import EvaluationButton from '@/components/EvaluationButton.vue'

interface Evaluation {
  id: string
  created_at: string
  background_information: string
  recommendations: string[]
  summary_notes: string
  assessment: {
    child_name: string
    categories: {
      name: string
      age: number
      responses: string[]
      competency: string
    }[]
  }
}

const route = useRoute()
const evaluationId = route.params.id
const evaluation = ref<Evaluation>({
  id: '',
  created_at: '',
  background_information: '',
  recommendations: [],
  summary_notes: '',
  assessment: {
    child_name: '',
    categories: []
  }
})

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
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

const fetchEvaluation = async () => {
  try {
    const response = await api.get(`/evaluations/${evaluationId}`)
    
    if (!response.data?.data) {
      throw new Error('Invalid response format')
    }
    
    evaluation.value = response.data.data
  } catch (error) {
    console.error('Error fetching evaluation:', error)
    toast.error('Failed to load evaluation data')
  }
}

onMounted(() => {
  fetchEvaluation()
})
</script>