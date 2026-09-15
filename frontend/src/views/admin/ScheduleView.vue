<script setup>
import { computed, onMounted, ref } from 'vue'
import api from '../../lib/api'
import AdminLayout from '../../components/AdminLayout.vue'
import { useAuthStore } from '../../stores/auth'

const auth = useAuthStore()
const slots = ref([])
const editors = ref([])
const form = ref({ editor_id: '', date: '', start_time: '', end_time: '' })
const error = ref('')
const saving = ref(false)

async function load() {
  const { data } = await api.get('/admin/slots')
  slots.value = data
}

onMounted(async () => {
  await load()
  if (auth.isAdmin) {
    const { data } = await api.get('/admin/users', { params: { role: 'editor' } })
    editors.value = data
  } else {
    form.value.editor_id = auth.user?.id
  }
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

async function createSlot() {
  error.value = ''
  saving.value = true
  try {
    await api.post('/admin/slots', form.value)
    form.value.date = ''
    form.value.start_time = ''
    form.value.end_time = ''
    await load()
  } catch (e) {
    error.value = e.response?.data?.message || 'Възникна грешка.'
  } finally {
    saving.value = false
  }
}

async function removeSlot(slot) {
  if (slot.is_booked) return
  await api.delete(`/admin/slots/${slot.id}`)
  await load()
}
</script>

<template>
  <AdminLayout>
    <h1 class="font-display text-2xl italic text-cream">График / Слотове</h1>

    <form class="mt-8 flex flex-wrap items-end gap-4 rounded-xl border border-divider bg-surface p-5" @submit.prevent="createSlot">
      <label v-if="auth.isAdmin" class="flex flex-col gap-1 text-xs text-ink-muted">
        Психолог
        <select v-model="form.editor_id" required class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink">
          <option value="" disabled>Избери</option>
          <option v-for="e in editors" :key="e.id" :value="e.id">{{ e.name }}</option>
        </select>
      </label>
      <label class="flex flex-col gap-1 text-xs text-ink-muted">
        Дата
        <input v-model="form.date" type="date" required class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink" />
      </label>
      <label class="flex flex-col gap-1 text-xs text-ink-muted">
        Начало
        <input v-model="form.start_time" type="time" required class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink" />
      </label>
      <label class="flex flex-col gap-1 text-xs text-ink-muted">
        Край
        <input v-model="form.end_time" type="time" required class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink" />
      </label>
      <button type="submit" :disabled="saving" class="rounded-full bg-accent px-5 py-2 text-sm font-medium text-bg disabled:opacity-60">
        {{ saving ? 'Запазване…' : 'Добави час' }}
      </button>
      <p v-if="error" class="w-full text-sm text-red-400">{{ error }}</p>
    </form>

    <div class="mt-8 flex flex-col gap-6">
      <div v-for="(daySlots, date) in slotsByDate" :key="date">
        <p class="text-xs uppercase tracking-wide text-ink-muted">
          {{ new Date(date).toLocaleDateString('bg-BG', { weekday: 'long', day: 'numeric', month: 'long' }) }}
        </p>
        <div class="mt-2 flex flex-wrap gap-2">
          <span
            v-for="slot in daySlots"
            :key="slot.id"
            class="group flex items-center gap-2 rounded-full border px-4 py-2 text-sm"
            :class="slot.is_booked ? 'border-divider text-ink-muted' : 'border-accent/60 text-cream'"
          >
            {{ slot.start_time }}–{{ slot.end_time }} · {{ slot.editor?.name }}
            <span v-if="slot.is_booked" class="text-xs text-ink-muted">(зает)</span>
            <button v-else class="text-ink-muted hover:text-red-400" title="Изтрий" @click="removeSlot(slot)">×</button>
          </span>
        </div>
      </div>
      <p v-if="!slots.length" class="text-sm text-ink-muted">Няма добавени часове.</p>
    </div>
  </AdminLayout>
</template>
