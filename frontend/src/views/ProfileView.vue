<script setup>
import { onMounted, ref } from 'vue'
import api from '../lib/api'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const bookings = ref([])
const packages = ref([])
const tickets = ref([])
const tab = ref('bookings')

const statusLabels = {
  pending: 'Чака одобрение',
  confirmed: 'Приета',
  rejected: 'Отхвърлена',
  rescheduled: 'Преместена',
}

onMounted(async () => {
  const [b, p, t] = await Promise.all([
    api.get('/my-bookings'),
    api.get('/my-packages'),
    api.get('/my-tickets'),
  ])
  bookings.value = b.data
  packages.value = p.data
  tickets.value = t.data
})

function formatDateTime(slot) {
  if (!slot) return ''
  return `${new Date(slot.date).toLocaleDateString('bg-BG')} · ${slot.start_time}`
}
</script>

<template>
  <div class="mx-auto max-w-4xl px-6 py-16">
    <div class="flex items-center justify-between">
      <h1 class="font-display text-3xl italic text-cream">Здравей, {{ auth.user?.name }}</h1>
      <button class="text-sm text-ink-muted hover:text-cream" @click="auth.logout()">Изход</button>
    </div>

    <div v-if="auth.user?.status === 'frozen'" class="mt-6 rounded-xl border border-amber-700/50 bg-surface p-4 text-sm text-ink-muted">
      Профилът е замразен — само за преглед, без нови заявки.
    </div>

    <nav class="mt-10 flex gap-6 border-b border-divider text-sm">
      <button
        v-for="t in [['bookings', 'Резервации'], ['packages', 'Пакети'], ['tickets', 'Събития']]"
        :key="t[0]"
        class="border-b-2 pb-3 transition"
        :class="tab === t[0] ? 'border-accent text-cream' : 'border-transparent text-ink-muted hover:text-cream'"
        @click="tab = t[0]"
      >
        {{ t[1] }}
      </button>
    </nav>

    <div v-if="tab === 'bookings'" class="mt-8 flex flex-col gap-4">
      <p v-if="!bookings.length" class="text-sm text-ink-muted">Все още нямаш резервации.</p>
      <div v-for="b in bookings" :key="b.id" class="rounded-xl border border-divider bg-surface p-5">
        <div class="flex items-center justify-between">
          <span class="font-display italic text-cream">{{ b.help_type?.name }}</span>
          <span class="rounded-full border border-divider px-3 py-1 text-xs text-ink-muted">{{ statusLabels[b.status] }}</span>
        </div>
        <p class="mt-2 text-sm text-ink-muted">{{ formatDateTime(b.slot) }} — {{ b.slot?.editor?.name }}</p>
        <p v-if="b.public_note" class="mt-2 text-sm text-ink-muted">Бележка: {{ b.public_note }}</p>
      </div>
    </div>

    <div v-else-if="tab === 'packages'" class="mt-8 flex flex-col gap-4">
      <p v-if="!packages.length" class="text-sm text-ink-muted">Нямаш закупени пакети.</p>
      <div v-for="p in packages" :key="p.id" class="rounded-xl border border-divider bg-surface p-5">
        <div class="flex items-center justify-between">
          <span class="font-display italic text-cream">{{ p.package?.name }}</span>
          <span class="text-sm text-accent">{{ p.sessions_remaining }} часа остават</span>
        </div>
        <p class="mt-2 text-xs uppercase tracking-wide text-ink-muted">{{ p.status }}</p>
      </div>
    </div>

    <div v-else class="mt-8 flex flex-col gap-4">
      <p v-if="!tickets.length" class="text-sm text-ink-muted">Нямаш закупени билети за събития.</p>
      <div v-for="t in tickets" :key="t.id" class="rounded-xl border border-divider bg-surface p-5">
        <div class="flex items-center justify-between">
          <span class="font-display italic text-cream">{{ t.event?.title }}</span>
          <span class="text-sm text-ink-muted">x{{ t.quantity }}</span>
        </div>
      </div>
    </div>
  </div>
</template>
