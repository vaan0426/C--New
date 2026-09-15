<script setup>
import { onMounted, ref } from 'vue'
import api from '../../lib/api'
import AdminLayout from '../../components/AdminLayout.vue'

const pages = ref([])
const form = ref(emptyForm())
const editingId = ref(null)
const error = ref('')
const saving = ref(false)

function emptyForm() {
  return { title: '', content: '', is_published: true }
}

async function load() {
  const { data } = await api.get('/admin/pages')
  pages.value = data
}
onMounted(load)

function edit(p) {
  editingId.value = p.id
  form.value = { title: p.title, content: p.content || '', is_published: p.is_published }
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
      await api.put(`/admin/pages/${editingId.value}`, form.value)
    } else {
      await api.post('/admin/pages', form.value)
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
  await api.delete(`/admin/pages/${p.id}`)
  if (editingId.value === p.id) cancelEdit()
  await load()
}
</script>

<template>
  <AdminLayout>
    <h1 class="font-display text-2xl italic text-cream">Страници</h1>

    <form class="mt-8 flex flex-col gap-4 rounded-xl border border-divider bg-surface p-5" @submit.prevent="save">
      <input v-model="form.title" placeholder="Заглавие" required class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink" />
      <textarea v-model="form.content" placeholder="Съдържание" rows="5" class="rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink" />
      <label class="flex items-center gap-2 text-sm text-ink-muted"><input v-model="form.is_published" type="checkbox" /> Публикувана</label>
      <div class="flex items-center gap-3">
        <button type="submit" :disabled="saving" class="rounded-full bg-accent px-5 py-2 text-sm font-medium text-bg disabled:opacity-60">
          {{ editingId ? 'Запази промените' : 'Добави страница' }}
        </button>
        <button v-if="editingId" type="button" class="text-sm text-ink-muted hover:text-cream" @click="cancelEdit">Отказ</button>
      </div>
      <p v-if="error" class="text-sm text-red-400">{{ error }}</p>
    </form>

    <div class="mt-8 overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="text-xs uppercase tracking-wide text-ink-muted">
          <tr class="border-b border-divider">
            <th class="py-3 pr-4">Заглавие</th>
            <th class="py-3 pr-4">Slug</th>
            <th class="py-3 pr-4">Статус</th>
            <th class="py-3 pr-4">Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in pages" :key="p.id" class="border-b border-divider/60">
            <td class="py-3 pr-4 text-cream">{{ p.title }}</td>
            <td class="py-3 pr-4 text-ink-muted">{{ p.slug }}</td>
            <td class="py-3 pr-4 text-ink-muted">{{ p.is_published ? 'публикувана' : 'чернова' }}</td>
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
