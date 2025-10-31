<template>
  <div class="max-w-6xl mx-auto p-6">
    <Card v-if="assessment">
      <CardHeader>
        <CardTitle class="text-2xl font-bold">
          Assessment Evaluation for {{ assessment.child_name || 'Child' }}
        </CardTitle>
        <CardDescription>
          Assessment Date: {{ formatDate(assessment.assessment_date) }}
          <span v-if="assessment.assessed_ages" class="ml-2">
            • Ages Assessed: {{ assessment.assessed_ages.join(', ') }}
          </span>
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
            <!-- Group categories by name to avoid duplication -->
            <Card
              v-for="categoryGroup in getGroupedCategories"
              :key="categoryGroup.name"
              class="p-6"
            >
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
                <!-- Show results for each age within this category -->
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

            <Button type="button" variant="outline" size="sm" @click="addSelectedRecommendations">
              <PlusCircle class="w-4 h-4 mr-2" />
              Add Selected
            </Button>
          </div>

          <!-- Custom Recommendations -->
          <div class="space-y-4">
            <div
              v-for="(rec, index) in evaluation.recommendations"
              :key="index"
              class="flex items-start gap-2"
            >
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

            <Button type="button" variant="outline" @click="addRecommendation">
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
          <Button @click="handleSubmitEvaluation" :disabled="isLoading">
            {{ isLoading ? 'Submitting...' : 'Submit Evaluation' }}
          </Button>
        </div>
      </CardContent>
    </Card>

    <!-- Loading State -->
    <Card v-else-if="isLoading">
      <CardHeader>
        <CardTitle>Loading Assessment...</CardTitle>
      </CardHeader>
      <CardContent class="flex justify-center">
        <Loader2 class="h-8 w-8 animate-spin" />
      </CardContent>
    </Card>

    <!-- Error State -->
    <Card v-else>
      <CardHeader>
        <CardTitle>Assessment Not Found</CardTitle>
      </CardHeader>
      <CardContent>
        <p class="text-muted-foreground">Unable to load assessment data.</p>
      </CardContent>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea/index'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { PlusCircle, Trash2, Loader2 } from 'lucide-vue-next'
import type { Evaluation } from '@/types'
import { formatDate } from '@/utils/date'
import { useEvaluationForm } from '@/composables/useEvaluationForm'
import { standardRecommendations } from '@/data/evaluationRecommendations'
import { toast } from 'vue-sonner'

const route = useRoute()
const assessmentId = route.params.id as string

const { assessment, isLoading, fetchAssessment, submitEvaluation } = useEvaluationForm()
const evaluation = ref<Evaluation>({
  background_information: '',
  recommendations: [],
  summary_notes: '',
})

const selectedRecommendations = ref<string[]>([])

const getGroupedCategories = computed(() => {
  if (!assessment.value?.categories) return []

  const grouped: Record<
    string,
    {
      name: string
      ages: number[]
      ageResults: any[]
    }
  > = {}

  assessment.value.categories.forEach((category) => {
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

const addRecommendation = () => {
  evaluation.value.recommendations.push('')
}

const removeRecommendation = (index: number) => {
  evaluation.value.recommendations.splice(index, 1)
}

const addSelectedRecommendations = () => {
  selectedRecommendations.value.forEach((rec) => {
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
    summary_notes: '',
  }
}

const handleSubmitEvaluation = async () => {
  try {
    const filteredRecommendations = evaluation.value.recommendations.filter(
      (rec) => rec.trim() !== '',
    )

    if (filteredRecommendations.length === 0) {
      toast.error('Please add at least one recommendation')
      return
    }

    if (!evaluation.value.background_information.trim()) {
      toast.error('Please provide background information')
      return
    }

    if (!evaluation.value.summary_notes.trim()) {
      toast.error('Please provide an evaluation summary')
      return
    }

    const success = await submitEvaluation({
      ...evaluation.value,
      recommendations: filteredRecommendations,
    })

    if (success) {
      toast.success('Evaluation submitted successfully!')
      resetForm()
    }
  } catch (error) {
    toast.error('Failed to submit evaluation')
    console.error('Evaluation submission error:', error)
  }
}

onMounted(() => {
  fetchAssessment(assessmentId)
})
</script>
