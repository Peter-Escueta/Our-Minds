<script setup lang="ts">
import { h, computed } from 'vue'
import type { Child } from '@/types/child'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Button } from '@/components/ui/button'
import { MoreHorizontal } from 'lucide-vue-next'
import { useRouter } from 'vue-router'

const router = useRouter()
const userRole = localStorage.getItem('user_role')

const props = defineProps<{
  child: Child
}>()

const emit = defineEmits(['edit', 'delete', 'view', 'refresh'])

const handleView = () => {
  router.push(`/children/${props.child.id}`)
}

const handleEdit = () => {
  router.push(`/children/${props.child.id}/edit`)
}

const handleDelete = async () => {
  emit('delete', props.child.id)
}

const handleAssess = () => {
  router.push(`/assessments/${props.child.id}/create`)
}

const handleEvaluate = () => {
  router.push(`/evaluations/create?childId=${props.child.id}`)
}

const hasAssessments = computed(() => {
  return (props.child.assessments_count ?? 0) > 0 || 
         (props.child.assessments && props.child.assessments.length > 0)
})
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="h-8 w-8 p-0">
        <MoreHorizontal class="h-4 w-4" />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <DropdownMenuItem @click="handleView">
        View Details
      </DropdownMenuItem>
      
      <DropdownMenuItem @click="handleEdit">
        Edit
      </DropdownMenuItem>
      
      <!-- Assessor Actions -->
      <template v-if="userRole === 'assessor'">
        <DropdownMenuItem 
          v-if="!hasAssessments"
          @click="handleAssess"
          class="text-primary font-medium"
        >
          Create Assessment
        </DropdownMenuItem>
        <DropdownMenuItem 
          v-if="hasAssessments"
          @click="() => router.push(`/assessments?childId=${props.child.id}`)"
        >
          View Assessments
        </DropdownMenuItem>
      </template>
      
      <!-- Consultant Actions -->
      <template v-if="userRole === 'consultant'">
        <DropdownMenuItem 
          v-if="hasAssessments"
          @click="handleEvaluate"
          class="text-primary font-medium"
        >
          Create Evaluation
        </DropdownMenuItem>
        <DropdownMenuItem 
          v-if="hasAssessments"
          @click="() => router.push(`/evaluations?childId=${props.child.id}`)"
        >
          View Evaluations
        </DropdownMenuItem>
      </template>
      
      <DropdownMenuItem 
        class="text-destructive" 
        @click="handleDelete"
      >
        Delete
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>