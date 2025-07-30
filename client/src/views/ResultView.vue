<template>
  <Header />
  <div class="max-w-4xl mx-auto p-6">

    <Card v-if="results">
      <CardHeader>
        <CardTitle class="text-2xl font-bold">
          Assessment Results for {{ results.child_name }}
        </CardTitle>
        <CardDescription>
          Assessment Date: {{ formatDate(results.assessment_date) }}
        </CardDescription>
      </CardHeader>

      <CardContent>
        <Tabs v-model="activeTab" class="w-full">
          <TabsList class="grid w-full grid-cols-2">
            <TabsTrigger value="detailed">Detailed Results</TabsTrigger>
            <TabsTrigger value="summary">Summary View</TabsTrigger>
          </TabsList>

          <TabsContent value="detailed">
            <div class="space-y-6">
              <Card v-for="category in results.categories" :key="category.name" class="p-6">
                <CardHeader class="p-0 pb-4">
                  <CardTitle class="text-xl font-semibold text-primary">
                    {{ category.name.toUpperCase() }}
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
                    Their current {{ category.name.toLowerCase() }} skills are 
                    <span 
                      :class="category.competency.includes('within') ? 'text-green-600' : 'text-amber-600'"
                      class="font-medium"
                    >
                      {{ category.competency }}
                    </span>.
                  </p>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          <TabsContent value="summary">
            <!-- Summary view content here -->
            <div class="space-y-4">
              <Card v-for="category in results.categories" :key="'summary-' + category.name" class="p-6">
                <CardHeader class="p-0 pb-4">
                  <CardTitle class="text-lg font-semibold">
                    {{ category.name }}
                  </CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                  <div class="flex items-center gap-4">
                    <div class="w-full space-y-1">
                      <Progress 
                        :value="(
                          category.responses.filter(r => r.startsWith('can')).length / 
                          category.responses.length * 100
                        )" 
                        :class="
                          (category.responses.filter(r => r.startsWith('can')).length / 
                          category.responses.length >= 0.5 
                            ? 'bg-green-500' 
                            : 'bg-amber-500'
                        )"
                      />
                      <p class="text-sm text-muted-foreground">
                        {{ Math.round(
                          (category.responses.filter(r => r.startsWith('can')).length / 
                          category.responses.length * 100
                         ) ) }}% positive responses
                      </p>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>
        </Tabs>
      </CardContent>
    </Card>

    <Card v-else>
      <CardHeader>
        <CardTitle>Loading Assessment Results</CardTitle>
      </CardHeader>
      <CardContent class="flex justify-center">
        <Loader2 class="h-8 w-8 animate-spin" />
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
import { Progress } from '@/components/ui/progress'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Loader2 } from 'lucide-vue-next'
import Header from '@/components/Header.vue'

interface AssessmentResult {
  child_name: string
  assessment_date: string
  categories: {
    name: string
    responses: string[]
    competency: string
  }[]
}

const route = useRoute()
const assessmentId = route.params.id
const results = ref<AssessmentResult | null>(null)
const activeTab = ref('detailed')
const isLoading = ref(true)

// Configure axios instance
const api = axios.create({
  baseURL: 'http://localhost:8000/api', // Note the /api prefix
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

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const fetchResults = async () => {
  try {
    isLoading.value = true
    console.log('Fetching results for assessment ID:', assessmentId) // Debug log
    
    const response = await api.get(`/assessments/${assessmentId}/results`)
    console.log('API Response:', response.data) // Debug log
    
    if (!response.data?.data) {
      throw new Error('Invalid response format')
    }
    
    results.value = response.data.data
  } catch (error) {
    console.error('API Error Details:', error)
    toast.error('Failed to load assessment results')
    
    // More detailed error message
    if (error.response) {
      console.error('Response data:', error.response.data)
      console.error('Status code:', error.response.status)
    }
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  console.log('Component mounted, starting data fetch') // Debug log
  fetchResults()
})
</script>