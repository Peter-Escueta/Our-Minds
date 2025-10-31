<template>
  <div class="max-w-6xl mx-auto p-6">
    <Card v-if="results">
      <CardHeader>
        <CardTitle class="text-2xl font-bold">
          Assessment Results for {{ results.child_name }}
        </CardTitle>
        <CardDescription>
          Assessment Date: {{ formatDate(results.assessment_date) }}
          <span v-if="results.assessed_ages" class="ml-2">
            • Ages Assessed: {{ results.assessed_ages.join(', ') }}
          </span>
        </CardDescription>
      </CardHeader>

      <CardContent>
        <Tabs v-model="activeTab" class="w-full">
          <TabsList class="grid w-full grid-cols-3">
            <TabsTrigger value="detailed">Detailed Results</TabsTrigger>
            <TabsTrigger value="summary">Summary View</TabsTrigger>
            <TabsTrigger value="byAge">View by Age</TabsTrigger>
          </TabsList>

          <!-- Detailed Results Tab -->
          <TabsContent value="detailed">
            <div class="space-y-6">
              <div
                v-for="categoryGroup in getGroupedCategories(results.categories)"
                :key="categoryGroup.name"
              >
                <Card class="p-6">
                  <CardHeader class="p-0 pb-4">
                    <div class="flex justify-between items-start">
                      <CardTitle class="text-xl font-semibold text-primary">
                        {{ categoryGroup.name.toUpperCase() }}
                      </CardTitle>
                      <div class="flex gap-2">
                        <Badge v-for="age in categoryGroup.ages" :key="age" variant="outline">
                          Age {{ age }}
                        </Badge>
                      </div>
                    </div>
                  </CardHeader>

                  <CardContent class="p-0 mt-4">
                    <div v-for="age in categoryGroup.ages" :key="age" class="mb-6 last:mb-0">
                      <div class="flex items-center gap-2 mb-3">
                        <h4 class="font-semibold text-lg">Age {{ age }}</h4>
                        <Badge variant="secondary">
                          {{ getAgeResults(categoryGroup.results, age).length }} skills assessed
                        </Badge>
                      </div>

                      <ul class="space-y-2">
                        <li
                          v-for="(response, index) in getAgeResults(categoryGroup.results, age)"
                          :key="index"
                          class="flex items-start"
                        >
                          <span
                            class="mr-2 mt-1 h-2 w-2 rounded-full"
                            :class="getResponseColor(response)"
                          />
                          <span>{{ response }}</span>
                        </li>
                      </ul>

                      <div class="mt-3 p-3 bg-muted rounded-lg">
                        <div class="flex items-center gap-4">
                          <Progress
                            :value="calculateProgressForAge(categoryGroup.results, age)"
                            :class="getProgressColorForAge(categoryGroup.results, age)"
                            class="flex-1"
                          />
                          <span class="text-sm font-medium whitespace-nowrap">
                            {{ calculateProgressForAge(categoryGroup.results, age) }}%
                          </span>
                        </div>
                        <p class="text-sm text-muted-foreground italic mt-2">
                          {{ getCompetencyForAge(categoryGroup.results, age) }}
                        </p>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </div>
            </div>
          </TabsContent>

          <!-- Summary View Tab -->
          <TabsContent value="summary">
            <div class="space-y-4">
              <Card
                v-for="categoryGroup in getGroupedCategories(results.categories)"
                :key="'summary-' + categoryGroup.name"
                class="p-6"
              >
                <CardHeader class="p-0 pb-4">
                  <div class="flex justify-between items-center">
                    <CardTitle class="text-lg font-semibold">
                      {{ categoryGroup.name }}
                    </CardTitle>
                    <div class="flex gap-2">
                      <Badge v-for="age in categoryGroup.ages" :key="age" variant="secondary">
                        Age {{ age }}
                      </Badge>
                    </div>
                  </div>
                </CardHeader>
                <CardContent class="p-0">
                  <div class="space-y-4">
                    <div
                      v-for="age in categoryGroup.ages"
                      :key="age"
                      class="flex items-center gap-4"
                    >
                      <div class="w-16 text-sm font-medium whitespace-nowrap">Age {{ age }}:</div>
                      <div class="flex-1 space-y-1">
                        <Progress
                          :value="calculateProgressForAge(categoryGroup.results, age)"
                          :class="getProgressColorForAge(categoryGroup.results, age)"
                        />
                        <p class="text-sm text-muted-foreground">
                          {{ calculateProgressTextForAge(categoryGroup.results, age) }}
                        </p>
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          <!-- View by Age Tab -->
          <TabsContent value="byAge">
            <div class="space-y-6">
              <div v-for="age in getUniqueAges(results.categories)" :key="age">
                <h3 class="text-lg font-semibold mb-4 text-primary">Age {{ age }} Assessment</h3>
                <div class="grid gap-4 md:grid-cols-2">
                  <Card
                    v-for="category in getCategoriesByAge(results.categories, age)"
                    :key="`${age}-${category.name}`"
                    class="p-4"
                  >
                    <CardHeader class="p-0 pb-3">
                      <CardTitle class="text-md font-semibold">
                        {{ category.name }}
                      </CardTitle>
                    </CardHeader>
                    <CardContent class="p-0">
                      <div class="space-y-2">
                        <div class="flex justify-between items-center">
                          <span class="text-sm text-muted-foreground">Progress:</span>
                          <span class="text-sm font-medium">
                            {{ calculateProgress(category) }}%
                          </span>
                        </div>
                        <Progress
                          :value="calculateProgress(category)"
                          :class="getProgressColor(category)"
                        />
                        <p class="text-xs text-muted-foreground italic mt-2">
                          {{ category.competency }}
                        </p>
                      </div>
                    </CardContent>
                  </Card>
                </div>
              </div>
            </div>
          </TabsContent>
        </Tabs>
      </CardContent>
    </Card>

    <Card v-else-if="isLoading">
      <CardHeader>
        <CardTitle>Loading Assessment Results</CardTitle>
      </CardHeader>
      <CardContent class="flex justify-center">
        <Loader2 class="h-8 w-8 animate-spin" />
      </CardContent>
    </Card>

    <Card v-else>
      <CardHeader>
        <CardTitle>No Results Found</CardTitle>
      </CardHeader>
      <CardContent>
        <p class="text-muted-foreground">Unable to load assessment results.</p>
      </CardContent>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Progress } from '@/components/ui/progress'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Badge } from '@/components/ui/badge'
import { Loader2 } from 'lucide-vue-next'
import { useApi } from '@/composables/useApi'
import type { AssessmentResult } from '@/types'
import { formatDate } from '@/utils/date'

const { api, handleApiError } = useApi()
const route = useRoute()
const assessmentId = route.params.id
const results = ref<AssessmentResult | null>(null)
const activeTab = ref('detailed')
const isLoading = ref(true)

const API_ASSESSMENT_ENDPOINT = import.meta.env.VITE_API_ASSESSMENT_ENDPOINT

const getGroupedCategories = (categories: any[]) => {
  const grouped: Record<string, { name: string; ages: number[]; results: any[] }> = {}

  categories.forEach((category) => {
    if (!grouped[category.name]) {
      grouped[category.name] = {
        name: category.name,
        ages: [],
        results: [],
      }
    }

    if (category.age && !grouped[category.name].ages.includes(category.age)) {
      grouped[category.name].ages.push(category.age)
    }

    grouped[category.name].results.push(category)
  })

  Object.values(grouped).forEach((group) => {
    group.ages.sort((a, b) => a - b)
  })

  return Object.values(grouped)
}

const getResponseColor = (response: string) => {
  if (response.startsWith('can')) return 'bg-green-500'
  if (response.startsWith('cannot')) return 'bg-red-500'
  if (response.startsWith('emerging')) return 'bg-amber-500'
  return 'bg-gray-500'
}

const getAgeResults = (results: any[], age: number) => {
  const ageResult = results.find((r) => r.age === age)
  return ageResult ? ageResult.responses : []
}

const calculateProgress = (category: any) => {
  const positiveCount = category.responses.filter(
    (r: string) => r.startsWith('can') || r.startsWith('emerging'),
  ).length
  return Math.round((positiveCount / category.responses.length) * 100)
}

const calculateProgressForAge = (results: any[], age: number) => {
  const responses = getAgeResults(results, age)
  if (responses.length === 0) return 0

  const positiveCount = responses.filter(
    (r: string) => r.startsWith('can') || r.startsWith('emerging'),
  ).length
  return Math.round((positiveCount / responses.length) * 100)
}

const calculateProgressTextForAge = (results: any[], age: number) => {
  const responses = getAgeResults(results, age)
  if (responses.length === 0) return 'No data'

  const positiveCount = responses.filter(
    (r: string) => r.startsWith('can') || r.startsWith('emerging'),
  ).length
  return `${positiveCount} of ${responses.length} skills demonstrated`
}

const getProgressColor = (category: any) => {
  const progress = calculateProgress(category)
  if (progress >= 75) return 'bg-green-500'
  if (progress >= 50) return 'bg-amber-500'
  return 'bg-red-500'
}

const getProgressColorForAge = (results: any[], age: number) => {
  const progress = calculateProgressForAge(results, age)
  if (progress >= 75) return 'bg-green-500'
  if (progress >= 50) return 'bg-amber-500'
  return 'bg-red-500'
}

const getCompetencyForAge = (results: any[], age: number) => {
  const ageResult = results.find((r) => r.age === age)
  return ageResult ? ageResult.competency : 'No assessment data'
}

const getUniqueAges = (categories: any[]) => {
  const ages = categories.map((cat) => cat.age).filter((age) => age != null)
  return [...new Set(ages)].sort((a, b) => a - b)
}

const getCategoriesByAge = (categories: any[], age: number) => {
  return categories.filter((cat) => cat.age === age)
}

const fetchResults = async () => {
  try {
    isLoading.value = true
    const response = await api.get(`${API_ASSESSMENT_ENDPOINT}/${assessmentId}/results`)

    if (!response.data?.data) {
      throw new Error('Invalid response format')
    }

    results.value = response.data.data
  } catch (error) {
    handleApiError(error, 'Failed to load assessment data')
    throw error
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchResults()
})
</script>
