<script setup lang="ts">
import { onMounted } from 'vue'
import { toast } from 'vue-sonner'
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
import { useAssessmentData } from '@/composables/useAssessmentData'
import { useAssessmentForm } from '@/composables/useAssessmentForm'

const props = defineProps<{
  id: string
}>()

const { categories, questions, isLoading, fetchData } = useAssessmentData()
const {
  selectedAges,
  formResponses,
  availableAges,
  initializeAges,
  getQuestionsForCategory,
  handleAgeChange,
  submitAssessment,
  hasResponses,
} = useAssessmentForm(props.id)

const handleFormSubmit = async () => {
  const success = await submitAssessment()
  if (success) {
    toast.success('Form submitted successfully!')
  }
}

onMounted(async () => {
  await fetchData()
  initializeAges(categories.value.map((cat) => cat.id))
})
</script>

<template>
  <div class="min-h-screen">
    <div class="hidden">bg-green-700 bg-red-700 bg-blue-700 bg-yellow-700 bg-purple-700</div>

    <h1 class="text-center text-2xl text-primary font-bold mb-5">
      Child Development Assessment Form
    </h1>

    <div v-if="isLoading" class="flex justify-center py-8">
      <Loader2 class="h-8 w-8 animate-spin" />
      <span class="ml-2">Loading assessment form...</span>
    </div>

    <div v-else class="space-y-8 px-20">
      <Card v-for="category in categories" :key="category.id" class="py-0 gap-0 text-center">
        <CardHeader class="pb-0 mb-0 p-4 border-b-0 rounded-t-lg" :class="category.color">
          <div class="grid grid-cols-12 items-center">
            <div class="col-span-12">
              <CardTitle class="font-bold text-white">{{ category.name }} </CardTitle>
              <div class="flex justify-end items-center space-x-2 text-white">
                <Label class="whitespace-nowrap">Age:</Label>
                <Select
                  :modelValue="selectedAges[category.id]"
                  @update:modelValue="
                    (value) => handleAgeChange(category.id, Number(value), questions)
                  "
                >
                  <SelectTrigger class="w-[100px] bg-white/20">
                    <SelectValue placeholder="Select age" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem v-for="age in availableAges" :key="age" :value="age">
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
            v-if="getQuestionsForCategory(questions, category.id).length === 0"
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
                v-for="question in getQuestionsForCategory(questions, category.id)"
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
        @click="handleFormSubmit"
        class="px-4 py-2 bg-primary text-primary-foreground rounded hover:bg-primary/90"
        :disabled="!hasResponses"
      >
        Submit Assessment
      </Button>
    </div>
  </div>
</template>
