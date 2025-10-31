<template>
  <div class="max-w-6xl mx-auto p-6">
    <Card>
      <CardHeader>
        <CardTitle class="text-2xl font-bold">
          Evaluation for {{ evaluation.assessment.child_name }}
          <EvaluationButton :evaluation-id="evaluation.id" />
        </CardTitle>
        <CardDescription>
          Evaluated on: {{ formatDate(evaluation.created_at) }}
          <span v-if="evaluation.assessment.assessed_ages" class="ml-2">
            • Ages Assessed: {{ evaluation.assessment.assessed_ages.join(', ') }}
          </span>
        </CardDescription>
      </CardHeader>

      <CardContent class="space-y-8">
        <section>
          <h2 class="text-xl font-semibold mb-4">Background Information</h2>
          <div class="bg-gray-50 p-4 rounded-lg">
            <p class="whitespace-pre-line">{{ evaluation.background_information }}</p>
          </div>
        </section>

        <section>
          <h2 class="text-xl font-semibold mb-4">Assessment Results</h2>
          <div class="space-y-6">
            <div v-for="categoryGroup in getGroupedCategories" :key="categoryGroup.name">
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
                  <div
                    v-for="ageData in categoryGroup.ageResults"
                    :key="ageData.age"
                    class="mb-6 last:mb-0 p-4 bg-muted/50 rounded-lg"
                  >
                    <div class="flex items-center gap-2 mb-3">
                      <h4 class="font-semibold text-lg">Age {{ ageData.age }}</h4>
                      <Badge variant="secondary">
                        {{ ageData.responses.length }} skills assessed
                      </Badge>
                    </div>
                    <ul class="space-y-2">
                      <li
                        v-for="(response, index) in ageData.responses"
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
                    <p class="mt-3 text-sm text-muted-foreground italic">
                      {{ ageData.competency }}
                    </p>
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>
        </section>

        <section>
          <h2 class="text-xl font-semibold mb-4">Recommendations</h2>
          <div class="space-y-3">
            <div
              v-for="(recommendation, index) in evaluation.recommendations"
              :key="index"
              class="flex items-start gap-3"
            >
              <span class="flex-shrink-0 mt-1 h-2 w-2 rounded-full bg-primary" />
              <p class="flex-1">{{ recommendation }}</p>
            </div>
          </div>
        </section>

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
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { formatDate } from '@/utils/date'
import EvaluationButton from '@/components/EvaluationButton.vue'
import type { Evaluation } from '@/types'
import { useApi } from '@/composables/useApi'

const route = useRoute()
const evaluationId = route.params.id
const API_EVALUATION_ENDPOINT = import.meta.env.VITE_API_EVALUATION_ENDPOINT
const { api, handleApiError } = useApi()

const evaluation = ref<Evaluation>({
  id: '',
  created_at: '',
  background_information: '',
  recommendations: [],
  summary_notes: '',
  assessment: {
    child_name: '',
    categories: [],
    assessed_ages: [],
  },
})

const getGroupedCategories = computed(() => {
  if (!evaluation.value.assessment.categories) return []

  const grouped: Record<
    string,
    {
      name: string
      ages: number[]
      ageResults: any[]
    }
  > = {}

  evaluation.value.assessment.categories.forEach((category) => {
    if (!grouped[category.name]) {
      grouped[category.name] = {
        name: category.name,
        ages: [],
        ageResults: [],
      }
    }

    grouped[category.name].ages.push(category.age)
    grouped[category.name].ageResults.push({
      age: category.age,
      responses: category.responses,
      competency: category.competency,
    })
  })

  Object.values(grouped).forEach((group) => {
    group.ages.sort((a, b) => a - b)
    group.ageResults.sort((a, b) => a.age - b.age)
  })

  return Object.values(grouped)
})

const getResponseColor = (response: string) => {
  if (response.startsWith('can')) return 'bg-green-500'
  if (response.startsWith('cannot')) return 'bg-red-500'
  if (response.startsWith('emerging')) return 'bg-amber-500'
  return 'bg-gray-500'
}

const fetchEvaluation = async () => {
  try {
    const response = await api.get(`${API_EVALUATION_ENDPOINT}/${evaluationId}`)

    if (!response.data?.data) {
      throw new Error('Invalid response format')
    }

    evaluation.value = response.data.data
  } catch (error) {
    handleApiError(error, 'Failed to load evaluation data')
    throw error
  }
}

onMounted(fetchEvaluation)
</script>
