<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import { type BreadcrumbItem } from '@/types'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Reportes', href: dashboard().url },
]

interface Report {
  id: string
  title: string
  description: string
  icon: string
  color: string
  route: string
}

const reports = ref<Report[]>([
  {
    id: 'members',
    title: 'Reporte de Matriculados',
    description: 'Lista completa de miembros con filtros por estado, ubicación y fecha',
    icon: '👥',
    color: 'bg-blue-500',
    route: 'admin.report.members'
  },
  {
    id: 'financial',
    title: 'Reporte Financiero',
    description: 'Ingresos, pagos pendientes y estado de cuentas',
    icon: '💰',
    color: 'bg-green-500',
    route: 'admin.report.financial'
  },
  {
    id: 'locations',
    title: 'Reporte por Ubicación',
    description: 'Estadísticas de miembros agrupados por localidad',
    icon: '📍',
    color: 'bg-purple-500',
    route: 'admin.report.locations'
  },
  {
    id: 'status',
    title: 'Reporte de Estados',
    description: 'Análisis de miembros activos, inactivos y suspendidos',
    icon: '📊',
    color: 'bg-orange-500',
    route: 'admin.report.status'
  },
  {
    id: 'custom',
    title: 'Reporte Personalizado',
    description: 'Crea reportes personalizados con múltiples filtros',
    icon: '⚙️',
    color: 'bg-gray-500',
    route: 'admin.report.custom'
  },
  {
    id: 'export',
    title: 'Exportar Datos',
    description: 'Descarga datos en formato Excel, PDF o CSV',
    icon: '📥',
    color: 'bg-indigo-500',
    route: 'admin.report.export'
  }
])

const navigateToReport = (route: string) => {
  router.visit(route)
}
</script>

<template>
  <Head title="Reportes" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
          Reportes del Sistema
        </h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Selecciona el tipo de reporte que deseas generar
        </p>
      </div>

      <!-- Grid de Reportes -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="report in reports"
          :key="report.id"
          @click="navigateToReport(report.route)"
          class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:-translate-y-1 border border-gray-200 dark:border-gray-700 overflow-hidden group"
        >
          <!-- Color Bar -->
          <div :class="[report.color, 'h-2 w-full']"></div>
          
          <!-- Card Content -->
          <div class="p-6">
            <!-- Icon -->
            <div class="flex items-center justify-center w-16 h-16 mb-4 rounded-full bg-gray-100 dark:bg-gray-700 text-4xl group-hover:scale-110 transition-transform">
              {{ report.icon }}
            </div>
            
            <!-- Title -->
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
              {{ report.title }}
            </h3>
            
            <!-- Description -->
            <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
              {{ report.description }}
            </p>
            
            <!-- Arrow Icon -->
            <div class="mt-4 flex items-center text-blue-600 dark:text-blue-400 text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity">
              Ver reporte
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Sección de Accesos Rápidos -->
      <div class="mt-12 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-lg p-6 border border-blue-200 dark:border-gray-600">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
          Accesos Rápidos
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <button
            @click="router.visit('admin.report.index')"
            class="flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition-shadow text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600"
          >
            <span class="mr-2">📅</span>
            Último reporte generado
          </button>
          <button
            @click="router.visit('admin.report.scheduled')"
            class="flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition-shadow text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600"
          >
            <span class="mr-2">🔔</span>
            Reportes programados
          </button>
          <button
            @click="router.visit('admin.report.history')"
            class="flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition-shadow text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600"
          >
            <span class="mr-2">📂</span>
            Historial de reportes
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>