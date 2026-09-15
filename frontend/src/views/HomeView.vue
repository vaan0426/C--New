<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import { RouterLink } from 'vue-router'
import api from '../lib/api'

const slides = [
  {
    quote: 'Стига си слагал маска на страховете си.',
    cta: 'ПРОГРАМА — ОТНОВО АЗ',
  },
  {
    quote: 'Споделям значи обичам',
    cta: 'ПРОГРАМА — АЗ СМЕ НИЕ',
  },
]
const activeSlide = ref(0)
let timer = null

onMounted(() => {
  timer = setInterval(() => {
    activeSlide.value = (activeSlide.value + 1) % slides.length
  }, 5000)
})
onUnmounted(() => clearInterval(timer))

const services = [
  { name: 'Индивидуална', desc: 'Пространство само за теб — да разбереш какво носиш и накъде вървиш.' },
  { name: 'Семейна', desc: 'За връзки, в които комуникацията се е загубила по пътя.' },
  { name: 'За жени', desc: 'Кръг на споделяне и подкрепа в женски опит и трансформация.' },
  { name: 'Групова', desc: 'Растеж чрез принадлежност — не си сам/а в това, което преживяваш.' },
]

const steps = [
  { n: '1', title: 'Избери час', desc: 'Разгледай свободните часове и избери подходящия за теб.' },
  { n: '2', title: 'Изчакай потвърждение', desc: 'Заявката се потвърждава лично от психолога.' },
  { n: '3', title: 'Проведи сесията', desc: 'Идваш подготвен/а — останалото е наша работа.' },
]

const pricing = [
  { name: 'Индивидуална консултация', price: '60 €', meta: '/ 50 мин' },
  { name: 'Семейна консултация', price: '80 €', meta: '/ 75 мин' },
  {
    name: 'Пакет „5 сесии“',
    price: '200 €',
    meta: '/ 1 месец',
    note: 'Спестява 15%',
    featured: true,
  },
  { name: 'Групова терапия', price: '40 €', meta: '/ участник / 90 мин' },
]

const testimonials = [
  { quote: 'Работя с хора, които са готови да се върнат към себе си.', author: 'доволна клиентка' },
  { quote: 'Намерих спокойствие и разбиране, от които имах нужда за семейството си.', author: 'клиентка, семейна терапия' },
  { quote: 'Твоята история заслужава нова глава — и тук наистина го усетих.', author: 'участничка, Кръг на жените' },
]

const sampleEvents = [
  { id: 'sample-1', title: 'Кръг на жените', starts_at: null, price: 40, spots_left: 6, capacity: 12 },
]
const events = ref(sampleEvents)

onMounted(async () => {
  try {
    const { data } = await api.get('/events')
    if (data?.length) events.value = data
  } catch {
    // keep sample content if the API isn't reachable yet
  }
})

function formatDate(value) {
  if (!value) return 'Скоро'
  return new Date(value).toLocaleDateString('bg-BG', { day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit' })
}
</script>

<template>
  <div>
    <!-- 1-2. Hero slider -->
    <section class="relative h-[92vh] min-h-[560px] overflow-hidden bg-surface">
      <div
        v-for="(slide, i) in slides"
        :key="slide.cta"
        class="absolute inset-0 flex items-end transition-opacity duration-1000"
        :class="i === activeSlide ? 'opacity-100' : 'opacity-0'"
        :style="{
          background:
            'radial-gradient(ellipse at 30% 20%, rgba(184,135,74,0.18), transparent 55%), linear-gradient(180deg, rgba(23,19,16,0.2), rgba(23,19,16,0.95)), linear-gradient(135deg, #2a2018, #171310)',
        }"
      >
        <div class="mx-auto w-full max-w-6xl px-6 pb-24">
          <p class="max-w-xl font-display text-4xl italic leading-tight text-cream sm:text-5xl">
            „{{ slide.quote }}“
          </p>
          <RouterLink
            to="/book"
            class="mt-8 inline-block rounded-full border border-accent px-6 py-3 text-xs font-medium tracking-[0.2em] text-accent transition hover:bg-accent hover:text-bg"
          >
            {{ slide.cta }}
          </RouterLink>
        </div>
      </div>

      <div class="absolute bottom-8 left-1/2 flex -translate-x-1/2 gap-2">
        <button
          v-for="(slide, i) in slides"
          :key="slide.cta + '-dot'"
          class="h-2 w-2 rounded-full transition"
          :class="i === activeSlide ? 'bg-accent' : 'bg-ink-muted/40'"
          :aria-label="`Слайд ${i + 1}`"
          @click="activeSlide = i"
        />
      </div>
    </section>

    <!-- 3. За мен -->
    <section id="about" class="mx-auto max-w-5xl px-6 py-24 text-center">
      <div class="mx-auto h-28 w-28 overflow-hidden rounded-full border border-divider bg-surface" />
      <h2 class="mt-8 font-display text-3xl italic text-cream">За мен</h2>
      <p class="mx-auto mt-4 max-w-2xl text-[15px] leading-relaxed text-ink-muted">
        Работя с индивиди, двойки и семейства, които търсят пространство за истински разговор — без клинична
        дистанция, с топлина и последователност.
      </p>
      <p class="mt-6 font-display text-xl italic text-accent">„Ти не търсиш мотивация. Ти търсиш промяна.“</p>
    </section>

    <!-- 4. Услуги -->
    <section id="services" class="border-y border-divider/70 bg-surface/40 py-24">
      <div class="mx-auto max-w-6xl px-6">
        <h2 class="text-center font-display text-3xl italic text-cream">Услуги</h2>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          <div
            v-for="s in services"
            :key="s.name"
            class="rounded-2xl border border-divider bg-surface p-6 transition hover:border-accent/60"
          >
            <h3 class="font-display text-xl italic text-cream">{{ s.name }}</h3>
            <p class="mt-3 text-sm leading-relaxed text-ink-muted">{{ s.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 5. Как протича -->
    <section id="process" class="mx-auto max-w-5xl px-6 py-24">
      <h2 class="text-center font-display text-3xl italic text-cream">Как протича</h2>
      <div class="mt-14 grid gap-10 sm:grid-cols-3">
        <div v-for="(step, i) in steps" :key="step.title" class="relative text-center">
          <div
            class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-accent font-display text-xl italic text-accent"
          >
            {{ step.n }}
          </div>
          <h3 class="mt-5 text-sm font-medium tracking-wide text-cream">{{ step.title }}</h3>
          <p class="mt-2 text-sm text-ink-muted">{{ step.desc }}</p>
          <span
            v-if="i < steps.length - 1"
            class="absolute right-[-1.25rem] top-7 hidden text-accent/50 sm:block"
            aria-hidden="true"
          >→</span>
        </div>
      </div>
    </section>

    <!-- 6. Пакети и цени -->
    <section id="packages" class="border-y border-divider/70 bg-surface/40 py-24">
      <div class="mx-auto max-w-6xl px-6">
        <h2 class="text-center font-display text-3xl italic text-cream">Пакети и цени</h2>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          <div
            v-for="p in pricing"
            :key="p.name"
            class="flex flex-col rounded-2xl border p-6"
            :class="p.featured ? 'border-accent bg-bg shadow-[0_0_0_1px_rgba(184,135,74,0.4)]' : 'border-divider bg-surface'"
          >
            <span v-if="p.note" class="mb-3 w-fit rounded-full bg-accent px-3 py-1 text-xs font-medium text-bg">
              {{ p.note }}
            </span>
            <h3 class="font-display text-lg italic text-cream">{{ p.name }}</h3>
            <p class="mt-4 text-3xl text-cream">
              {{ p.price }}
              <span class="text-sm text-ink-muted">{{ p.meta }}</span>
            </p>
            <RouterLink
              to="/book"
              class="mt-6 rounded-full border border-accent py-2 text-center text-sm text-accent transition hover:bg-accent hover:text-bg"
            >
              Избери
            </RouterLink>
          </div>
        </div>
      </div>
    </section>

    <!-- 7. Събития -->
    <section id="events" class="mx-auto max-w-6xl px-6 py-24">
      <h2 class="text-center font-display text-3xl italic text-cream">Събития</h2>
      <div class="mt-12 grid gap-6 sm:grid-cols-3">
        <div v-for="e in events" :key="e.id" class="rounded-2xl border border-divider bg-surface p-6">
          <p class="text-xs uppercase tracking-widest text-ink-muted">{{ formatDate(e.starts_at) }}</p>
          <h3 class="mt-2 font-display text-xl italic text-cream">{{ e.title }}</h3>
          <div class="mt-4 flex items-center justify-between text-sm">
            <span class="text-accent">{{ e.price }} €</span>
            <span class="text-ink-muted">{{ e.spots_left ?? e.capacity }} свободни места</span>
          </div>
        </div>
      </div>
    </section>

    <!-- 8. Отзиви -->
    <section class="border-y border-divider/70 bg-surface/40 py-24">
      <div class="mx-auto max-w-5xl px-6">
        <h2 class="text-center font-display text-3xl italic text-cream">Отзиви</h2>
        <div class="mt-12 grid gap-8 sm:grid-cols-3">
          <figure v-for="t in testimonials" :key="t.author" class="text-center">
            <blockquote class="font-display text-lg italic leading-snug text-cream">„{{ t.quote }}“</blockquote>
            <figcaption class="mt-4 text-xs uppercase tracking-widest text-ink-muted">{{ t.author }}</figcaption>
          </figure>
        </div>
      </div>
    </section>

    <!-- 9. CTA банер -->
    <section class="mx-auto max-w-4xl px-6 py-28 text-center">
      <h2 class="font-display text-3xl italic text-cream sm:text-4xl">Готов/а ли си за първата стъпка?</h2>
      <RouterLink
        to="/book"
        class="mt-8 inline-block rounded-full bg-accent px-8 py-3 text-sm font-medium text-bg transition hover:brightness-110"
      >
        Запази своя час
      </RouterLink>
    </section>
  </div>
</template>
