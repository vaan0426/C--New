<script setup>
import { onMounted, ref } from 'vue'
import api from '../../lib/api'
import AdminLayout from '../../components/AdminLayout.vue'

const stats = ref(null)

onMounted(async () => {
  const { data } = await api.get('/admin/dashboard')
  stats.value = data
})
</script>

<template>
  <AdminLayout>
    <h1 class="font-display text-2xl italic text-cream">Табло</h1>
    <div v-if="stats" class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
      <div class="rounded-xl border border-divider bg-surface p-5">
        <p class="text-xs uppercase tracking-wide text-ink-muted">Чакащи заявки</p>
        <p class="mt-2 text-2xl text-cream">{{ stats.pending_bookings }}</p>
      </div>
      <div class="rounded-xl border border-divider bg-surface p-5">
        <p class="text-xs uppercase tracking-wide text-ink-muted">Свободни часове</p>
        <p class="mt-2 text-2xl text-cream">{{ stats.upcoming_slots }}</p>
      </div>
      <div class="rounded-xl border border-divider bg-surface p-5">
        <p class="text-xs uppercase tracking-wide text-ink-muted">Предстоящи събития</p>
        <p class="mt-2 text-2xl text-cream">{{ stats.upcoming_events }}</p>
      </div>
      <div class="rounded-xl border border-divider bg-surface p-5">
        <p class="text-xs uppercase tracking-wide text-ink-muted">Чакащи плащания</p>
        <p class="mt-2 text-2xl text-cream">{{ stats.payments_pending }}</p>
      </div>
      <div class="rounded-xl border border-divider bg-surface p-5">
        <p class="text-xs uppercase tracking-wide text-ink-muted">Приходи (платени)</p>
        <p class="mt-2 text-2xl text-accent">{{ stats.revenue_paid }} €</p>
      </div>
    </div>
  </AdminLayout>
</template>
