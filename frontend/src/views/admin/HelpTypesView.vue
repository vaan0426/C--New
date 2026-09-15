<script setup>
import { onMounted, ref } from 'vue'
import api from '../../lib/api'
import AdminLayout from '../../components/AdminLayout.vue'

const helpTypes = ref([])
const form = ref(emptyForm())
const editingId = ref(null)
const error = ref('')
const saving = ref(false)

function emptyForm() {
  return { name: '', description: '', base_price: 0, is_active: true }
}

async function load() {
  const { data } = await api.get('/admin/help-types')
  helpTypes.value = data
}
onMounted(load)

function edit(ht) {
  editingId.value = ht.id
  form.value = { name: ht.name, description: ht.description || '', base_price: ht.base_price, is_active: ht.is_active }
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
      await api.put(`/admin/help-types/${editingId.value}`, form.value)
    } else {
      await api.post('/admin/help-types', form.value)
    }
    cancelEdit()
    await load()
  } catch (e) {
    error.value = e.response?.data?.message || 'Възникна грешка.'
  } finally {
    saving.value = false
  }
}

async function remove(ht) {
  await api.delete(`/admin/help-types/${ht.id}`)
  if (editingId.value === ht.id) cancelEdit()
  await load()
}
</script>

<template>
  <AdminLayout>
    <h1 class="font-display text-2xl italic text-cream">Тип помощ (таксономия)</h1>

    <form class="mt-8 grid gap-4 rounded-xl border border-divider bg-surface p-5 sm:grid-cols-2 lg:grid-cols-4" @submit.prevent="save">
      <input v-model="form.name" placeholder="Име" required class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink" />
      <input v-model.number="form.base_price" type="number" min="0" step="0.01" placeholder="Базова цена (€)" required class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink" />
      <input v-model="form.description" placeholder="Описание" class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink sm:col-span-2" />
      <label class="flex items-center gap-2 text-sm text-ink-muted"><input v-model="form.is_active" type="checkbox" /> Активен</label>
      <div class="flex items-center gap-3 lg:col-span-4">
        <button type="submit" :disabled="saving" class="rounded-full bg-accent px-5 py-2 text-sm font-medium text-bg disabled:opacity-60">
          {{ editingId ? 'Запази промените' : 'Добави тип помощ' }}
        </button>
        <button v-if="editingId" type="button" class="text-sm text-ink-muted hover:text-cream" @click="cancelEdit">Отказ</button>
      </div>
      <p v-if="error" class="text-sm text-red-400 lg:col-span-4">{{ error }}</p>
    </form>

    <div class="mt-8 overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="text-xs uppercase tracking-wide text-ink-muted">
          <tr class="border-b border-divider">
            <th class="py-3 pr-4">Име</th>
            <th class="py-3 pr-4">Базова цена</th>
            <th class="py-3 pr-4">Статус</th>
            <th class="py-3 pr-4">Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="ht in helpTypes" :key="ht.id" class="border-b border-divider/60">
            <td class="py-3 pr-4 text-cream">{{ ht.name }}</td>
            <td class="py-3 pr-4 text-ink-muted">{{ ht.base_price }} €</td>
            <td class="py-3 pr-4 text-ink-muted">{{ ht.is_active ? 'активен' : 'скрит' }}</td>
            <td class="flex gap-3 py-3 pr-4">
              <button class="text-accent hover:underline" @click="edit(ht)">Редакция</button>
              <button class="text-ink-muted hover:text-red-400" @click="remove(ht)">Изтрий</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>
