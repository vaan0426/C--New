<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'

defineProps({ auth: { type: Object, required: true } })

const open = ref(false)

const navLinks = [
  { label: 'За мен', href: '/#about' },
  { label: 'Услуги', href: '/#services' },
  { label: 'Пакети', href: '/#packages' },
  { label: 'Събития', href: '/#events' },
  { label: 'Контакти', href: '/#contact' },
]
</script>

<template>
  <header class="sticky top-0 z-40 border-b border-divider/70 bg-bg/90 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
      <RouterLink to="/" class="flex items-center gap-3">
        <span
          class="flex h-10 w-10 items-center justify-center rounded-full border border-accent text-accent font-display text-lg italic"
        >
          ПП
        </span>
        <span class="font-display text-xl italic text-cream">Психологически програми</span>
      </RouterLink>

      <nav class="hidden items-center gap-8 text-sm tracking-wide text-ink-muted md:flex">
        <a v-for="link in navLinks" :key="link.href" :href="link.href" class="transition hover:text-cream">
          {{ link.label }}
        </a>
      </nav>

      <div class="hidden items-center gap-4 md:flex">
        <RouterLink
          v-if="!auth.isAuthenticated"
          to="/login"
          class="text-sm text-ink-muted transition hover:text-cream"
        >
          Вход
        </RouterLink>
        <RouterLink v-else to="/profile" class="text-sm text-ink-muted transition hover:text-cream">
          Моят профил
        </RouterLink>
        <RouterLink
          to="/book"
          class="rounded-full bg-accent px-5 py-2 text-sm font-medium text-bg transition hover:brightness-110"
        >
          Запази час
        </RouterLink>
      </div>

      <button class="text-cream md:hidden" @click="open = !open" aria-label="Меню">
        <span class="block h-0.5 w-6 bg-current" />
        <span class="mt-1.5 block h-0.5 w-6 bg-current" />
        <span class="mt-1.5 block h-0.5 w-6 bg-current" />
      </button>
    </div>

    <div v-if="open" class="border-t border-divider/70 px-6 py-4 md:hidden">
      <nav class="flex flex-col gap-4 text-sm text-ink-muted">
        <a v-for="link in navLinks" :key="link.href" :href="link.href" @click="open = false">{{ link.label }}</a>
        <RouterLink v-if="!auth.isAuthenticated" to="/login" @click="open = false">Вход</RouterLink>
        <RouterLink v-else to="/profile" @click="open = false">Моят профил</RouterLink>
        <RouterLink
          to="/book"
          class="w-fit rounded-full bg-accent px-5 py-2 font-medium text-bg"
          @click="open = false"
        >
          Запази час
        </RouterLink>
      </nav>
    </div>
  </header>
</template>
