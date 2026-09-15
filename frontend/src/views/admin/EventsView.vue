<script setup>
import { onMounted, ref } from 'vue'
import api from '../../lib/api'
import AdminLayout from '../../components/AdminLayout.vue'
import { useAuthStore } from '../../stores/auth'

const auth = useAuthStore()
const events = ref([])
const editors = ref([])
const helpTypes = ref([])
const form = ref(emptyForm())
const editingId = ref(null)
const error = ref('')
const saving = ref(false)

function emptyForm() {
  return {
    editor_id: auth.isAdmin ? '' : auth.user?.id,
    help_type_id: '',
    title: '',
    description: '',
    starts_at: '',
    price: 0,
    capacity: 10,
    is_active: true,
  }
}

async function load() {
  const { data } = await api.get('/admin/events')
  events.value = data
}

onMounted(async () => {
  await load()
  const ht = await api.get('/help-types')
  helpTypes.value = ht.data
  if (auth.isAdmin) {
    const { data } = await api.get('/admin/users', { params: { role: 'editor' } })
    editors.value = data
  }
})

function edit(e) {
  editingId.value = e.id
  form.value = {
    editor_id: e.editor_id,
    help_type_id: e.help_type_id || '',
    title: e.title,
    description: e.description || '',
    starts_at: e.starts_at?.slice(0, 16),
    price: e.price,
    capacity: e.capacity,
    is_active: e.is_active,
  }
}

function cancelEdit() {
  editingId.value = null
  form.value = emptyForm()
}

async function save() {
  error.value = ''
  saving.value = true
  try {
    if (editingId.value) {
      await api.put(`/admin/events/${editingId.value}`, form.value)
    } else {
      await api.post('/admin/events', form.value)
    }
    cancelEdit()
    await load()
  } catch (e) {
    error.value = e.response?.data?.message || 'Възникна грешка.'
  } finally {
    saving.value = false
  }
}

async function remove(e) {
  await api.delete(`/admin/events/${e.id}`)
  if (editingId.value === e.id) cancelEdit()
  await load()
}
</script>

<template>
  <AdminLayout>
    <h1 class="font-display text-2xl italic text-cream">Събития / Курсове</h1>

    <form class="mt-8 grid gap-4 rounded-xl border border-divider bg-surface p-5 sm:grid-cols-2 lg:grid-cols-3" @submit.prevent="save">
      <input v-model="form.title" placeholder="Заглавие" required class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink" />
      <input v-model="form.starts_at" type="datetime-local" required class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink" />
      <select v-if="auth.isAdmin" v-model="form.editor_id" required class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink">
        <option value="" disabled>Психолог</option>
        <option v-for="e in editors" :key="e.id" :value="e.id">{{ e.name }}</option>
      </select>
      <select v-model="form.help_type_id" class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink">
        <option value="">Тип помощ (по избор)</option>
        <option v-for="ht in helpTypes" :key="ht.id" :value="ht.id">{{ ht.name }}</option>
      </select>
      <input v-model.number="form.price" type="number" min="0" step="0.01" placeholder="Цена (€)" required class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink" />
      <input v-model.number="form.capacity" type="number" min="1" placeholder="Капацитет" required class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink" />
      <input v-model="form.description" placeholder="Описание" class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink sm:col-span-2 lg:col-span-3" />
      <label class="flex items-center gap-2 text-sm text-ink-muted"><input v-model="form.is_active" type="checkbox" /> Активно</label>
      <div class="flex items-center gap-3 lg:col-span-3">
        <button type="submit" :disabled="saving" class="rounded-full bg-accent px-5 py-2 text-sm font-medium text-bg disabled:opacity-60">
          {{ editingId ? 'Запази промените' : 'Добави събитие' }}
        </button>
        <button v-if="editingId" type="button" class="text-sm text-ink-muted hover:text-cream" @click="cancelEdit">Отказ</button>
      </div>
      <p v-if="error" class="text-sm text-red-400 lg:col-span-3">{{ error }}</p>
    </form>

    <div class="mt-8 overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="text-xs uppercase tracking-wide text-ink-muted">
          <tr class="border-b border-divider">
            <th class="py-3 pr-4">Заглавие</th>
            <th class="py-3 pr-4">Дата</th>
            <th class="py-3 pr-4">Психолог</th>
            <th class="py-3 pr-4">Места</th>
            <th class="py-3 pr-4">Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="e in events" :key="e.id" class="border-b border-divider/60">
            <td class="py-3 pr-4 text-cream">{{ e.title }}</td>
            <td class="py-3 pr-4 text-ink-muted">{{ new Date(e.starts_at).toLocaleString('bg-BG') }}</td>
            <td class="py-3 pr-4 text-ink-muted">{{ e.editor?.name }}</td>
            <td class="py-3 pr-4 text-ink-muted">{{ e.spots_taken }}/{{ e.capacity }}</td>
            <td class="flex gap-3 py-3 pr-4">
              <button class="text-accent hover:underline" @click="edit(e)">Редакция</button>
              <button class="text-ink-muted hover:text-red-400" @click="remove(e)">Изтрий</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>
