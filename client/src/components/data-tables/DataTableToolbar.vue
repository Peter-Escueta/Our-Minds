<script setup lang="ts">
import { computed, ref } from 'vue'
import { useVueTable } from '@tanstack/vue-table'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { ChevronDown } from 'lucide-vue-next'

const props = defineProps<{
  table: ReturnType<typeof useVueTable>
}>()

const emit = defineEmits(['refresh'])

const filterInput = ref('')
const filterColumn = ref('surname')

const handleFilter = () => {
  props.table.getColumn(filterColumn.value)?.setFilterValue(filterInput.value)
}
</script>

<template>
  <div class="flex items-center justify-between">
    <div class="flex flex-1 items-center space-x-2">
      <Input
        v-model="filterInput"
        placeholder="Filter children..."
        class="h-8 w-[150px] lg:w-[250px]"
        @keyup.enter="handleFilter"
      />
      <select
        v-model="filterColumn"
        class="h-8 rounded-md border border-input bg-background px-3 py-2 text-sm"
      >
        <option value="surname">By Surname</option>
        <option value="first_name">By First Name</option>
        <option value="mother_name">By Mother's Name</option>
      </select>
    </div>
    
    <div class="flex items-center space-x-2">
      <Button
        variant="outline"
        size="sm"
        @click="emit('refresh')"
      >
        Refresh
      </Button>
      
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <Button variant="outline" size="sm" class="ml-auto">
            Columns <ChevronDown class="ml-2 h-4 w-4" />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
          <DropdownMenuCheckboxItem
            v-for="column in table.getAllColumns().filter(column => column.getCanHide())"
            :key="column.id"
            class="capitalize"
            :checked="column.getIsVisible()"
            @update:checked="value => column.toggleVisibility(!!value)"
          >
            {{ column.id }}
          </DropdownMenuCheckboxItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  </div>
</template>