<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()

const form = ref({ name: '', email: '', phone: '', password: '', password_confirmation: '' })
const error = ref('')
const success = ref(false)
const loading = ref(false)

async function submit() {
  error.value = ''
  loading.value = true
  try {
    await auth.register(form.value)
    success.value = true
  } catch (e) {
    const errors = e.response?.data?.errors
    error.value = errors ? Object.values(errors).flat().join(' ') : 'Възникна грешка при регистрация.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="mx-auto flex max-w-md flex-col px-6 py-24">
    <h1 class="font-display text-3xl italic text-cream">Регистрация</h1>

    <div v-if="success" class="mt-10 rounded-xl border border-accent/50 bg-surface p-6 text-sm text-ink-muted">
      Регистрацията е успешна. Изпратихме имейл за активация на профила — моля, потвърди го, за да можеш да влезеш.
    </div>

    <form v-else class="mt-10 flex flex-col gap-5" @submit.prevent="submit">
      <label class="flex flex-col gap-2 text-sm text-ink-muted">
        Име
        <input v-model="form.name" required class="rounded-lg border border-divider bg-surface px-4 py-3 text-ink outline-none focus:border-accent" />
      </label>
      <label class="flex flex-col gap-2 text-sm text-ink-muted">
        Имейл
        <input v-model="form.email" type="email" required class="rounded-lg border border-divider bg-surface px-4 py-3 text-ink outline-none focus:border-accent" />
      </label>
      <label class="flex flex-col gap-2 text-sm text-ink-muted">
        Телефон
        <input v-model="form.phone" class="rounded-lg border border-divider bg-surface px-4 py-3 text-ink outline-none focus:border-accent" />
      </label>
      <label class="flex flex-col gap-2 text-sm text-ink-muted">
        Парола
        <input v-model="form.password" type="password" required class="rounded-lg border border-divider bg-surface px-4 py-3 text-ink outline-none focus:border-accent" />
      </label>
      <label class="flex flex-col gap-2 text-sm text-ink-muted">
        Потвърди парола
        <input v-model="form.password_confirmation" type="password" required class="rounded-lg border border-divider bg-surface px-4 py-3 text-ink outline-none focus:border-accent" />
      </label>
      <p v-if="error" class="text-sm text-red-400">{{ error }}</p>
      <button
        type="submit"
        :disabled="loading"
        class="mt-2 rounded-full bg-accent py-3 text-sm font-medium text-bg transition hover:brightness-110 disabled:opacity-60"
      >
        {{ loading ? 'Изчакайте…' : 'Регистрирай се' }}
      </button>
    </form>

    <p class="mt-8 text-center text-sm text-ink-muted">
      Вече имаш профил?
      <RouterLink to="/login" class="text-accent hover:underline">Влез</RouterLink>
    </p>
  </div>
</template>
