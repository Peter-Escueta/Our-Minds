<script setup lang="ts">
import { Button } from '@/components/ui/button'
import UserDataTable from '@/components/data-tables/UserDataTable.vue'
import { ref, onMounted, watch } from 'vue'
import type { User } from '@/types'
import { useUserTable } from '@/composables/useUserTable'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { useApi } from '@/composables/useApi'
import { Label } from '@/components/ui/label'
const email = ref('')
const password = ref('')
const isCreatingAccount = ref(false)
const { api, handleApiError } = useApi()
const handleAccountCreation = async () => {
  if (!email.value || !password.value) {
    handleApiError(new Error('Please fill in all fields'), 'Missing required fields')
    return
  }

  isCreatingAccount.value = true
  try {
    const response = await api.post('/users', {
      email: email.value,
      password: password.value,
    })

    console.log('Account created successfully:', response.data)

    email.value = ''
    password.value = ''

    await fetchUsers()
  } catch (error) {
    console.error('Error creating account:', error)
  } finally {
    isCreatingAccount.value = false
  }
}

const { fetchUsers, users: fetchedUsers, isLoading: usersLoading } = useUserTable()
const users = ref<User[]>([])
const isLoading = ref(true)

onMounted(async () => {
  await fetchUsers()
})

watch(fetchedUsers, (newUsers) => {
  users.value = Array.isArray(newUsers) ? newUsers : [newUsers]
})

watch(usersLoading, (loading) => {
  isLoading.value = loading
})
</script>

<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Account Management</h1>
      <Dialog>
        <DialogTrigger as-child>
          <Button variant="outline"> Create Account </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[425px]">
          <DialogHeader>
            <DialogTitle>Create Account</DialogTitle>
            <DialogDescription>
              In order to create an account please fill in the form below.
            </DialogDescription>
          </DialogHeader>
          <div class="grid gap-4 py-4">
            <div class="grid grid-cols-4 items-center gap-4">
              <Label for="email" class="text-right"> Email </Label>
              <Input
                id="email"
                placeholder="john.doe@gmail.com"
                class="col-span-3"
                v-model="email"
              />
            </div>
            <div class="grid grid-cols-4 items-center gap-4">
              <Label for="password" class="text-right"> Password </Label>
              <Input
                id="password"
                placeholder="Insert password"
                type="password"
                class="col-span-3"
                v-model="password"
              />
            </div>
          </div>
          <DialogFooter>
            <Button @click="handleAccountCreation"> Create Account </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>

    <UserDataTable :data="users" :isLoading="isLoading" @refresh="fetchUsers" class="flex-1" />
  </div>
</template>
