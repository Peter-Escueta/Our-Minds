<script setup lang="ts">
import { h, computed } from 'vue'
import type { Child, Assessment } from '@/types/child'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Button } from '@/components/ui/button'
import { MoreHorizontal } from 'lucide-vue-next'
import { useRouter } from 'vue-router'
import ConsentButton from '@/components/ConsentButton.vue'

const router = useRouter()
const userRole = sessionStorage.getItem('user_role')

const props = defineProps<{
  child: Child
}>()

const emit = defineEmits(['edit', 'delete', 'view', 'refresh'])

const handleView = () => {
  router.push(`/assessments/${props.child.id}/results`)
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
  // Navigate to evaluation creation for the first assessment
  const firstAssessment = props.child.assessments?.[0]
  if (firstAssessment) {
    router.push(`/assessments/${firstAssessment.id}/evaluate`)
  }
}

const hasAssessments = computed(() => {
  return (
    (props.child.assessments_count ?? 0) > 0 ||
    (props.child.assessments && props.child.assessments.length > 0)
  )
})

const hasEvaluations = computed(() => {
  if (!props.child.assessments) return false
  return props.child.assessments.some(
    (assessment) =>
      (assessment.evaluations_count ?? 0) > 0 ||
      (assessment.evaluations && assessment.evaluations.length > 0),
  )
})

// Get the latest evaluation for view navigation
const getLatestEvaluation = computed(() => {
  if (!props.child.assessments) return null

  // Find the first assessment that has evaluations
  const assessmentWithEvaluation = props.child.assessments.find(
    (assessment) => assessment.evaluations && assessment.evaluations.length > 0,
  )

  if (assessmentWithEvaluation && assessmentWithEvaluation.evaluations) {
    return assessmentWithEvaluation.evaluations[0] // Return first evaluation
  }

  return null
})

// Get all evaluations for view all page
const getAllEvaluationsPath = computed(() => {
  return `/evaluations/${props.child.id}`
})

// Check if user can create evaluation (has assessments but no evaluations)
const canCreateEvaluation = computed(() => {
  return hasAssessments.value && !hasEvaluations.value
})

// Check if user can view evaluations
const canViewEvaluations = computed(() => {
  return hasEvaluations.value
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
      <!-- Assessor Actions -->
      <template v-if="userRole === 'assessor'">
        <DropdownMenuItem
          v-if="!hasAssessments"
          @click="handleAssess"
          class="text-primary font-medium"
        >
          Create Assessment
        </DropdownMenuItem>
        <DropdownMenuItem v-if="hasAssessments" @click="handleView">
          View Assessments
        </DropdownMenuItem>
      </template>

      <!-- Consultant Actions -->
      <DropdownMenuItem
        v-if="canCreateEvaluation"
        @click="handleEvaluate"
        class="text-primary font-medium"
      >
        Create Evaluation
      </DropdownMenuItem>

      <!-- View specific evaluation if available -->
      <DropdownMenuItem
        v-if="getLatestEvaluation"
        @click="() => router.push(`/evaluations/${getLatestEvaluation.id}`)"
      >
        View Latest Evaluation
      </DropdownMenuItem>

      <DropdownMenuItem>
        <ConsentButton :childId="props.child.id" class="w-full text-left" />
      </DropdownMenuItem>

      <DropdownMenuItem class="text-destructive" @click="handleDelete"> Delete </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
