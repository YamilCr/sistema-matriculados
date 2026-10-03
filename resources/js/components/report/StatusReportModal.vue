<!-- StatusReportModal.vue -->
<script setup lang="ts">
import Modal from '@/components/Modal.vue'
import { ref } from 'vue'

interface Props {
  show: boolean
}

defineProps<Props>()
const emit = defineEmits<{ close: [] }>()

const statusData = {
  active: {
    count: 198,
    percentage: 80.8,
    trend: '+5.2%',
    trendUp: true
  },
  inactive: {
    count: 32,
    percentage: 13.1,
    trend: '-2.1%',
    trendUp: false
  },
  suspended: {
    count: 15,
    percentage: 6.1,
    trend: '+1.3%',
    trendUp: true
  }
}

const monthlyTrend = [
  { month: 'Enero', active: 185, inactive: 35, suspended: 12 },
  { month: 'Febrero', active: 190, inactive: 34, suspended: 13 },
  { month: 'Marzo', active: 195, inactive: 33, suspended: 14 },
  { month: 'Abril', active: 198, inactive: 32, suspended: 15 },
]

const recentChanges = [
  { id: 1, member: 'Juan Pérez', from: 'Inactivo', to: 'Activo', date: '2024-01-05', reason: 'Reactivación por pago' },
  { id: 2, member: 'María González', from: 'Activo', to: 'Suspendido', date: '2024-01-04', reason: 'Falta de pago' },
  { id: 3, member: 'Carlos Rodríguez', from: 'Suspendido', to: 'Activo', date: '2024-01-03', reason: 'Regularización' },
]
</script>

<template>
  <Modal :show="show" max-width="6xl" @close="emit('close')">
    <div class="p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            Reporte de Estados
          </h2>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Análisis detallado de estados de matriculados
          </p>
        </div>
        <button
          @click="emit('close')"
          class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Cards de estados -->
      <div class="grid grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/20 rounded-lg p-6 border-2 border-green-200 dark:border-green-700">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-medium text-green-600 dark:text-green-400">ACTIVOS</h3>
            <span :class="[statusData.active.trendUp ? 'text-green-600' : 'text-red-600', 'text-xs font-semibold']">
              {{ statusData.active.trend }}
            </span>
          </div>
          <p class="text-4xl font-bold text-green-900 dark:text-green-200 mb-1">
            {{ statusData.active.count }}
          </p>
          <p class="text-sm text-green-700 dark:text-green-300">
            {{ statusData.active.percentage }}% del total
          </p>
          <div class="mt-4 bg-green-200 dark:bg-green-700 rounded-full h-2">
            <div class="bg-green-500 dark:bg-green-400 rounded-full h-2" :style="`width: ${statusData.active.percentage}%`"></div>
          </div>
        </div>

        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 rounded-lg p-6 border-2 border-gray-200 dark:border-gray-600">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">INACTIVOS</h3>
            <span :class="[statusData.inactive.trendUp ? 'text-green-600' : 'text-red-600', 'text-xs font-semibold']">
              {{ statusData.inactive.trend }}
            </span>
          </div>
          <p class="text-4xl font-bold text-gray-900 dark:text-gray-200 mb-1">
            {{ statusData.inactive.count }}
          </p>
          <p class="text-sm text-gray-700 dark:text-gray-300">
            {{ statusData.inactive.percentage }}% del total
          </p>
          <div class="mt-4 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
            <div class="bg-gray-500 dark:bg-gray-400 rounded-full h-2" :style="`width: ${statusData.inactive.percentage}%`"></div>
          </div>
        </div>

        <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/20 rounded-lg p-6 border-2 border-red-200 dark:border-red-700">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-medium text-red-600 dark:text-red-400">SUSPENDIDOS</h3>
            <span :class="[statusData.suspended.trendUp ? 'text-green-600' : 'text-red-600', 'text-xs font-semibold']">
              {{ statusData.suspended.trend }}
            </span>
          </div>
          <p class="text-4xl font-bold text-red-900 dark:text-red-200 mb-1">
            {{ statusData.suspended.count }}
          </p>
          <p class="text-sm text-red-700 dark:text-red-300">
            {{ statusData.suspended.percentage }}% del total
          </p>
          <div class="mt-4 bg-red-200 dark:bg-red-700 rounded-full h-2">
            <div class="bg-red-500 dark:bg-red-400 rounded-full h-2" :style="`width: ${statusData.suspended.percentage}%`"></div>
          </div>
        </div>
      </div>

      <!-- Tendencia mensual -->
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tendencia Mensual</h3>
      <div class="overflow-x-auto mb-6">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Mes</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Activos</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Inactivos</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Suspendidos</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="(data, index) in monthlyTrend" :key="index">
              <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ data.month }}</td>
              <td class="px-4 py-3 text-sm text-center text-green-600 dark:text-green-400 font-semibold">{{ data.active }}</td>
              <td class="px-4 py-3 text-sm text-center text-gray-600 dark:text-gray-400">{{ data.inactive }}</td>
              <td class="px-4 py-3 text-sm text-center text-red-600 dark:text-red-400 font-semibold">{{ data.suspended }}</td>
              <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-white font-semibold">
                {{ data.active + data.inactive + data.suspended }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Cambios recientes -->
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Cambios Recientes de Estado</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fecha</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Matriculado</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado Anterior</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado Nuevo</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Motivo</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="change in recentChanges" :key="change.id">
              <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ change.date }}</td>
              <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ change.member }}</td>
              <td class="px-4 py-3 text-sm">
                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                  {{ change.from }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm">
                <span :class="[
                  change.to === 'Activo' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                  change.to === 'Suspendido' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                  'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                  'px-2 py-1 rounded-full text-xs font-medium'
                ]">
                  {{ change.to }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ change.reason }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Acciones -->
      <div class="flex justify-end gap-3 mt-6">
        <button
          @click="emit('close')"
          class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
        >
          Cerrar
        </button>
        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
          Exportar PDF
        </button>
      </div>
    </div>
  </Modal>
</template>
