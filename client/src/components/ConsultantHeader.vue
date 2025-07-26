<script setup lang="ts">
import { useRouter } from 'vue-router'
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/components/ui/dropdown-menu'
import { Button } from '@/components/ui/button'
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar'

const router = useRouter()

const handleLogout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user_role')
  router.push('/')
}

const navigateToSettings = () => {
  router.push('/settings')
}

const navigateTo = (routeName: string) => {
  router.push({ name: routeName })
}
</script>

<template>
  <header class="bg-white shadow-sm sticky top-0 z-10">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
      <div class="flex items-center space-x-8">
        <div class="flex items-center space-x-2 cursor-pointer" @click="navigateTo('edit-checklist')">
          <div class="text-2xl font-bold text-primary">UNAWA</div>
          <div class="text-sm text-secondary">Child Development Assessment System</div>
        </div>
        
        <nav class="hidden md:flex space-x-6">
          <button 
            class="text-sm font-medium hover:text-primary transition-colors"
            @click="navigateTo('edit-checklist')"
          >
            Edit Checklist
          </button>
          <button 
            class="text-sm font-medium hover:text-primary transition-colors"
            @click="navigateTo('screening')"
          >
            New Screening
          </button>
          <button 
            class="text-sm font-medium hover:text-primary transition-colors"
            @click="navigateTo('children')"
          >
            Child List
          </button>
        </nav>
      </div>
      
      <div class="flex items-center space-x-4">
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="relative h-8 w-8 rounded-full">
              <Avatar class="h-8 w-8">
                <AvatarImage src="/avatars/default.png" alt="User" />
                <AvatarFallback>U</AvatarFallback>
              </Avatar>
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent class="w-56" align="end" forceMount>
            <DropdownMenuItem @click="navigateToSettings">
              <span>Settings</span>
            </DropdownMenuItem>
            <DropdownMenuItem @click="handleLogout" class="text-red-600">
              <span>Log out</span>
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </div>
    </div>
  </header>
</template>

<style scoped>
.router-link-active {
  color: var(--primary);
  font-weight: 600;
}
</style>