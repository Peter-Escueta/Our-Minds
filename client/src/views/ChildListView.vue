<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import ChildDataTable from '@/components/data-tables/ChildDataTable.vue'
import type { Child } from '@/types/child'
import Button from '@/components/ui/button/Button.vue'
import { useChildren } from '@/composables/useChildren'
import { useAuth } from '@/composables/useAuth'

const { fetchChildren, data: childrenData, isLoading: childrenLoading } = useChildren()
const router = useRouter()
const data = ref<Child[]>([])
const isLoading = ref(true)
const { isConsultant } = useAuth()

watch(childrenData, (newData) => {
  data.value = newData
})

watch(childrenLoading, (newLoading) => {
  isLoading.value = newLoading
})

const handleAddChild = () => {
  if (!isConsultant.value) return
  router.push('/children/create')
}
const handleChildDeleted = (deletedChildId: number) => {
  console.log('reached')
  data.value = data.value.filter((child) => child.id !== deletedChildId)
  childrenData.value = childrenData.value.filter((child) => child.id !== deletedChildId)
}
onMounted(() => {
  fetchChildren()
})
</script>

<template>
  <div class="flex flex-col space-y-4 h-full">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-primary">Children Management</h1>
      <Button v-if="isConsultant" @click="handleAddChild"> Add New Child </Button>
    </div>

    <ChildDataTable
      :data="childrenData"
      :isLoading="isLoading"
      @refresh="fetchChildren"
      @delete="handleChildDeleted"
      class="flex-1"
    />
  </div>
</template>
