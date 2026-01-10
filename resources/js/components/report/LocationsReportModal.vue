<!-- =========================================== -->
<!-- LocationsReportModal.vue -->
<!-- =========================================== -->
<script setup lang="ts">
import Modal from '@/components/Modal.vue'

interface Props {
  show: boolean
}

defineProps<Props>()
const emit = defineEmits<{ close: [] }>()

const locationStats = [
  { location: 'Comodoro Rivadavia', total: 145, active: 120, inactive: 20, suspended: 5 },
  { location: 'Rada Tilly', total: 45, active: 38, inactive: 5, suspended: 2 },
  { location: 'Caleta Olivia', total: 35, active: 28, inactive: 5, suspended: 2 },
  { location: 'Sarmiento', total: 20, active: 12, inactive: 2, suspended: 6 },
]
</script>

<template>
  <Modal :show="show" max-width="4xl" @close="emit('close')">
    <div class="p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            Reporte por Ubicación
          </h2>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Distribución de matriculados por localidad
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

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ubicación</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Activos</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Inactivos</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Suspendidos</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">% del Total</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="stat in locationStats" :key="stat.location">
              <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ stat.location }}</td>
              <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-white">{{ stat.total }}</td>
              <td class="px-4 py-3 text-sm text-center">
                <span class="text-green-600 dark:text-green-400 font-semibold">{{ stat.active }}</span>
              </td>
              <td class="px-4 py-3 text-sm text-center">
                <span class="text-gray-600 dark:text-gray-400">{{ stat.inactive }}</span>
              </td>
              <td class="px-4 py-3 text-sm text-center">
                <span class="text-red-600 dark:text-red-400 font-semibold">{{ stat.suspended }}</span>
              </td>
              <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-white">
                {{ ((stat.total / 245) * 100).toFixed(1) }}%
              </td>
            </tr>
          </tbody>
        </table>
      </div>

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