<script setup>
import { computed, onMounted, ref } from 'vue'
import api from '../lib/api'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()

const helpTypes = ref([])
const slots = ref([])
const selectedHelpType = ref(null)
const selectedSlot = ref(null)
const guest = ref({ name: '', email: '', phone: '' })
const submitting = ref(false)
const error = ref('')
const done = ref(false)

onMounted(async () => {
  const [ht, sl] = await Promise.all([api.get('/help-types'), api.get('/slots')])
  helpTypes.value = ht.data
  slots.value = sl.data
})

const slotsByDate = computed(() => {
  const groups = {}
  for (const slot of slots.value) {
    const date = slot.date.slice(0, 10)
    groups[date] ??= []
    groups[date].push(slot)
  }
  return groups
})

function formatDate(date) {
  return new Date(date).toLocaleDateString('bg-BG', { weekday: 'short', day: 'numeric', month: 'short' })
}

async function submit() {
  error.value = ''
  submitting.value = true
  try {
    const payload = {
      slot_id: selectedSlot.value.id,
      help_type_id: selectedHelpType.value.id,
    }
    if (!auth.isAuthenticated) {
      Object.assign(payload, guest.value)
    }
    await api.post('/bookings', payload)
    done.value = true
  } catch (e) {
    error.value = e.response?.data?.message || 'Възникна грешка при заявката.'
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <div class="mx-auto max-w-3xl px-6 py-20">
    <h1 class="font-display text-3xl italic text-cream">Запази час</h1>

    <div v-if="done" class="mt-10 rounded-xl border border-accent/50 bg-surface p-8 text-center">
      <p class="font-display text-xl italic text-cream">Заявката е приета.</p>
      <p class="mt-3 text-sm text-ink-muted">
        Статус: чака одобрение. Ще получиш потвърждение, след като психологът прегледа заявката.
      </p>
    </div>

    <form v-else class="mt-10 flex flex-col gap-10" @submit.prevent="submit">
      <div>
        <h2 class="text-sm font-medium uppercase tracking-widest text-ink-muted">1. Избери тип помощ</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-2">
          <button
            v-for="ht in helpTypes"
            :key="ht.id"
            type="button"
            class="rounded-xl border px-4 py-3 text-left transition"
            :class="selectedHelpType?.id === ht.id ? 'border-accent bg-bg' : 'border-divider bg-surface hover:border-accent/50'"
            @click="selectedHelpType = ht"
          >
            <span class="block text-sm text-cream">{{ ht.name }}</span>
            <span class="text-xs text-ink-muted">{{ ht.base_price }} €</span>
          </button>
        </div>
      </div>

      <div v-if="selectedHelpType">
        <h2 class="text-sm font-medium uppercase tracking-widest text-ink-muted">2. Избери час</h2>
        <div v-for="(daySlots, date) in slotsByDate" :key="date" class="mt-5">
          <p class="text-xs uppercase tracking-wide text-ink-muted">{{ formatDate(date) }}</p>
          <div class="mt-2 flex flex-wrap gap-2">
            <button
              v-for="slot in daySlots"
              :key="slot.id"
              type="button"
              class="rounded-full border px-4 py-2 text-sm transition"
              :class="selectedSlot?.id === slot.id ? 'border-accent bg-accent text-bg' : 'border-divider text-ink-muted hover:border-accent/50'"
              @click="selectedSlot = slot"
            >
              {{ slot.start_time }}
            </button>
          </div>
        </div>
      </div>

      <div v-if="selectedSlot && !auth.isAuthenticated">
        <h2 class="text-sm font-medium uppercase tracking-widest text-ink-muted">3. Твоите данни</h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
          <input v-model="guest.name" placeholder="Име" required class="rounded-lg border border-divider bg-surface px-4 py-3 text-ink outline-none focus:border-accent" />
          <input v-model="guest.email" type="email" placeholder="Имейл" required class="rounded-lg border border-divider bg-surface px-4 py-3 text-ink outline-none focus:border-accent" />
          <input v-model="guest.phone" placeholder="Телефон" class="rounded-lg border border-divider bg-surface px-4 py-3 text-ink outline-none focus:border-accent sm:col-span-2" />
        </div>
      </div>

      <p v-if="error" class="text-sm text-red-400">{{ error }}</p>

      <button
        v-if="selectedSlot"
        type="submit"
        :disabled="submitting"
        class="w-fit rounded-full bg-accent px-8 py-3 text-sm font-medium text-bg transition hover:brightness-110 disabled:opacity-60"
      >
        {{ submitting ? 'Изпращане…' : 'Изпрати заявка' }}
      </button>
    </form>
  </div>
</template>
