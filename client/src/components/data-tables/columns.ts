import { createColumnHelper } from '@tanstack/vue-table'
import { h } from 'vue'
import { Button } from '@/components/ui/button/index'
import { ArrowUpDown, MoreHorizontal } from 'lucide-vue-next'
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
  
  columnHelper.accessor('assessments_count', {
    header: 'Assessments/Evaluations',
    cell: ({ row }) => {
      const assessmentsCount = row.getValue('assessments_count') || 0
      const assessments = row.original.assessments || []
      
      const evaluationsCount = assessments.reduce((total, assessment) => {
        return total + (assessment.evaluations_count || (assessment.evaluations ? assessment.evaluations.length : 0))
      }, 0)

      const latestAssessment = assessments.length > 0 
        ? assessments.reduce((latest, current) => 
            new Date(current.assessment_date) > new Date(latest.assessment_date) ? current : latest
          )
        : null
      
      return h('div', { class: 'flex flex-col gap-1' }, [
        h('div', { class: 'flex gap-2' }, [
          h(Badge, {
            variant: assessmentsCount > 0 ? 'default' : 'secondary',
            class: 'w-fit'
          }, () => `${assessmentsCount} assessment${assessmentsCount !== 1 ? 's' : ''}`),
          h(Badge, {
            variant: evaluationsCount > 0 ? 'default' : 'secondary',
            class: 'w-fit'
          }, () => `${evaluationsCount} evaluation${evaluationsCount !== 1 ? 's' : ''}`)
        ]),
        latestAssessment && h('div', { 
          class: 'text-xs text-muted-foreground' 
        }, `Last: ${format(new Date(latestAssessment.assessment_date), 'MMM dd, yyyy')}`)
      ])
    }
  }),
  
  columnHelper.display({
    id: 'actions',
    cell: ({ row }) => h(DropdownAction, { child: row.original }),
    meta: {
      className: 'sticky right-0 bg-background'
    }
  })
]