<script setup>
import { RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()

const links = [
  { to: '/admin', label: 'Табло' },
  { to: '/admin/bookings', label: 'Резервации' },
]
</script>

<template>
  <div class="flex min-h-screen bg-bg text-ink">
    <aside class="hidden w-56 shrink-0 border-r border-divider bg-surface p-6 md:block">
      <p class="font-display text-lg italic text-cream">Админ панел</p>
      <nav class="mt-8 flex flex-col gap-3 text-sm">
        <RouterLink v-for="l in links" :key="l.to" :to="l.to" class="text-ink-muted hover:text-cream">
          {{ l.label }}
        </RouterLink>
      </nav>
      <button class="mt-10 text-sm text-ink-muted hover:text-cream" @click="auth.logout()">Изход</button>
    </aside>
    <div class="flex-1 p-8">
      <slot />
    </div>
  </div>
</template>
