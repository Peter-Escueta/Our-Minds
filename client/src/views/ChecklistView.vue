<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { toast } from 'vue-sonner'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Label } from '@/components/ui/label'
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
import { Button } from '@/components/ui/button'

import { Loader2 } from 'lucide-vue-next'
import { useAssessmentData } from '@/composables/useAssessmentData'
import { useAssessmentForm } from '@/composables/useAssessmentForm'

import {
  Combobox,
  ComboboxAnchor,
  ComboboxEmpty,
  ComboboxGroup,
  ComboboxInput,
  ComboboxItem,
  ComboboxList,
} from '@/components/ui/combobox'
import {
  TagsInput,
  TagsInputInput,
  TagsInputItem,
  TagsInputItemDelete,
  TagsInputItemText,
} from '@/components/ui/tags-input'
import { useFilter } from 'reka-ui'

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
  handleAgeRemove,
  submitAssessment,
  hasResponses,
} = useAssessmentForm(props.id)

const openStates = ref<Record<number, boolean>>({})
const searchTerms = ref<Record<number, string>>({})

const { contains } = useFilter({ sensitivity: 'base' })

const filteredAges = computed(() => {
  const result: Record<number, number[]> = {}

  categories.value.forEach((category) => {
    const categoryId = category.id
    const currentSelected = selectedAges.value[categoryId] || []
    const searchTerm = searchTerms.value[categoryId] || ''

    const availableOptions = availableAges.filter((age) => !currentSelected.includes(age))

    if (searchTerm) {
      result[categoryId] = availableOptions.filter((age) => contains(age.toString(), searchTerm))
    } else {
      result[categoryId] = availableOptions
    }
  })

  return result
})

const initializeComboboxStates = () => {
  categories.value.forEach((category) => {
    const categoryId = category.id
    if (!openStates.value[categoryId]) {
      openStates.value[categoryId] = false
    }
    if (!searchTerms.value[categoryId]) {
      searchTerms.value[categoryId] = ''
    }
  })
}

const handleAgeSelect = (categoryId: number, age: number) => {
  handleAgeChange(categoryId, age)
  searchTerms.value[categoryId] = ''
}

const handleFormSubmit = async () => {
  const success = await submitAssessment()
  if (success) {
    toast.success('Form submitted successfully!')
  }
}

onMounted(async () => {
  await fetchData()
  initializeAges(categories.value.map((cat) => cat.id))
  initializeComboboxStates()
})
</script>

<template>
  <div class="min-h-screen">
    <div class="hidden">bg-green-700 bg-red-700 bg-blue-700 bg-yellow-700 bg-purple-700</div>

    <h1 class="text-center text-4xl text-primary font-bold mb-5">
      Child Development Assessment Form
    </h1>

    <div v-if="isLoading" class="flex justify-center py-8">
      <Loader2 class="h-8 w-8 animate-spin" />
      <span class="ml-2">Loading assessment form...</span>
    </div>

    <div v-else class="space-y-8 px-20">
      <Card v-for="category in categories" :key="category.id" class="py-0 gap-0 text-center">
        <CardHeader class="pb-0 mb-0 p-4 border-b-0 rounded-t-lg text-2xl" :class="category.color">
          <div class="grid grid-cols-12 items-center">
            <div class="col-span-12">
              <CardTitle class="font-bold text-white">{{ category.name }} </CardTitle>

              <!-- Multiple Age Selection with Combobox -->
              <div class="flex flex-wrap justify-end items-center gap-2 mt-2">
                <!-- Age Combobox -->
                <div class="flex items-center space-x-2 text-white">
                  <Label class="whitespace-nowrap text-sm">Select Ages:</Label>

                  <Combobox v-model:open="openStates[category.id]" :ignore-filter="true">
                    <ComboboxAnchor as-child>
                      <TagsInput
                        :modelValue="selectedAges[category.id] || []"
                        class="px-2 gap-2 w-80 bg-white/20 border-white/30"
                      >
                        <div class="flex gap-2 flex-wrap items-center py-1">
                          <TagsInputItem
                            v-for="age in selectedAges[category.id] || []"
                            :key="age"
                            :value="age"
                            class="bg-white/30 text-white border-0"
                          >
                            <TagsInputItemText class="text-white" />
                            <TagsInputItemDelete
                              @click="handleAgeRemove(category.id, age)"
                              class="hover:bg-white/20 text-white"
                            />
                          </TagsInputItem>
                        </div>

                        <ComboboxInput v-model="searchTerms[category.id]" as-child>
                          <TagsInputInput
                            placeholder="Add age..."
                            class="min-w-[100px] w-full p-0 border-none focus-visible:ring-0 h-auto bg-transparent text-white placeholder:text-white/70"
                            @keydown.enter.prevent
                          />
                        </ComboboxInput>
                      </TagsInput>

                      <ComboboxList class="w-[--reka-popper-anchor-width]">
                        <ComboboxEmpty>No ages found</ComboboxEmpty>
                        <ComboboxGroup>
                          <ComboboxItem
                            v-for="age in filteredAges[category.id]"
                            :key="age"
                            :value="age.toString()"
                            @select.prevent="
                              (ev) => {
                                if (typeof ev.detail.value === 'string') {
                                  handleAgeSelect(category.id, Number(ev.detail.value))
                                }

                                if (filteredAges[category.id].length === 0) {
                                  openStates[category.id] = false
                                }
                              }
                            "
                          >
                            {{ age }} year old
                          </ComboboxItem>
                        </ComboboxGroup>
                      </ComboboxList>
                    </ComboboxAnchor>
                  </Combobox>
                </div>
              </div>
            </div>
          </div>
        </CardHeader>

        <CardContent class="p-0">
          <!-- Show message when no ages selected -->
          <div v-if="!selectedAges[category.id]?.length" class="text-foreground p-4">
            Please select at least one age to view questions
          </div>

          <!-- Show questions for all selected ages -->
          <div v-else>
            <div
              v-for="age in selectedAges[category.id]"
              :key="age"
              class="border-b last:border-b-0"
            >
              <!-- Age Header -->
              <div class="bg-gray-100 p-3 border-b">
                <h3 class="font-semibold text-lg text-gray-700">{{ age }} year old</h3>
              </div>

              <!-- Questions for this age -->
              <div
                v-if="getQuestionsForCategory(questions, category.id, age).length === 0"
                class="text-foreground p-4 text-sm"
              >
                No questions available for {{ age }} year old
              </div>

              <div v-else class="border rounded-b-lg overflow-hidden">
                <div class="grid grid-cols-12 bg-gray-50 p-4 font-bold border-b">
                  <div class="col-span-1 text-center text-green-600">CAN</div>
                  <div class="col-span-10 text-center text-foreground">QUESTION</div>
                  <div class="col-span-1 text-center text-red-600">CANNOT</div>
                </div>

                <div
                  v-for="question in getQuestionsForCategory(questions, category.id, age)"
                  :key="`${question.id}-${age}`"
                  class="grid grid-cols-12 p-4 border-t text-xl hover:bg-gray-50"
                >
                  <div class="col-span-1 flex justify-center">
                    <RadioGroup v-model="formResponses[`${category.id}-${question.id}-${age}`]">
                      <RadioGroupItem value="can" :id="`${question.id}-${age}-can`" />
                    </RadioGroup>
                  </div>

                  <div class="col-span-10 flex items-center justify-center">
                    <Label :for="`${question.id}-${age}-can`" class="cursor-pointer ml-4">
                      {{ question.text }}
                    </Label>
                  </div>

                  <div class="col-span-1 flex justify-center">
                    <RadioGroup v-model="formResponses[`${category.id}-${question.id}-${age}`]">
                      <RadioGroupItem value="cannot" :id="`${question.id}-${age}-cannot`" />
                    </RadioGroup>
                  </div>
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
