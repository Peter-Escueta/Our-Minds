import { createColumnHelper } from '@tanstack/vue-table'
import { h } from 'vue'
import { Button } from '@/components/ui/button/index'
import { ArrowUpDown } from 'lucide-vue-next'
import { format, parseISO } from 'date-fns'
import type { User } from '@/types'

const columnHelper = createColumnHelper<User>()

export const columns = (onDelete: (id: number) => void) => [
  columnHelper.accessor('name', {
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
      const user = row.original
      return h('div', { class: 'font-medium' }, user.name)
    },
    sortingFn: (rowA, rowB) => {
      return rowA.original.name.localeCompare(rowB.original.name)
    }
  }),

  columnHelper.accessor('created_at', {
    header: 'Created At',
    cell: ({ row }) => {
      try {

        const createdAt = row.original.created_at
        if (!createdAt) return 'N/A'

        const date = parseISO(createdAt)
        const formattedDate = format(date, 'MM/dd/yyyy')
        return formattedDate
      } catch (error) {
        console.error('Date formatting error:', error)
        return 'Invalid date'
      }
    },
    sortingFn: 'datetime'
  }),

  columnHelper.accessor('email', {
    header: 'Email',
    cell: ({ row }) => {
      return h('div', row.original.email)
    }
  }),

  columnHelper.accessor('role', {
    header: 'Role',
    cell: ({ row }) => {
      const role = row.original.role
      const displayRole = role === 'assessor' ? 'Assessor' : 'Consultant'
      return h('div', { class: 'font-medium' }, displayRole)
    }
  }),

  columnHelper.display({
    id: 'actions',
    header: 'Actions',
    cell: ({ row }) => {
      const user = row.original

      return h('div', { class: 'flex flex-col gap-1' }, [
        h('div', { class: 'flex gap-2' }, [
          h(Button, {
            variant: 'destructive',
            class: 'w-fit cursor-pointer',
            onClick: () => onDelete(user.id),
          }, () => 'Delete User'),
        ]),
      ])
    },
  })
]
