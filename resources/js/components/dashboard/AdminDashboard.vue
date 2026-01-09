<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'
import accountstatuses from '@/routes/accountstatuses'
import locations from '@/routes/locations'
import members from '@/routes/members'
import users from '@/routes/users'
// Nota: Para los iconos puedes usar librerías como Lucide-Vue-Next o Heroicons
// Aquí simularemos los espacios para iconos para mantener el código limpio.

const { auth } = usePage().props as {
  auth: { user: { name: string, image: string } }
}

defineProps<{
  stats: {
    total_members: number
    active_users: number
    morosos: number
  }
  role: 'admin' | 'staff'
}>()

const menuItems = [
  { name: 'Matriculados', href: members.index(), icon: '👥', color: 'bg-blue-500' },
  { name: 'Usuarios', href: users.index(), icon: '🔐', color: 'bg-purple-500' },
  { name: 'Localidades', href: locations.index(), icon: '📍', color: 'bg-emerald-500' },
  { name: 'Estados de Cuenta', href: accountstatuses.index(), icon: '💳', color: 'bg-amber-500' },
]
</script>

<template>
  <Head title="Panel de Administración" />

  <div class="min-h-screen bg-[#f8fafc] p-4 md:p-8 dark:bg-gray-950">
    <header class="mx-auto max-w-7xl mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">Sistema de Gestión</p>
        <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">
          Panel de Control
        </h1>
      </div>
      
      <div class="flex items-center gap-3 bg-white dark:bg-gray-900 p-2 pr-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="h-10 w-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 font-bold">
          <!-- <img v-if="photoPreview" :src="photoPreview" class="h-full w-full object-cover" /> -->
          <img v-if="auth.user.image" :src="'/storage/' + auth.user.image" class="h-full w-full object-cover rounded-xl" />
          <div v-else-if="auth.user.image!">{{ auth.user.name.charAt(0) }}</div>
        </div>
        <div>
          <p v-if="role === 'admin'" class="text-xs text-gray-500 font-medium">Super Usuario</p>
          <p v-else class="text-xs text-gray-500 font-medium">Personal de Administración </p>
          <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ auth.user.name }}</p>
        </div>
      </div>
    </header>

    <div class="mx-auto max-w-7xl">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        <div class="relative overflow-hidden bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 group hover:shadow-md transition-all">
          <dt class="text-sm font-semibold text-gray-500">Total Matriculados</dt>
          <dd class="mt-2 text-4xl font-black text-gray-900 dark:text-white">{{ stats.total_members.toLocaleString() }}</dd>
          <div class="absolute -right-2 -bottom-2 opacity-5 text-7xl font-black">👥</div>
        </div>

        <div class="relative overflow-hidden bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 group hover:shadow-md transition-all">
          <dt class="text-sm font-semibold text-red-500">Morosidad Activa</dt>
          <dd class="mt-2 text-4xl font-black text-red-600">{{ stats.morosos.toLocaleString() }}</dd>
          <div class="absolute -right-2 -bottom-2 opacity-10 text-7xl font-black text-red-500">⚠️</div>
        </div>

        <div class="relative overflow-hidden bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 group hover:shadow-md transition-all sm:col-span-2 lg:col-span-1">
          <dt class="text-sm font-semibold text-emerald-500">Usuarios en Línea</dt>
          <dd class="mt-2 text-4xl font-black text-emerald-600">{{ stats.active_users.toLocaleString() }}</dd>
          <div class="absolute -right-2 -bottom-2 opacity-10 text-7xl font-black text-emerald-500">⚡</div>
        </div>
      </div>

      <section>
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
          <span class="h-2 w-2 bg-indigo-500 rounded-full"></span>
          Gestión del Sistema
        </h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <Link 
            v-for="item in menuItems" 
            :key="item.name"
            :href="item.href"
            class="group relative flex flex-col p-6 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 hover:border-indigo-500 dark:hover:border-indigo-500 hover:shadow-xl hover:shadow-indigo-500/5 transition-all"
          >
            <div :class="[item.color, 'h-12 w-12 rounded-xl flex items-center justify-center text-2xl mb-4 shadow-lg shadow-inherit/20 text-white']">
              {{ item.icon }}
            </div>
            <span class="font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 transition-colors">
              {{ item.name }}
            </span>
            <span class="text-xs text-gray-500 mt-1 italic">Administrar módulo</span>
          </Link>
        </div>
      </section>
    </div>
  </div>
</template>