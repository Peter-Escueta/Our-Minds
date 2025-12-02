<script setup lang="ts">
import { computed } from 'vue'
import type { Child } from '@/types/child'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
  DropdownMenuSeparator,
  DropdownMenuSub,
  DropdownMenuSubTrigger,
  DropdownMenuSubContent,
} from '@/components/ui/dropdown-menu'
import { Button } from '@/components/ui/button'
import {
  MoreHorizontal,
  FileText,
  Activity,
  Trash2,
  Eye,
  ClipboardEdit,
  ClipboardCheck,
  CheckCircle2,
} from 'lucide-vue-next'
import { useRouter } from 'vue-router'
import { useApi } from '@/composables/useApi'
import ConsentButton from '@/components/ConsentButton.vue'

const router = useRouter()
const { api } = useApi()
const userRole = sessionStorage.getItem('user_role')

const props = defineProps<{
  child: Child
  hasPendingBackgroundEvaluation?: boolean
}>()

const emit = defineEmits(['delete'])

const isAssessor = computed(() => userRole === 'assessor')
const isConsultant = computed(() => userRole === 'consultant')

const sortedAssessments = computed(() => {
  if (!props.child.assessments || props.child.assessments.length === 0) return []
  return [...props.child.assessments].sort(
    (a, b) => new Date(b.assessment_date).getTime() - new Date(a.assessment_date).getTime(),
  )
})

const latestAssessment = computed(() => sortedAssessments.value[0] || null)

const allEvaluations = computed(() => {
  return (props.child.assessments || []).flatMap((assessment) => assessment.evaluations || [])
})

const sortedEvaluations = computed(() => {
  return [...allEvaluations.value].sort(
    (a, b) => new Date(b.created_at || 0).getTime() - new Date(a.created_at || 0).getTime(),
  )
})

const latestEvaluation = computed(() => sortedEvaluations.value[0] || null)

const hasPendingBackground = computed(() => {
  return latestEvaluation.value?.status === 'ready_for_evaluation'
})

const isFinalized = computed(() => {
  const status = latestEvaluation.value?.status
  return status === 'complete' || status === 'finished' || status === 'submitted'
})

const hasAnyEvaluations = computed(() => allEvaluations.value.length > 0)

const handleAssess = () => {
  router.push(`/assessments/${props.child.id}/create`)
}

const handleViewAssessments = () => {
  if (latestAssessment.value) {
    router.push(`/assessments/${latestAssessment.value.id}/results`)
  }
}

const handleCreateEvaluation = () => {
  if (latestAssessment.value) {
    router.push(`/assessments/${latestAssessment.value.id}/evaluate`)
  }
}

const handleViewEvaluation = () => {
  if (latestEvaluation.value) {
    router.push(`/evaluations/${latestEvaluation.value.id}`)
  }
}

const handleDelete = async () => {
  try {
    const response = await api.delete(`/children/${props.child.id}`)
    const data = response.data.id
    emit('delete', data)
  } catch (error) {
    console.error('Deletion failed', error)
  }
}
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="h-8 w-8 p-0">
        <MoreHorizontal class="h-4 w-4" />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end" class="w-64">
      <DropdownMenuSub>
        <DropdownMenuSubTrigger>
          <Activity class="mr-2 h-4 w-4" />
          Assessments
        </DropdownMenuSubTrigger>
        <DropdownMenuSubContent>
          <DropdownMenuItem
            v-if="isAssessor && !latestAssessment"
            @click="handleAssess"
            class="text-primary font-medium cursor-pointer"
          >
            Create Assessment
          </DropdownMenuItem>

          <DropdownMenuItem
            v-if="latestAssessment"
            @click="handleViewAssessments"
            class="cursor-pointer"
          >
            View Latest Results
          </DropdownMenuItem>

          <DropdownMenuItem v-if="!latestAssessment" disabled class="text-muted-foreground text-xs">
            No assessments found
          </DropdownMenuItem>
        </DropdownMenuSubContent>
      </DropdownMenuSub>

      <DropdownMenuSub>
        <DropdownMenuSubTrigger>
          <FileText class="mr-2 h-4 w-4" />
          Evaluations
        </DropdownMenuSubTrigger>
        <DropdownMenuSubContent>
          <template v-if="isAssessor">
            <DropdownMenuItem
              v-if="latestAssessment && !hasAnyEvaluations"
              @click="handleCreateEvaluation"
              class="text-primary font-medium cursor-pointer"
            >
              <ClipboardEdit class="mr-2 h-4 w-4" />
              Create Evaluation
            </DropdownMenuItem>

            <DropdownMenuItem
              v-if="hasAnyEvaluations"
              @click="handleViewEvaluation"
              class="cursor-pointer"
            >
              <Eye class="mr-2 h-4 w-4" />
              View Evaluation
            </DropdownMenuItem>
          </template>

          <template v-if="isConsultant">
            <DropdownMenuItem
              v-if="hasAnyEvaluations && !isFinalized"
              @click="handleCreateEvaluation"
              class="text-indigo-600 font-medium cursor-pointer"
            >
              <CheckCircle2 class="mr-2 h-4 w-4" />
              Finalize Evaluation
            </DropdownMenuItem>

            <DropdownMenuItem
              v-if="hasAnyEvaluations && !isFinalized"
              @click="handleViewEvaluation"
              class="cursor-pointer"
            >
              <ClipboardEdit class="mr-2 h-4 w-4" />
              View Evaluation
            </DropdownMenuItem>

            <DropdownMenuItem
              v-if="isFinalized"
              @click="handleViewEvaluation"
              class="text-green-600 font-medium cursor-pointer"
            >
              <ClipboardCheck class="mr-2 h-4 w-4" />
              View Official Report
            </DropdownMenuItem>
          </template>

          <DropdownMenuItem v-if="!latestAssessment" disabled class="text-muted-foreground text-xs">
            Assessment required first
          </DropdownMenuItem>

          <DropdownMenuItem
            v-if="isAssessor && hasAnyEvaluations && !hasPendingBackground && !isFinalized"
            disabled
            class="text-muted-foreground text-xs"
          >
            Pending Consultant Review
          </DropdownMenuItem>
        </DropdownMenuSubContent>
      </DropdownMenuSub>

      <DropdownMenuSeparator />

      <DropdownMenuItem as-child>
        <div class="w-full">
          <ConsentButton :childId="props.child.id" />
        </div>
      </DropdownMenuItem>

      <DropdownMenuSeparator />

      <DropdownMenuItem @click="handleDelete" class="text-destructive cursor-pointer">
        <Trash2 class="mr-2 h-4 w-4" />
        Delete Child
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
