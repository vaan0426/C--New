<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import { RouterLink } from 'vue-router'
import api from '../lib/api'

const slides = [
  {
    quote: 'Стига си слагал маска на страховете си.',
    cta: 'ПРОГРАМА — ОТНОВО АЗ',
    photo: 'photo--1',
  },
  {
    quote: 'Споделям значи обичам',
    cta: 'ПРОГРАМА — АЗ СМЕ НИЕ',
    photo: 'photo--4',
  },
]
const activeSlide = ref(0)
let timer = null

onMounted(() => {
  timer = setInterval(() => {
    activeSlide.value = (activeSlide.value + 1) % slides.length
  }, 6000)
})
onUnmounted(() => clearInterval(timer))

const services = [
  {
    name: 'Индивидуална',
    desc: 'Пространство само за теб — да разбереш какво носиш и накъде вървиш.',
    photo: 'photo--2',
  },
  {
    name: 'Семейна',
    desc: 'За връзки, в които комуникацията се е загубила по пътя.',
    photo: 'photo--5',
  },
  {
    name: 'За жени',
    desc: 'Кръг на споделяне и подкрепа в женски опит и трансформация.',
    photo: 'photo--3',
  },
  {
    name: 'Групова',
    desc: 'Растеж чрез принадлежност — не си сам/а в това, което преживяваш.',
    photo: 'photo--6',
  },
]

const steps = [
  { n: '1', title: 'Избери час', desc: 'Разгледай свободните часове и избери подходящия за теб.' },
  { n: '2', title: 'Изчакай потвърждение', desc: 'Заявката се потвърждава лично от психолога.' },
  { n: '3', title: 'Проведи сесията', desc: 'Идваш подготвен/а — останалото е наша работа.' },
]

const gallery = [
  { photo: 'photo--3', label: 'Кабинетът', tall: true },
  { photo: 'photo--5', label: 'Чакалня', tall: false },
  { photo: 'photo--1', label: 'Прозорецът', tall: false },
  { photo: 'photo--4', label: 'Кръгът', tall: true },
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

const eventPhotos = ['photo--2', 'photo--4', 'photo--6']
const sampleEvents = [
  { id: 'sample-1', title: 'Кръг на жените', starts_at: null, price: 40, capacity: 12 },
  { id: 'sample-2', title: 'Работилница за двойки', starts_at: null, price: 90, capacity: 8 },
  { id: 'sample-3', title: 'Ден на осъзнатостта', starts_at: null, price: 55, capacity: 15 },
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
    <section class="relative h-screen min-h-[640px] overflow-hidden bg-surface">
      <div
        v-for="(slide, i) in slides"
        :key="slide.cta"
        class="absolute inset-0 transition-opacity duration-1000"
        :class="i === activeSlide ? 'opacity-100' : 'opacity-0'"
      >
        <div class="photo absolute inset-0" :class="[slide.photo, i === activeSlide && 'ken-burns']" />
        <div class="absolute inset-0 bg-gradient-to-t from-bg via-bg/10 to-transparent" />
        <div class="relative flex h-full items-end">
          <div class="mx-auto w-full max-w-6xl px-6 pb-28">
            <p class="max-w-2xl font-display text-5xl italic leading-[1.1] text-cream sm:text-6xl lg:text-7xl">
              „{{ slide.quote }}“
            </p>
            <RouterLink
              to="/book"
              class="mt-9 inline-block rounded-full border border-accent px-7 py-3.5 text-xs font-medium tracking-[0.2em] text-accent transition hover:bg-accent hover:text-bg"
            >
              {{ slide.cta }}
            </RouterLink>
          </div>
        </div>
      </div>

      <div class="absolute bottom-8 left-1/2 flex -translate-x-1/2 gap-2">
        <button
          v-for="(slide, i) in slides"
          :key="slide.cta + '-dot'"
          class="h-1.5 rounded-full transition-all"
          :class="i === activeSlide ? 'w-8 bg-accent' : 'w-1.5 bg-cream/30'"
          :aria-label="`Слайд ${i + 1}`"
          @click="activeSlide = i"
        />
      </div>
    </section>

    <!-- 3. За мен -->
    <section id="about" class="mx-auto grid max-w-6xl gap-12 px-6 py-28 lg:grid-cols-[0.85fr_1.15fr] lg:items-center lg:gap-20">
      <div class="relative aspect-[4/5] overflow-hidden rounded-sm">
        <img src="/images/about.webp" alt="Портрет" class="h-full w-full object-cover object-top" />
      </div>
      <div>
        <h2 class="font-display text-4xl italic text-cream">За мен</h2>
        <p class="mt-6 max-w-lg text-[15px] leading-relaxed text-ink-muted">
          Работя с индивиди, двойки и семейства, които търсят пространство за истински разговор — без клинична
          дистанция, с топлина и последователност. Вярвам, че промяната идва не от съвети, а от това да бъдеш
          чут/а истински.
        </p>
        <p class="mt-8 font-display text-2xl italic text-accent">„Ти не търсиш мотивация. Ти търсиш промяна.“</p>
      </div>
    </section>

    <!-- 4. Услуги -->
    <section id="services" class="border-y border-divider/70 bg-surface/40 py-28">
      <div class="mx-auto max-w-6xl px-6">
        <h2 class="font-display text-4xl italic text-cream">Услуги</h2>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          <div
            v-for="s in services"
            :key="s.name"
            class="photo relative group aspect-[3/4] rounded-sm"
            :class="s.photo"
          >
            <div class="photo-caption">
              <h3 class="font-display text-xl italic text-cream">{{ s.name }}</h3>
              <p class="mt-2 text-xs leading-relaxed text-cream/80">{{ s.desc }} ♡</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 5. Как протича -->
    <section id="process" class="mx-auto max-w-5xl px-6 py-28">
      <h2 class="text-center font-display text-4xl italic text-cream">Как протича</h2>
      <div class="relative mt-16 grid gap-10 sm:grid-cols-3">
        <div
          class="pointer-events-none absolute top-7 hidden h-px w-full bg-gradient-to-r from-transparent via-divider to-transparent sm:block"
        />
        <div v-for="step in steps" :key="step.title" class="relative text-center">
          <div
            class="relative mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-accent bg-bg font-display text-xl italic text-accent"
          >
            {{ step.n }}
          </div>
          <h3 class="mt-5 text-sm font-medium tracking-wide text-cream">{{ step.title }}</h3>
          <p class="mt-2 text-sm text-ink-muted">{{ step.desc }}</p>
        </div>
      </div>
    </section>

    <!-- Пространството — снимкова галерия -->
    <section class="border-y border-divider/70 bg-surface/40 py-28">
      <div class="mx-auto max-w-6xl px-6">
        <h2 class="font-display text-4xl italic text-cream">Пространството</h2>
        <div class="mt-12 grid grid-cols-2 gap-4 sm:grid-cols-4">
          <div
            v-for="g in gallery"
            :key="g.label"
            class="photo relative rounded-sm"
            :class="[g.photo, g.tall ? 'row-span-2 aspect-[3/4]' : 'aspect-square']"
          >
            <div class="photo-caption !p-3">
              <p class="text-[11px] tracking-wide text-cream/80">{{ g.label }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 6. Пакети и цени -->
    <section id="packages" class="py-28">
      <div class="mx-auto max-w-6xl px-6">
        <h2 class="text-center font-display text-4xl italic text-cream">Пакети и цени</h2>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          <div
            v-for="p in pricing"
            :key="p.name"
            class="flex flex-col rounded-sm border p-7"
            :class="p.featured ? 'border-accent bg-surface shadow-[0_0_0_1px_rgba(184,135,74,0.4)]' : 'border-divider bg-surface/60'"
          >
            <span v-if="p.note" class="mb-4 w-fit rounded-full bg-accent px-3 py-1 text-xs font-medium text-bg">
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
    <section id="events" class="border-y border-divider/70 bg-surface/40 py-28">
      <div class="mx-auto max-w-6xl px-6">
        <h2 class="font-display text-4xl italic text-cream">Събития</h2>
        <div class="mt-12 grid gap-6 sm:grid-cols-3">
          <div
            v-for="(e, i) in events"
            :key="e.id"
            class="photo relative aspect-[4/5] rounded-sm"
            :class="eventPhotos[i % eventPhotos.length]"
          >
            <div class="photo-caption">
              <p class="text-[11px] uppercase tracking-widest text-cream/70">{{ formatDate(e.starts_at) }}</p>
              <h3 class="mt-1 font-display text-xl italic text-cream">{{ e.title }}</h3>
              <div class="mt-3 flex items-center justify-between text-sm">
                <span class="text-accent">{{ e.price }} €</span>
                <span class="text-cream/70">{{ e.spots_left ?? e.capacity }} свободни места</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 8. Отзиви -->
    <section class="relative overflow-hidden py-28">
      <div class="photo photo--3 absolute inset-0 opacity-[0.14]" />
      <div class="relative mx-auto max-w-5xl px-6">
        <h2 class="text-center font-display text-4xl italic text-cream">Отзиви</h2>
        <div class="mt-14 grid gap-10 sm:grid-cols-3">
          <figure v-for="t in testimonials" :key="t.author" class="text-center">
            <div class="photo relative photo--5 mx-auto h-14 w-14 rounded-full" />
            <blockquote class="mt-5 font-display text-lg italic leading-snug text-cream">„{{ t.quote }}“</blockquote>
            <figcaption class="mt-4 text-xs uppercase tracking-widest text-ink-muted">{{ t.author }}</figcaption>
          </figure>
        </div>
      </div>
    </section>

    <!-- 9. CTA банер -->
    <section class="relative overflow-hidden">
      <div class="photo photo--1 absolute inset-0" />
      <div class="relative mx-auto max-w-4xl px-6 py-32 text-center">
        <h2 class="font-display text-4xl italic text-cream sm:text-5xl">Готов/а ли си за първата стъпка?</h2>
        <RouterLink
          to="/book"
          class="mt-9 inline-block rounded-full bg-accent px-9 py-3.5 text-sm font-medium text-bg transition hover:brightness-110"
        >
          Запази своя час
        </RouterLink>
      </div>
    </section>
  </div>
</template>
