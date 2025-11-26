<script setup lang="ts">
import { watch, ref, capitalize } from 'vue'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
  SelectGroup,
  SelectLabel,
} from '@/components/ui/select'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Plus, Trash2, Pencil, Loader2, Satellite } from 'lucide-vue-next'
import { useChecklist } from '@/composables/useChecklist'
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { useApi } from '@/composables/useApi'
import { ssrInterpolate } from 'vue/server-renderer'
const { api, handleApiError } = useApi()
const {
  categories,
  questions,
  availableAges,
  selectedCategory,
  selectedAge,
  newQuestionText,
  editingQuestionId,
  isLoading,
  isCategoriesLoading,
  fetchQuestions,
  findCategory,
  getQuestionsForAge,
  saveQuestion,
  editQuestion,
  deleteQuestion,
  cancelEdit,
} = useChecklist()
const category = ref('')
const colors = [
  'slate',
  'gray',
  'zinc',
  'neutral',
  'red',
  'stone',
  'orange',
  'yellow',
  'lime',
  'green',
  'emerald',
  'teal',
  'cyan',
  'sky',
  'blue',
  'indigo',
  'violet',
  'purple',
  'fuchsia',
  'pink',
  'rose',
]
const color = ref()
async function createCategory() {
  try {
    const response = await api.post('/skill-categories', {
      name: category.value,
      color: color.value,
    })
    const data = response.data.data
    categories.value.push(data)
    category.value = ''
    color.value = ''
  } catch (error) {
    handleApiError(error)
  }
}
async function deleteCategory(categoryId: number) {
  try {
    const response = await api.delete('/skill-categories/' + categoryId)
    const deletedId = response.data.category.id
    categories.value = categories.value.filter((c) => c.id !== deletedId)
  } catch (error) {
    handleApiError(error)
  }
}
watch(selectedCategory, fetchQuestions)
</script>

<template>
  <div class="min-h-screen">
    <h1 class="text-center text-2xl text-primary font-bold mb-8">Manage Assessment Questions</h1>
    <div
      class="bg-orange-700 bg-cyan-700 bg-yellow-700 bg-lime-700 bg-green-700 bg-emerald-700 bg-teal-700 bg-sky-700 bg-indigo-700 bg-violet-700 bg-fuchsia-700 bg-purple-700 bg-pink-700 bg-rose-700 bg-blue-700 bg-slate-700 bg-gray-700 bg-zinc-700 bg-neutral-700 bg-red-700 bg-stone-700"
    ></div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
      <!-- Left sidebar - Category selection -->
      <div class="lg:col-span-1 space-y-4">
        <Card>
          <CardHeader class="flex items-center justify-between">
            <CardTitle>Categories</CardTitle>
            <Dialog>
              <form>
                <DialogTrigger as-child>
                  <Button> Add New </Button>
                  <DialogContent>
                    <DialogClose></DialogClose>
                    <DialogTitle> Create a new category </DialogTitle>
                    <Label>Category Name</Label>
                    <Input v-model="category"></Input>
                    <Label>Header Color</Label>
                    <Select v-model="color">
                      <SelectTrigger
                        ><SelectValue placeholder="Select a header color"></SelectValue
                      ></SelectTrigger>
                      <SelectContent>
                        <SelectGroup>
                          <SelectLabel>Colors</SelectLabel>
                          <SelectItem v-for="color in colors" :key="color" :value="color">
                            {{ capitalize(color) }}
                            <div class="w-4 h-4 rounded-full" :class="`bg-${color}-700`"></div
                          ></SelectItem>
                        </SelectGroup>
                      </SelectContent>
                    </Select>
                    <Button @click="createCategory()">Submit </Button>
                  </DialogContent>
                </DialogTrigger>
              </form>
            </Dialog>
          </CardHeader>
          <CardContent>
            <div v-if="isCategoriesLoading" class="flex justify-center py-4">
              <Loader2 class="h-6 w-6 animate-spin" />
              <span class="ml-2">Loading...</span>
            </div>
            <div v-else class="space-y-2">
              <Button
                v-for="category in categories"
                :key="category.id"
                variant="ghost"
                class="w-full justify-start"
                :class="{ 'bg-accent': selectedCategory === category.id }"
                @click="selectedCategory = category.id"
              >
                <span class="truncate">{{ category.name }}</span>
                <Badge class="ml-2">{{ category.questions_count || 0 }}</Badge>
                <Button class="bg-red-700 h-6 w-6" @click="deleteCategory(category.id)">X</Button>
              </Button>
              <div v-if="categories.length === 0" class="text-center text-gray-500 py-2">
                No categories available
              </div>
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
                  <Select
                    v-model="selectedCategory"
                    :disabled="isCategoriesLoading || categories.length === 0"
                  >
                    <SelectTrigger>
                      <SelectValue>
                        {{ findCategory(selectedCategory)?.name || 'Select category' }}
                      </SelectValue>
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem
                        v-for="category in categories"
                        :key="category.id"
                        :value="category.id"
                      >
                        {{ category.name }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>

              <div>
                <Label for="question">Question Text</Label>
                <Input
                  id="question"
                  v-model="newQuestionText"
                  placeholder="Enter the assessment question"
                  @keyup.enter="saveQuestion"
                />
              </div>

              <div class="flex justify-end space-x-2">
                <Button v-if="editingQuestionId" variant="outline" @click="cancelEdit">
                  Cancel
                </Button>
                <Button
                  @click="saveQuestion"
                  :disabled="!newQuestionText.trim() || !selectedCategory"
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
            <CardTitle>
              Questions for {{ findCategory(selectedCategory)?.name || 'Selected Category' }}
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div v-if="isLoading" class="flex justify-center py-8">
              <Loader2 class="h-8 w-8 animate-spin" />
              <span class="ml-2">Loading questions...</span>
            </div>

            <div v-else class="space-y-6">
              <div v-for="age in availableAges" :key="age">
                <div v-if="getQuestionsForAge(age).length > 0" class="space-y-2">
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
                      <TableRow
                        v-for="(question, index) in getQuestionsForAge(age)"
                        :key="question.id"
                      >
                        <TableCell>{{ index + 1 }}</TableCell>
                        <TableCell>{{ question.text }}</TableCell>
                        <TableCell>
                          <div class="flex space-x-2">
                            <Button variant="ghost" size="sm" @click="editQuestion(question)">
                              <Pencil class="h-4 w-4" />
                            </Button>
                            <Button variant="ghost" size="sm" @click="deleteQuestion(question.id)">
                              <Trash2 class="h-4 w-4 text-red-600" />
                            </Button>
                          </div>
                        </TableCell>
                      </TableRow>
                    </TableBody>
                  </Table>
                </div>
              </div>

              <div v-if="questions.length === 0" class="text-center text-gray-500 py-8">
                No questions found for this category. Add your first question above.
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>
</template>
