<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { toast } from 'vue-sonner'
import { useRouter } from 'vue-router'
import ChildDataTable from '@/components/data-tables/ChildDataTable.vue'
import type { Child } from '@/types/child'
import Button from '@/components/ui/button/Button.vue'
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
} from '@/components/ui/dropdown-menu'
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar'
import Header from '@/components/Header.vue'

const router = useRouter()
const data = ref<Child[]>([])
const isLoading = ref(true)
const userRole = localStorage.getItem('user_role') // Get user role from localStorage

// Computed property to check if user is consultant
const isConsultant = computed(() => userRole === 'consultant')

const handleLogout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user_role')
  axios.defaults.headers.common['Authorization'] = ''
  toast.success('Logged out successfully')
  router.push('/login')
}

const navigateToSettings = () => {
  router.push('/settings')
}

const handleAddChild = () => {
  if (!isConsultant.value) return // Extra protection
  router.push('/children/create')
}

const handleRefresh = () => {
  fetchChildren()
}

const fetchChildren = async () => {
  try {
    isLoading.value = true
    const token = localStorage.getItem('auth_token') 
    
    if (!token) {
      throw new Error('No authentication token found')
    }

    const response = await axios.get('http://localhost:8000/api/children', {
      headers: {
        'Authorization': `Bearer ${token}`
      },
      params: {
        with: 'assessments' 
      }
    })
    
    data.value = response.data
    console.log('Assigned Data:', data.value)
  } catch (error) {
    if (error.response?.status === 401) {
      toast.error('Session expired. Please login again.')
      router.push('/') 
    } else {
      toast.error('Failed to fetch children')
      console.error('Error:', error)
    }
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchChildren()
})
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <Header />
    <div class="flex-1 border-l border-r border-gray-200 mx-auto w-full max-w-[90rem]">
      <main class="container mx-auto font-display py-8 px-20 h-full">
        <div class="flex flex-col space-y-4 h-full">
          <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Children Management</h1>
            <Button 
              v-if="isConsultant"
              @click="handleAddChild"
            >
              Add New Child
            </Button>
          </div>
          
          <ChildDataTable 
            :data="data" 
            :isLoading="isLoading"
            @refresh="fetchChildren"
            class="flex-1"
          />
        </div>
      </main>
    </div>
  </div>
</template>