<script setup lang="ts">
import { ref, onMounted } from 'vue'
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
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Plus, Trash2, Pencil } from 'lucide-vue-next'

type Question = {
  id: string
  text: string
  age: number
}

type SkillCategory = {
  id: string
  name: string
  color?: string
  questions: Question[]
}

const categories: SkillCategory[] = [
  {
    id: 'psychosocial',
    name: 'Psychosocial Skills',
    color: 'bg-red-700',
    questions: []
  },
  {
    id: 'language',
    name: 'Language Skills',
    color: 'bg-blue-700',
    questions: []
  },
  {
    id: 'fine-motor',
    name: 'Fine Motor Skills',
    color: 'bg-green-700',
    questions: []
  },
  {
    id: 'cognitive',
    name: 'Cognitive Skills',
    color: 'bg-yellow-700',
    questions: []
  },
  {
    id: 'gross-motor',
    name: 'Gross Motor Skills',
    color: 'bg-purple-700',
    questions: []
  }
]

const availableAges = Array.from({ length: 12 }, (_, i) => i + 1) // Ages 1-12

const selectedCategory = ref('psychosocial')
const selectedAge = ref(1)
const newQuestionText = ref('')
const editingQuestionId = ref<string | null>(null)

// Initialize with some sample questions (in a real app, you'd load from an API)
onMounted(() => {
  categories.forEach(category => {
    category.questions = [
      { id: `${category.id}-sample-1`, text: `Sample question for age 3`, age: 3 },
      { id: `${category.id}-sample-2`, text: `Sample question for age 5`, age: 5 }
    ]
  })
})

const getQuestionsForCategoryAndAge = (categoryId: string, age: number) => {
  const category = categories.find(c => c.id === categoryId)
  return category?.questions.filter(q => q.age === age) || []
}

const addQuestion = () => {
  if (!newQuestionText.value.trim()) return

  const category = categories.find(c => c.id === selectedCategory.value)
  if (!category) return

  if (editingQuestionId.value) {
    // Edit existing question
    const question = category.questions.find(q => q.id === editingQuestionId.value)
    if (question) {
      question.text = newQuestionText.value
      question.age = selectedAge.value
    }
    editingQuestionId.value = null
  } else {
    // Add new question
    const newId = `${selectedCategory.value}-${Date.now()}`
    category.questions.push({
      id: newId,
      text: newQuestionText.value,
      age: selectedAge.value
    })
  }

  newQuestionText.value = ''
}

const editQuestion = (question: Question) => {
  editingQuestionId.value = question.id
  newQuestionText.value = question.text
  selectedAge.value = question.age
}

const deleteQuestion = (categoryId: string, questionId: string) => {
  const category = categories.find(c => c.id === categoryId)
  if (category) {
    category.questions = category.questions.filter(q => q.id !== questionId)
  }
}

const cancelEdit = () => {
  editingQuestionId.value = null
  newQuestionText.value = ''
}
</script>

<template>
  <div class="min-h-screen">
    <!-- Header (same as assessment form) -->
    <header class="bg-white shadow-sm sticky top-0 z-10">
      <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex items-center space-x-2">
          <div class="text-2xl font-bold text-primary">UNAWA</div>
          <div class="text-sm text-secondary">Question Management</div>
        </div>
        
        <!-- User dropdown would go here -->
      </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto font-display py-8">
      <h1 class="text-center text-2xl text-primary font-bold mb-8">Manage Assessment Questions</h1>

      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Left sidebar - Category selection -->
        <div class="lg:col-span-1 space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Categories</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="space-y-2">
                <Button
                  v-for="category in categories"
                  :key="category.id"
                  variant="ghost"
                  class="w-full justify-start"
                  :class="{ 'bg-accent': selectedCategory === category.id }"
                  @click="selectedCategory = category.id"
                >
                  {{ category.name }}
                </Button>
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
                        <SelectItem v-for="age in availableAges" :key="age" :value="age">
                          Age {{ age }}
                        </SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                  <div>
                    <Label for="category">Skill Category</Label>
                    <Select v-model="selectedCategory" disabled>
                      <SelectTrigger>
                        <SelectValue :placeholder="categories.find(c => c.id === selectedCategory)?.name" />
                      </SelectTrigger>
                    </Select>
                  </div>
                </div>

                <div>
                  <Label for="question">Question Text</Label>
                  <Input
                    id="question"
                    v-model="newQuestionText"
                    placeholder="Enter the assessment question"
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
                    @click="addQuestion"
                    :disabled="!newQuestionText.trim()"
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
              <CardTitle>Questions for {{ categories.find(c => c.id === selectedCategory)?.name }}</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="space-y-6">
                <div v-for="age in availableAges" :key="age">
                  <div v-if="getQuestionsForCategoryAndAge(selectedCategory, age).length > 0" class="space-y-2">
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
                        <TableRow v-for="(question, index) in getQuestionsForCategoryAndAge(selectedCategory, age)" :key="question.id">
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
                                @click="deleteQuestion(selectedCategory, question.id)"
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

                <div v-if="categories.find(c => c.id === selectedCategory)?.questions.length === 0" class="text-center text-gray-500 py-8">
                  No questions added yet for this category.
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </main>
  </div>
</template>