<script setup lang="ts">
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { toast } from 'vue-sonner'

const router = useRouter()
const form = ref({
  email: '',
  password: ''
})

const isLoading = ref(false)
const errors = ref<Record<string, string>>({})

const handleLogin = async () => {
  try {
    isLoading.value = true
    errors.value = {}

    const response = await axios.post('http://localhost:8000/api/login', {
      email: form.value.email,
      password: form.value.password,
      device_name: 'web-browser' 
    })

    localStorage.setItem('auth_token', response.data.token)
    localStorage.setItem('user_role', response.data.user.role) 
    
    axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`

    toast.success('Login successful')
    
    if (response.data.user.role === 'assessor') {
      router.push({ name: 'checklist' })
    } else if (response.data.user.role === 'consultant') {
      router.push({ name: 'edit-checklist' })
    } else {
      router.push('/')
    }
  } catch (error: any) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors
    } else {
      toast.error(error.response?.data?.message || 'Login failed')
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="w-full min-h-screen lg:grid lg:grid-cols-2 font-display">
    <div class="flex items-center justify-center py-12">
      <div class="mx-auto grid w-[350px] gap-6">
        <div class="grid gap-2 text-center">
          <h1 class="text-3xl font-bold">Login</h1>
          <p class="text-balance text-muted-foreground">
            Enter your email below to login to your account
          </p>
        </div>
        <form @submit.prevent="handleLogin" class="grid gap-4">
          <div class="grid gap-2">
            <Label for="email">Email</Label>
            <Input
              id="email"
              type="email"
              placeholder="m@example.com"
              required
              v-model="form.email"
              :class="{ 'border-destructive': errors.email }"
            />
            <p v-if="errors.email" class="text-sm text-destructive">
              {{ errors.email[0] }}
            </p>
          </div>
          <div class="grid gap-2">
            <div class="flex items-center">
              <Label for="password">Password</Label>
              <a
                href="/forgot-password"
                class="ml-auto inline-block text-sm underline"
              >
                Forgot your password?
              </a>
            </div>
            <Input 
              id="password" 
              type="password" 
              required 
              v-model="form.password"
              :class="{ 'border-destructive': errors.password }"
            />
            <p v-if="errors.password" class="text-sm text-destructive">
              {{ errors.password[0] }}
            </p>
          </div>
          <Button type="submit" class="w-full" :disabled="isLoading">
            <span v-if="isLoading">Logging in...</span>
            <span v-else>Login</span>
          </Button>
          <Button variant="outline" class="w-full" type="button">
            Login with Google
          </Button>
        </form>
        <div class="mt-4 text-center text-sm">
          Don't have an account?
          <router-link to="/register" class="underline">
            Sign up
          </router-link>
        </div>
      </div>
    </div>
    <div class="hidden bg-muted lg:block">
      <img
        src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='1920' height='1080'%3E%3Crect width='100%25' height='100%25' fill='%23e5e7eb'/%3E%3Ctext x='50%25' y='50%25' fill='%239ca3af' font-size='40' font-family='Arial' dy='.3em' text-anchor='middle'%3EPlaceholder%3C/text%3E%3C/svg%3E"
        alt="Placeholder"
        width="1920"
        height="1080"
        class="h-full w-full object-cover dark:brightness-[0.2] dark:grayscale"
      />
    </div>
  </div>
</template>