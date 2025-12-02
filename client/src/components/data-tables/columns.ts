import { createColumnHelper } from '@tanstack/vue-table'
import { h } from 'vue'
import { Button } from '@/components/ui/button/index'
import { ArrowUpDown } from 'lucide-vue-next'
import DropdownAction from './DataTableActions.vue'
import { Badge } from '@/components/ui/badge'
import { format, differenceInYears, parseISO } from 'date-fns'
import type { Child } from '@/types/child'

const columnHelper = createColumnHelper<Child>()

export const columns = [
  columnHelper.accessor('surname', {
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => [
        'Name',
        h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })
      ])
    },
    cell: ({ row }) => {
      const child = row.original
      return h('div', { class: 'font-medium' },
        `${child.surname}, ${child.first_name}${child.middle_name ? ` ${child.middle_name.charAt(0)}.` : ''}`
      )
    },
    sortingFn: (rowA, rowB) => {
      const surnameCompare = rowA.original.surname.localeCompare(rowB.original.surname)
      if (surnameCompare !== 0) return surnameCompare
      return rowA.original.first_name.localeCompare(rowB.original.first_name)
    }
  }),

  columnHelper.accessor('date_of_birth', {
    header: 'Age',
    cell: ({ row }) => {
      try {
        const dob = parseISO(row.getValue('date_of_birth'))
        const age = differenceInYears(new Date(), dob)
        const formattedDate = format(dob, 'MM/dd/yyyy')
        return h('div', `${age} yrs (${formattedDate})`)
      } catch (error) {
        console.error('Error formatting date:', error)
        return h('div', 'Invalid date')
      }
    },
    sortingFn: 'datetime'
  }),

  columnHelper.accessor('gender', {
    header: 'Gender',
    cell: ({ row }) => {
      const gender = row.getValue('gender')
      return h(
        Badge,
        {
          variant: gender === 'male' ? 'default' :
                  gender === 'female' ? 'secondary' : 'outline'
        },
        () => gender.charAt(0).toUpperCase() + gender.slice(1)
      )
    }
  }),

  columnHelper.accessor('mother_name', {
    header: 'Parent/Guardian',
    cell: ({ row }) => {
      return h('div', [
        h('div', { class: 'font-medium' }, row.original.mother_name),
        row.original.father_name && h('div', { class: 'text-sm text-muted-foreground' }, row.original.father_name)
      ])
    }
  }),

  columnHelper.accessor('mother_contact', {
    header: 'Contact',
    cell: ({ row }) => {
      return h('div', [
        h('div', row.original.mother_contact),
        row.original.father_contact && h('div', { class: 'text-sm text-muted-foreground' }, row.original.father_contact)
      ])
    }
  }),

  columnHelper.accessor('assessments', {
    id: 'status',
    header: 'Assessments/Evaluations',
    cell: ({ row }) => {
      const assessments = row.original.assessments || []
      const hasAssessments = assessments.length > 0

      const allEvaluations = assessments.flatMap(
        (assessment) => assessment.evaluations || []
      )

      const totalEvaluations = allEvaluations.length
      const hasEvaluations = totalEvaluations > 0

      const hasBackground = allEvaluations.some(
        (evaluation) => evaluation.status === 'ready_for_evaluation'
      )

      const hasCompletedEvaluations = allEvaluations.some(
        (evaluation) =>
          evaluation.status === 'complete' ||
          evaluation.status === 'finished' ||
          evaluation.status === 'submitted'
      )

      const latestAssessment = hasAssessments
        ? assessments.reduce((latest, current) =>
            new Date(current.assessment_date) > new Date(latest.assessment_date) ? current : latest
          )
        : null

      return h('div', { class: 'flex flex-col gap-1' }, [
        h('div', { class: 'flex gap-2' }, [
          h(Badge, {
            variant: hasAssessments ? 'default' : 'secondary',
            class: 'w-fit'
          }, () => hasAssessments ? 'Assessment Created' : 'No Assessment'),

          h(Badge, {
            variant: hasCompletedEvaluations ? 'default' :
                    hasBackground ? 'warning' :
                    hasEvaluations ? 'outline' : 'secondary',
            class: 'w-fit'
          }, () => {
            if (hasCompletedEvaluations) return 'Evaluation Complete'
            if (hasBackground) return 'Awaiting Evaluation'
            if (hasEvaluations) return `${totalEvaluations} Evaluation(s)`
            return 'No Evaluation'
          })
        ]),

        latestAssessment && h('div', {
          class: 'text-xs text-muted-foreground'
        }, `Last: ${format(parseISO(latestAssessment.assessment_date), 'MMM dd, yyyy')}`),

        hasBackground && h('div', {
          class: 'text-xs text-yellow-600 dark:text-yellow-400 font-medium'
        }, 'Evaluation created -  data pending')
      ])
    }
  }),

  columnHelper.display({
    id: 'actions',
    cell: ({ row, table }) => h(DropdownAction, {
      child: row.original,
      hasPendingBackgroundEvaluation: (row.original.assessments || [])
        .flatMap(a => a.evaluations || [])
        .some(e => e.status === 'ready_for_evaluation'),
      onDelete: (id: number) => {

        (table.options.meta as any)?.handleDelete?.(id)
      }
    }),
    meta: {
      className: 'sticky right-0 bg-background'
    }
  })
]
