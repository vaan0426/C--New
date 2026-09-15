<script setup>
import { onMounted, ref } from 'vue'
import api from '../../lib/api'
import AdminLayout from '../../components/AdminLayout.vue'

const users = ref([])
const roleFilter = ref('')
const noteDrafts = ref({})

const roleLabels = { admin: 'Админ', editor: 'Едитор', user: 'Потребител' }

async function load() {
  const { data } = await api.get('/admin/users', { params: roleFilter.value ? { role: roleFilter.value } : {} })
  users.value = data
  for (const u of data) noteDrafts.value[u.id] = u.internal_note || ''
}
onMounted(load)

async function updateRole(u, role) {
  await api.put(`/admin/users/${u.id}`, { role })
  await load()
}

async function toggleFrozen(u) {
  const status = u.status === 'frozen' ? 'active' : 'frozen'
  await api.put(`/admin/users/${u.id}`, { status })
  await load()
}

async function saveNote(u) {
  await api.put(`/admin/users/${u.id}`, { internal_note: noteDrafts.value[u.id] })
}
</script>

<template>
  <AdminLayout>
    <div class="flex items-center justify-between">
      <h1 class="font-display text-2xl italic text-cream">Потребители</h1>
      <select v-model="roleFilter" class="rounded-lg border border-divider bg-surface px-3 py-2 text-sm text-ink" @change="load">
        <option value="">Всички роли</option>
        <option v-for="(label, key) in roleLabels" :key="key" :value="key">{{ label }}</option>
      </select>
    </div>

    <div class="mt-8 flex flex-col gap-4">
      <div v-for="u in users" :key="u.id" class="rounded-xl border border-divider bg-surface p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-cream">{{ u.name }}</p>
            <p class="text-xs text-ink-muted">{{ u.email }} · {{ u.phone || '—' }}</p>
          </div>
          <div class="flex items-center gap-3 text-sm">
            <select :value="u.role" class="rounded-lg border border-divider bg-bg px-3 py-2 text-ink" @change="updateRole(u, $event.target.value)">
              <option v-for="(label, key) in roleLabels" :key="key" :value="key">{{ label }}</option>
            </select>
            <button
              class="rounded-full border px-3 py-1.5 text-xs"
              :class="u.status === 'frozen' ? 'border-amber-700 text-amber-500' : 'border-divider text-ink-muted hover:text-cream'"
              @click="toggleFrozen(u)"
            >
              {{ u.status === 'frozen' ? 'Замразен' : 'Активен' }}
            </button>
          </div>
        </div>
        <div class="mt-4 flex items-center gap-2">
          <input
            v-model="noteDrafts[u.id]"
            placeholder="Вътрешна бележка (не се вижда във frontend)"
            class="w-full rounded-lg border border-divider bg-bg px-3 py-2 text-sm text-ink"
          />
          <button class="shrink-0 rounded-full border border-divider px-3 py-2 text-xs text-ink-muted hover:text-cream" @click="saveNote(u)">
            Запази
          </button>
        </div>
      </div>
      <p v-if="!users.length" class="text-sm text-ink-muted">Няма намерени потребители.</p>
    </div>
  </AdminLayout>
</template>
