<script setup>
import { RouterView, useRoute } from 'vue-router'
import { useAuthStore } from './stores/auth'
import SiteHeader from './components/SiteHeader.vue'
import SiteFooter from './components/SiteFooter.vue'

const auth = useAuthStore()
const route = useRoute()
</script>

<template>
  <div class="flex min-h-screen flex-col bg-bg text-ink">
    <SiteHeader v-if="!route.meta.requiresStaff" :auth="auth" />
    <main class="flex-1" :class="{ 'pt-20': !route.meta.requiresStaff && route.name !== 'home' }">
      <RouterView />
    </main>
    <SiteFooter v-if="!route.meta.requiresStaff" />
  </div>
</template>
