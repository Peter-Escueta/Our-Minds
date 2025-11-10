<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import {
  FlexRender,
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import { valueUpdater } from '@/components/ui/table/utils'
import type { User } from '@/types/index'
import { columns } from './user_columns'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import DataTableToolbar from './DataTableToolbar.vue'
import { Button } from '@/components/ui/button'

const props = defineProps<{
  data: User[]
  isLoading?: boolean
}>()

const emit = defineEmits(['refresh'])

const sorting = ref([])
const columnFilters = ref([])
const rowSelection = ref({})
const columnVisibility = ref({})

const tableData = computed(() => props.data || [])

const table = useVueTable({
  get data() {
    return tableData.value
  },
  columns,
  getCoreRowModel: getCoreRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getSortedRowModel: getSortedRowModel(),
  onSortingChange: (updater) => valueUpdater(updater, sorting),
  onColumnFiltersChange: (updater) => valueUpdater(updater, columnFilters),
  onRowSelectionChange: (updater) => valueUpdater(updater, rowSelection),
  onColumnVisibilityChange: (updater) => valueUpdater(updater, columnVisibility),
  state: {
    get sorting() {
      return sorting.value
    },
    get columnFilters() {
      return columnFilters.value
    },
    get rowSelection() {
      return rowSelection.value
    },
    get columnVisibility() {
      return columnVisibility.value
    },
  },
})

watch(
  tableData,
  () => {
    table.setPageIndex(0)
  },
  { immediate: true },
)
</script>

<template>
  <div class="space-y-4">
    <DataTableToolbar :table="table" @refresh="emit('refresh')" />

    <div class="rounded-md border">
      <Table>
        <TableHeader>
          <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
            <TableHead v-for="header in headerGroup.headers" :key="header.id">
              <FlexRender
                v-if="!header.isPlaceholder"
                :render="header.column.columnDef.header"
                :props="header.getContext()"
              />
            </TableHead>
          </TableRow>
        </TableHeader>

        <TableBody>
          <TableRow v-if="isLoading">
            <TableCell :colspan="columns.length" class="h-24 text-center">
              <div class="flex items-center justify-center gap-2">
                <span class="animate-pulse">Loading data...</span>
              </div>
            </TableCell>
          </TableRow>

          <TableRow v-else-if="table.getRowModel().rows?.length === 0">
            <TableCell :colspan="columns.length" class="h-24 text-center">
              <div class="flex flex-col items-center justify-center gap-2">
                <span>No records found</span>
                <Button variant="ghost" size="sm" @click="emit('refresh')"> Retry </Button>
              </div>
            </TableCell>
          </TableRow>

          <TableRow
            v-for="row in table.getRowModel().rows"
            :key="row.id"
            :data-state="row.getIsSelected() && 'selected'"
            class="hover:bg-muted/50"
          >
            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
              <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>

    <div class="flex flex-col items-center justify-between gap-4 px-2 sm:flex-row">
      <div class="text-sm text-muted-foreground">
        Showing
        {{ table.getState().pagination.pageIndex * table.getState().pagination.pageSize + 1 }}-
        {{
          Math.min(
            (table.getState().pagination.pageIndex + 1) * table.getState().pagination.pageSize,
            table.getFilteredRowModel().rows.length,
          )
        }}
        of {{ table.getFilteredRowModel().rows.length }} items
      </div>

      <div class="flex items-center space-x-2">
        <div class="flex items-center space-x-2">
          <p class="text-sm font-medium">Rows</p>
          <select
            :value="table.getState().pagination.pageSize"
            @change="table.setPageSize(Number($event.target.value))"
            class="h-8 rounded-md border border-input bg-background px-3 py-1 text-sm"
          >
            <option v-for="size in [10, 20, 30, 40, 50]" :key="size" :value="size">
              {{ size }}
            </option>
          </select>
        </div>

        <div class="flex space-x-2">
          <Button
            variant="outline"
            size="sm"
            :disabled="!table.getCanPreviousPage()"
            @click="table.previousPage()"
          >
            Previous
          </Button>
          <Button
            variant="outline"
            size="sm"
            :disabled="!table.getCanNextPage()"
            @click="table.nextPage()"
          >
            Next
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>
