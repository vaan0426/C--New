<script setup>
import { onMounted, ref } from 'vue'
import api from '../../lib/api'
import AdminLayout from '../../components/AdminLayout.vue'

const packages = ref([])
const form = ref(emptyForm())
const editingId = ref(null)
const error = ref('')
const saving = ref(false)

function emptyForm() {
  return { name: '', description: '', sessions_count: 1, price: 0, validity_days: '', is_featured: false, is_active: true }
}

async function load() {
  const { data } = await api.get('/admin/packages')
  packages.value = data
}
onMounted(load)

function edit(p) {
  editingId.value = p.id
  form.value = {
    name: p.name,
    description: p.description || '',
    sessions_count: p.sessions_count,
    price: p.price,
    validity_days: p.validity_days || '',
    is_featured: p.is_featured,
    is_active: p.is_active,
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
      await api.put(`/admin/packages/${editingId.value}`, form.value)
    } else {
      await api.post('/admin/packages', form.value)
    }
    cancelEdit()
    await load()
  } catch (e) {
    error.value = e.response?.data?.message || 'Възникна грешка.'
  } finally {
    saving.value = false
  }
}

async function remove(p) {
  await api.delete(`/admin/packages/${p.id}`)
  if (editingId.value === p.id) cancelEdit()
  await load()
}
</script>

<template>
  <AdminLayout>
    <h1 class="font-display text-2xl italic text-cream">Пакети</h1>

    <form class="mt-8 grid gap-4 rounded-xl border border-divider bg-surface p-5 sm:grid-cols-2 lg:grid-cols-3" @submit.prevent="save">
      <input v-model="form.name" placeholder="Име" required class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink" />
      <input v-model.number="form.sessions_count" type="number" min="1" placeholder="Брой сесии" required class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink" />
      <input v-model.number="form.price" type="number" min="0" step="0.01" placeholder="Цена (€)" required class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink" />
      <input v-model.number="form.validity_days" type="number" min="1" placeholder="Валидност (дни)" class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink" />
      <input v-model="form.description" placeholder="Описание" class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink sm:col-span-2" />
      <div class="flex items-center gap-4 text-sm text-ink-muted">
        <label class="flex items-center gap-2"><input v-model="form.is_featured" type="checkbox" /> Изтъкнат</label>
        <label class="flex items-center gap-2"><input v-model="form.is_active" type="checkbox" /> Активен</label>
      </div>
      <div class="flex items-center gap-3 lg:col-span-3">
        <button type="submit" :disabled="saving" class="rounded-full bg-accent px-5 py-2 text-sm font-medium text-bg disabled:opacity-60">
          {{ editingId ? 'Запази промените' : 'Добави пакет' }}
        </button>
        <button v-if="editingId" type="button" class="text-sm text-ink-muted hover:text-cream" @click="cancelEdit">Отказ</button>
      </div>
      <p v-if="error" class="text-sm text-red-400 lg:col-span-3">{{ error }}</p>
    </form>

    <div class="mt-8 overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="text-xs uppercase tracking-wide text-ink-muted">
          <tr class="border-b border-divider">
            <th class="py-3 pr-4">Име</th>
            <th class="py-3 pr-4">Сесии</th>
            <th class="py-3 pr-4">Цена</th>
            <th class="py-3 pr-4">Статус</th>
            <th class="py-3 pr-4">Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in packages" :key="p.id" class="border-b border-divider/60">
            <td class="py-3 pr-4 text-cream">{{ p.name }} <span v-if="p.is_featured" class="text-xs text-accent">★</span></td>
            <td class="py-3 pr-4 text-ink-muted">{{ p.sessions_count }}</td>
            <td class="py-3 pr-4 text-ink-muted">{{ p.price }} €</td>
            <td class="py-3 pr-4 text-ink-muted">{{ p.is_active ? 'активен' : 'скрит' }}</td>
            <td class="flex gap-3 py-3 pr-4">
              <button class="text-accent hover:underline" @click="edit(p)">Редакция</button>
              <button class="text-ink-muted hover:text-red-400" @click="remove(p)">Изтрий</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>
