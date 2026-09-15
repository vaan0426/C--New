<script setup>
import { onMounted, ref } from 'vue'
import api from '../../lib/api'
import AdminLayout from '../../components/AdminLayout.vue'

const bookings = ref([])
const statusFilter = ref('')

const statusLabels = {
  pending: 'Чака одобрение',
  confirmed: 'Приета',
  rejected: 'Отхвърлена',
  rescheduled: 'Преместена',
}

async function load() {
  const { data } = await api.get('/admin/bookings', {
    params: statusFilter.value ? { status: statusFilter.value } : {},
  })
  bookings.value = data
}

onMounted(load)

async function confirm(booking) {
  await api.post(`/admin/bookings/${booking.id}/confirm`)
  await load()
}

async function reject(booking) {
  await api.post(`/admin/bookings/${booking.id}/reject`)
  await load()
}
</script>

<template>
  <AdminLayout>
    <div class="flex items-center justify-between">
      <h1 class="font-display text-2xl italic text-cream">Резервации</h1>
      <select
        v-model="statusFilter"
        class="rounded-lg border border-divider bg-surface px-3 py-2 text-sm text-ink"
        @change="load"
      >
        <option value="">Всички статуси</option>
        <option v-for="(label, key) in statusLabels" :key="key" :value="key">{{ label }}</option>
      </select>
    </div>

    <div class="mt-8 overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="text-xs uppercase tracking-wide text-ink-muted">
          <tr class="border-b border-divider">
            <th class="py-3 pr-4">Клиент</th>
            <th class="py-3 pr-4">Тип помощ</th>
            <th class="py-3 pr-4">Час</th>
            <th class="py-3 pr-4">Статус</th>
            <th class="py-3 pr-4">Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="b in bookings" :key="b.id" class="border-b border-divider/60">
            <td class="py-3 pr-4">
              <div class="text-cream">{{ b.user?.name }}</div>
              <div class="text-xs text-ink-muted">{{ b.user?.email }}</div>
            </td>
            <td class="py-3 pr-4 text-ink-muted">{{ b.help_type?.name }}</td>
            <td class="py-3 pr-4 text-ink-muted">
              {{ new Date(b.slot?.date).toLocaleDateString('bg-BG') }} · {{ b.slot?.start_time }}
            </td>
            <td class="py-3 pr-4">
              <span class="rounded-full border border-divider px-3 py-1 text-xs text-ink-muted">
                {{ statusLabels[b.status] }}
              </span>
            </td>
            <td class="flex gap-2 py-3 pr-4">
              <button
                v-if="b.status === 'pending'"
                class="rounded-full bg-accent px-3 py-1 text-xs font-medium text-bg"
                @click="confirm(b)"
              >
                Потвърди
              </button>
              <button
                v-if="b.status === 'pending'"
                class="rounded-full border border-divider px-3 py-1 text-xs text-ink-muted hover:text-cream"
                @click="reject(b)"
              >
                Отхвърли
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>
