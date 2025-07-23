<script setup lang="ts">
    import { ref, computed } from 'vue'
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
    import {
      DropdownMenu,
      DropdownMenuContent,
      DropdownMenuItem,
      DropdownMenuTrigger,
    } from '@/components/ui/dropdown-menu'
    import { Button } from '@/components/ui/button'
    import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'

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
        questions: [
        { id: 'psych1-3', text: 'Plays cooperatively with other children', age: 3 },
        { id: 'psych1-4', text: 'Shows empathy for others', age: 4 },
        { id: 'psych1-5', text: 'Takes turns in games', age: 5 },
        { id: 'psych2-3', text: 'Expresses emotions appropriately', age: 3 },
        { id: 'psych2-5', text: 'Resolves conflicts with peers', age: 5 },
        ]
    },
    {
        id: 'language',
        name: 'Language Skills',
        color: 'bg-blue-700',
        questions: [
        { id: 'lang1-3', text: 'Uses 3-4 word sentences', age: 3 },
        { id: 'lang1-4', text: 'Tells simple stories', age: 4 },
        { id: 'lang1-5', text: 'Uses future tense correctly', age: 5 },
        { id: 'lang2-4', text: 'Follows 3-step instructions', age: 4 },
        { id: 'lang2-5', text: 'Understands opposites', age: 5 },
        ]
    },
    {
        id: 'fine-motor',
        name: 'Fine Motor Skills',
        color: 'bg-green-700',
        questions: [
        { id: 'fine1-3', text: 'Holds crayon with fingers', age: 3 },
        { id: 'fine1-4', text: 'Cuts along a line with scissors', age: 4 },
        { id: 'fine1-5', text: 'Writes some letters', age: 5 },
        { id: 'fine2-5', text: 'Buttons and unbuttons clothing', age: 5 },
        ]
    },
    {
        id: 'cognitive',
        name: 'Cognitive Skills',
        color: 'bg-yellow-700',
        questions: [
        { id: 'cog1-3', text: 'Matches colors and shapes', age: 3 },
        { id: 'cog1-4', text: 'Counts to 10', age: 4 },
        { id: 'cog1-5', text: 'Recognizes some letters', age: 5 },
        { id: 'cog2-5', text: 'Understands concept of time', age: 5 },
        ]
    },
    {
        id: 'gross-motor',
        name: 'Gross Motor Skills',
        color: 'bg-purple-700',
        questions: [
        { id: 'gross1-3', text: 'Jumps with both feet', age: 3 },
        { id: 'gross1-4', text: 'Hops on one foot', age: 4 },
        { id: 'gross1-5', text: 'Skips alternating feet', age: 5 },
        { id: 'gross2-5', text: 'Catches a ball with hands', age: 5 },
        ]
    }
    ]

    const selectedAges = ref<Record<string, number>>({
    psychosocial: 5,
    language: 5,
    'fine-motor': 5,
    cognitive: 5,
    'gross-motor': 5,
    })

    const formResponses = ref<Record<string, string>>({})

    const availableAges = [3, 4, 5]

    const getQuestionsForCategory = (categoryId: string) => {
    const age = selectedAges.value[categoryId]
    const category = categories.find(c => c.id === categoryId)
    return category?.questions.filter(q => q.age === age) || []
    }

    const handleAgeChange = (categoryId: string, age: number) => {
    selectedAges.value[categoryId] = age
    // Clear responses for questions in this category when age changes
    const questionIds = getQuestionsForCategory(categoryId).map(q => q.id)
    questionIds.forEach(id => {
        delete formResponses.value[id]
    })
    }

    const handleLogout = () => {
      // Add logout logic here
      console.log('User logged out')
    }

    const navigateToSettings = () => {
      // Add navigation to settings logic here
      console.log('Navigating to settings')
    }
</script>

<template>
  <div class="min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-10">
      <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex items-center space-x-2">
          <div class="text-2xl font-bold text-primary">UNAWA</div>
          <div class="text-sm text-secondary">Child Development Assessment System</div>
        </div>
        
        <div class="flex items-center space-x-4">
          <DropdownMenu>
            <DropdownMenuTrigger as-child>
              <Button variant="ghost" class="relative h-8 w-8 rounded-full">
                <Avatar class="h-8 w-8">
                  <AvatarImage src="/avatars/default.png" alt="User" />
                  <AvatarFallback>U</AvatarFallback>
                </Avatar>
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent class="w-56" align="end" forceMount>
              <DropdownMenuItem @click="navigateToSettings">
                <span>Settings</span>
              </DropdownMenuItem>
              <DropdownMenuItem @click="handleLogout" class="text-red-600">
                <span>Log out</span>
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto font-display py-8 border-1">
      <h1 class="text-center text-2xl text-primary font-bold mb-5">Child Development Assessment Form</h1>

      <div class="space-y-8 px-20">
        <Card class="py-0 gap-0 text-center" v-for="category in categories" :key="category.id">
          <CardHeader  class=" pb-0  mb-0 bg-gray-100 p-4 border-b-0 rounded-t-lg " :class="category.color">
            <div class="grid grid-cols-12 items-center">
              <div class="col-span-12">
                <CardTitle class="font-bold text-zinc-200">{{ category.name }}</CardTitle>
              </div>
              <div class="col-span-2 flex justify-end items-center space-x-2 text-zinc-100">
                <Label class="whitespace-nowrap">Selected Age:</Label>
                <Select 
                  :modelValue="selectedAges[category.id]" 
                  @update:modelValue="(value) => handleAgeChange(category.id, Number(value))"
                >
                  <SelectTrigger class="w-[100px]">
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
          </CardHeader>
          
          <CardContent class="p-0">
            <div v-if="getQuestionsForCategory(category.id).length === 0" class="text-foreground p-4">
              No questions available for selected age.
            </div>
            
            <div v-else>
              <!-- Table Layout -->
              <div class="border rounded-b-lg overflow-hidden">
                <div class="grid grid-cols-12 bg-gray-50 p-4 font-bold border-b">
                  <div class="col-span-1 text-center text-green-600">CAN</div>
                  <div class="col-span-10 text-center text-foreground">QUESTION</div>
                  <div class="col-span-1 text-center text-red-600">CANNOT</div>
                </div>
                
                <div 
                  v-for="question in getQuestionsForCategory(category.id)" 
                  :key="question.id" 
                  class="grid grid-cols-12 p-4 border-t hover:bg-gray-50"
                >
                  <!-- CAN Column -->
                  <div class="col-span-1 flex justify-center">
                    <RadioGroup 
                      v-model="formResponses[question.id]" 
                      class="flex items-center"
                    >
                      <RadioGroupItem 
                        value="can" 
                        :id="`${question.id}-can`" 
                        class="h-5 w-5"
                      />
                    </RadioGroup>
                  </div>
                  
                  <!-- Question Column -->
                  <div class="col-span-10 flex items-center justify-center">
                    <Label :for="`${question.id}-can`" class="cursor-pointer ml-4 text-foreground">
                      {{ question.text }}
                    </Label>
                  </div>
                  
                  <!-- CANNOT Column -->
                  <div class="col-span-1 flex justify-center">
                    <RadioGroup 
                      v-model="formResponses[question.id]" 
                      class="flex items-center"
                    >
                      <RadioGroupItem 
                        value="cannot" 
                        :id="`${question.id}-cannot`" 
                        class="h-5 w-5"
                      />
                    </RadioGroup>
                  </div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
      
      <div class="mt-8 flex justify-center">
        <button class="px-4 py-2 bg-primary text-primary-foreground rounded hover:bg-primary/90 items-center">
          Submit Assessment
        </button>
      </div>
    </main>
  </div>
</template>