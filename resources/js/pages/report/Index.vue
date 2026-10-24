<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import { type BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'

import CustomReportModal from '@/components/report/CustomReportModal.vue'
import ExportReportModal from '@/components/report/ExportReportModal.vue'
import LocationsReportModal from '@/components/report/LocationsReportModal.vue'
import MembersReportModal from '@/components/report/MembersReportModal.vue'
import StatusReportModal from '@/components/report/StatusReportModal.vue'
import type { ReportMember } from '@/lib/exportData'

defineProps<{
  members: ReportMember[]
  statuses: { id: number; name: string }[]
  provinces: { id: number; name: string }[]
  cities: { id: number; name: string; province_id: number }[]
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Inicio', href: dashboard().url },
  { title: 'Reportes', href: '/report' },
]

const reports = [
  { id: 'members', title: 'Reporte de Matriculados', description: 'Lista completa de matriculados con su estado y ubicación', icon: '👥', color: 'bg-blue-500' },
  { id: 'locations', title: 'Reporte por Ubicación', description: 'Matriculados agrupados por provincia y ciudad', icon: '📍', color: 'bg-purple-500' },
  { id: 'status', title: 'Reporte de Estados', description: 'Cuántos matriculados hay al día, morosos o suspendidos', icon: '📊', color: 'bg-orange-500' },
  { id: 'custom', title: 'Reporte Personalizado', description: 'Filtrá por fecha, estado y provincia, y elegí las columnas', icon: '⚙️', color: 'bg-gray-500' },
  { id: 'export', title: 'Exportar Datos', description: 'Descargá el padrón completo en Excel o CSV', icon: '📥', color: 'bg-indigo-500' },
]

const activeModal = ref<string | null>(null)
const closeModal = () => { activeModal.value = null }
</script>

<template>
  <Head title="Reportes" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 w-full max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
          Reportes del Sistema
        </h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Los reportes se calculan con los datos actuales del sistema
        </p>
      </div>

      <!-- Grid de Reportes -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="report in reports"
          :key="report.id"
          @click="activeModal = report.id"
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

    </div>

    <!-- Modales -->
    <MembersReportModal :show="activeModal === 'members'" :members="members" @close="closeModal" />
    <LocationsReportModal :show="activeModal === 'locations'" :members="members" :statuses="statuses" @close="closeModal" />
    <StatusReportModal :show="activeModal === 'status'" :members="members" :statuses="statuses" @close="closeModal" />
    <CustomReportModal :show="activeModal === 'custom'" :members="members" :statuses="statuses" :provinces="provinces" @close="closeModal" />
    <ExportReportModal :show="activeModal === 'export'" :members="members" @close="closeModal" />
  </AppLayout>
</template>