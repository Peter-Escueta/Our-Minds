<script setup lang="ts">
import { RouterView } from 'vue-router'
import Header from '@/components/Header.vue'
import { useRoute } from 'vue-router'
import { computed, onMounted } from 'vue'
import { useAuth } from '@/composables/useAuth'

const route = useRoute()
const { initializeUser } = useAuth()
const showLayout = computed(() => {
  return route.name !== 'home' && route.name !== 'login'
})
onMounted(() => {
  initializeUser()
})
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <Header v-if="showLayout" />
    <div
      v-if="showLayout"
      class="flex-1 border-l border-r border-gray-200 mx-auto w-full max-w-[90rem]"
    >
      <main class="container mx-auto font-display py-8 px-20 h-full">
        <RouterView />
      </main>
    </div>

    <RouterView v-else />
  </div>
</template>
