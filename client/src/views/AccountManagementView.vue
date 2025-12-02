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
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Input } from '@/components/ui/input'
import { useApi } from '@/composables/useApi'
import { Label } from '@/components/ui/label'
const email = ref('')
const password = ref('')
const role = ref('')
const name = ref('')
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
      role: role.value,
      name: name.value,
    })

    email.value = ''
    password.value = ''
    role.value = ''
    name.value = ''
    await fetchUsers()
  } catch (error) {
    console.error('Error creating account:', error)
  } finally {
    isCreatingAccount.value = false
  }
}
async function handleDelete(userId: number) {
  const response = await api.delete('/users/' + userId)
  const deletedId = response.data.id
  users.value = users.value.filter((u) => u.id !== deletedId)
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
          <Button variant="outline" class="cursor-pointer"> Create Account </Button>
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
              <Label for="name" class="text-right"> Name </Label>
              <Input id="name" placeholder="John Doe" class="col-span-3" v-model="name" />
            </div>
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
            <div class="grid grid-cols-4 items-center gap-4">
              <Label for="role">Role</Label>
              <Select v-model="role">
                <SelectTrigger class="w-[180px]">
                  <SelectValue placeholder="Select a role" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    <SelectLabel>Role</SelectLabel>
                    <SelectItem value="assessor"> Assessor </SelectItem>
                    <SelectItem value="consultant"> Consultant </SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
            </div>
          </div>
          <DialogFooter>
            <Button @click="handleAccountCreation"> Create Account </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>

    <UserDataTable
      :data="users"
      :onDelete="handleDelete"
      :isLoading="isLoading"
      @refresh="fetchUsers"
      class="flex-1"
    />
  </div>
</template>
