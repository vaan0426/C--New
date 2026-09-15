<script setup>
import AdminLayout from '../../components/AdminLayout.vue'
</script>

<template>
  <AdminLayout>
    <h1 class="font-display text-2xl italic text-cream">Настройки</h1>
    <p class="mt-2 max-w-xl text-sm text-ink-muted">
      Тези стойности идват от конфигурацията на backend-а (<code class="text-cream">.env</code>) и все още не са
      преведени в редактируем екран — при нужда е следваща стъпка (спецификация §12).
    </p>

    <div class="mt-8 grid gap-4 sm:grid-cols-2">
      <div class="rounded-xl border border-divider bg-surface p-5">
        <p class="text-xs uppercase tracking-wide text-ink-muted">Валута</p>
        <p class="mt-2 text-lg text-cream">EUR</p>
      </div>
      <div class="rounded-xl border border-divider bg-surface p-5">
        <p class="text-xs uppercase tracking-wide text-ink-muted">Платежни методи</p>
        <p class="mt-2 text-lg text-cream">Stripe · На място</p>
      </div>
      <div class="rounded-xl border border-divider bg-surface p-5 sm:col-span-2">
        <p class="text-xs uppercase tracking-wide text-ink-muted">Имейл шаблони</p>
        <p class="mt-2 text-sm text-ink-muted">
          Активация на акаунт (Laravel verification notification) — стандартен шаблон, все още не е персонализиран
          с марковата визия. Потвърждение на резервация — предстои (спецификация §12).
        </p>
      </div>
    </div>
  </AdminLayout>
</template>
