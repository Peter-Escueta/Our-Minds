<script setup lang="ts">
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useAuth } from '@/composables/useAuth'

const { login, isLoading, errors } = useAuth()

const form = ref({
  email: '',
  password: '',
})

const handleLogin = async () => {
  try {
    await login(form.value.email, form.value.password)
  } catch (error) {
    console.error('Login failed:', error)
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
              {{ errors.email }}
            </p>
          </div>
          <div class="grid gap-2">
            <div class="flex items-center">
              <Label for="password">Password</Label>
            </div>
            <Input
              id="password"
              type="password"
              required
              v-model="form.password"
              :class="{ 'border-destructive': errors.password }"
            />
            <p v-if="errors.password" class="text-sm text-destructive">
              {{ errors.password }}
            </p>
          </div>
          <Button type="submit" class="w-full" :disabled="isLoading">
            <span v-if="isLoading">Logging in...</span>
            <span v-else>Login</span>
          </Button>
          <p v-if="errors.general" class="text-sm text-destructive text-center">
            {{ errors.general }}
          </p>
        </form>
      </div>
    </div>
    <div class="hidden lg:flex items-center justify-center bg-muted">
      <img
        src="/images/our-minds-logo.png"
        alt="Our Minds Logo"
        class="max-h-[300px] w-auto object-contain bg-muted"
      />
    </div>
  </div>
</template>
