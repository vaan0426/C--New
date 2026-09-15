<script setup>
const year = new Date().getFullYear()
</script>

<template>
  <footer class="border-t border-divider/70 bg-surface">
    <div class="mx-auto grid max-w-6xl gap-10 px-6 py-14 md:grid-cols-3">
      <div>
        <div class="flex items-center gap-3">
          <span
            class="flex h-10 w-10 items-center justify-center rounded-full border border-accent text-accent font-display text-lg italic"
          >
            ПП
          </span>
          <span class="font-display text-xl italic text-cream">Психологически програми</span>
        </div>
        <p class="mt-4 max-w-xs text-sm leading-relaxed text-ink-muted">
          Терапевтично студио за индивидуална, семейна и женска терапия. Ти не търсиш мотивация. Ти търсиш промяна.
        </p>
      </div>

      <div>
        <h3 class="font-display text-lg italic text-cream">Навигация</h3>
        <ul class="mt-4 space-y-2 text-sm text-ink-muted">
          <li><a href="/#about" class="hover:text-cream">За мен</a></li>
          <li><a href="/#services" class="hover:text-cream">Услуги</a></li>
          <li><a href="/#packages" class="hover:text-cream">Пакети</a></li>
          <li><a href="/#events" class="hover:text-cream">Събития</a></li>
        </ul>
      </div>

      <div id="contact">
        <h3 class="font-display text-lg italic text-cream">Контакти</h3>
        <ul class="mt-4 space-y-2 text-sm text-ink-muted">
          <li>info@psychologicheski-programi.bg</li>
          <li>+359 88 000 0000</li>
          <li>София, България</li>
        </ul>
      </div>
    </div>

    <div class="border-t border-divider/70 px-6 py-6 text-center text-xs text-ink-muted">
      © {{ year }} Психологически програми. Всички права запазени.
    </div>
  </footer>
</template>
