<script setup lang="ts">
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
} from '@/components/ui/dropdown-menu'
import { Button } from '@/components/ui/button'
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar'
import { useAuth } from '@/composables/useAuth'

const userRole = sessionStorage.getItem('user_role')
const { logout } = useAuth()
const handleLogout = () => {
  logout()
}
import { useNavigation } from '@/composables/useNavigation'
const { navigateTo } = useNavigation()
</script>

<template>
  <header class="bg-white shadow-sm sticky top-0 z-10">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
      <!-- Logo Section -->
      <div class="flex items-center space-x-8">
        <div
          class="flex items-center space-x-2 cursor-pointer"
          @click="navigateTo(userRole === 'assessor' ? 'children' : 'edit-checklist')"
        >
          <div class="text-2xl font-bold text-primary">UNAWA</div>
          <div class="text-sm text-secondary">Child Development Assessment System</div>
        </div>

        <!-- Navigation Links (Conditional by Role) -->
        <nav class="hidden md:flex space-x-6">
          <!-- Consultant-Only Links -->
          <template v-if="userRole === 'consultant'">
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
          </template>

          <!-- Common Link (Both Roles) -->
          <button
            class="text-sm font-medium hover:text-primary transition-colors"
            @click="navigateTo('children')"
          >
            Child List
          </button>
        </nav>
      </div>

      <!-- Avatar Dropdown (Same for Both Roles) -->
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
          <DropdownMenuContent class="w-56" align="end">
            <DropdownMenuItem @select="handleLogout" class="text-red-600 focus:text-red-600">
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
