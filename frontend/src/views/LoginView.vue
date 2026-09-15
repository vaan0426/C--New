<script setup>
import { ref } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

async function submit() {
  error.value = ''
  loading.value = true
  try {
    await auth.login(email.value, password.value)
    router.push(route.query.redirect || '/profile')
  } catch (e) {
    error.value = e.response?.data?.message || 'Възникна грешка при вход.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="mx-auto flex max-w-md flex-col px-6 py-24">
    <h1 class="font-display text-3xl italic text-cream">Вход</h1>
    <form class="mt-10 flex flex-col gap-5" @submit.prevent="submit">
      <label class="flex flex-col gap-2 text-sm text-ink-muted">
        Имейл
        <input
          v-model="email"
          type="email"
          required
          class="rounded-lg border border-divider bg-surface px-4 py-3 text-ink outline-none focus:border-accent"
        />
      </label>
      <label class="flex flex-col gap-2 text-sm text-ink-muted">
        Парола
        <input
          v-model="password"
          type="password"
          required
          class="rounded-lg border border-divider bg-surface px-4 py-3 text-ink outline-none focus:border-accent"
        />
      </label>
      <p v-if="error" class="text-sm text-red-400">{{ error }}</p>
      <button
        type="submit"
        :disabled="loading"
        class="mt-2 rounded-full bg-accent py-3 text-sm font-medium text-bg transition hover:brightness-110 disabled:opacity-60"
      >
        {{ loading ? 'Изчакайте…' : 'Влез' }}
      </button>
    </form>
    <p class="mt-8 text-center text-sm text-ink-muted">
      Нямаш профил?
      <RouterLink to="/register" class="text-accent hover:underline">Регистрирай се</RouterLink>
    </p>
  </div>
</template>
