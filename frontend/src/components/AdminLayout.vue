<script setup>
import { RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()

const links = [
  { to: '/admin', label: 'Табло' },
  { to: '/admin/bookings', label: 'Резервации' },
  { to: '/admin/schedule', label: 'График / Слотове' },
  { to: '/admin/packages', label: 'Пакети' },
  { to: '/admin/help-types', label: 'Тип помощ' },
  { to: '/admin/events', label: 'Събития/Курсове' },
  { to: '/admin/pages', label: 'Страници' },
  { to: '/admin/users', label: 'Потребители', adminOnly: true },
  { to: '/admin/settings', label: 'Настройки' },
]
</script>

<template>
  <div class="flex min-h-screen bg-bg text-ink">
    <aside class="hidden w-56 shrink-0 border-r border-divider bg-surface p-6 md:block">
      <p class="font-display text-lg italic text-cream">Админ панел</p>
      <nav class="mt-8 flex flex-col gap-3 text-sm">
        <template v-for="l in links" :key="l.to">
          <RouterLink v-if="!l.adminOnly || auth.isAdmin" :to="l.to" class="text-ink-muted hover:text-cream">
            {{ l.label }}
          </RouterLink>
        </template>
      </nav>
      <button class="mt-10 text-sm text-ink-muted hover:text-cream" @click="auth.logout()">Изход</button>
    </aside>
    <div class="flex-1 overflow-x-auto p-8">
      <slot />
    </div>
  </div>
</template>
