<!-- CustomReportModal.vue -->
<script setup lang="ts">
import Modal from '@/components/Modal.vue'
import { ref } from 'vue'

interface Props {
  show: boolean
}

defineProps<Props>()
const emit = defineEmits<{ close: [] }>()

const selectedFilters = ref({
  dateFrom: '',
  dateTo: '',
  status: [] as string[],
  locations: [] as string[],
  columns: ['enrollment', 'name', 'email', 'status'] as string[]
})

const availableStatuses = ['Activo', 'Inactivo', 'Suspendido']
const availableLocations = ['Comodoro Rivadavia', 'Rada Tilly', 'Caleta Olivia', 'Sarmiento']
const availableColumns = [
  { id: 'enrollment', label: 'N° Matrícula' },
  { id: 'name', label: 'Nombre' },
  { id: 'email', label: 'Email' },
  { id: 'phone', label: 'Teléfono' },
  { id: 'status', label: 'Estado' },
  { id: 'location', label: 'Ubicación' },
  { id: 'registration_date', label: 'Fecha de Registro' },
]

const generateReport = () => {
  console.log('Generando reporte con filtros:', selectedFilters.value)
  // Aquí iría la lógica para generar el reporte
  alert('Generando reporte personalizado...')
}
</script>

<template>
  <Modal :show="show" max-width="4xl" @close="emit('close')">
    <div class="p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            Reporte Personalizado
          </h2>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Configura los filtros y columnas para tu reporte
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

      <div class="space-y-6">
        <!-- Filtro de fechas -->
        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Rango de Fechas</h3>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Desde</label>
              <input
                v-model="selectedFilters.dateFrom"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hasta</label>
              <input
                v-model="selectedFilters.dateTo"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>
        </div>

        <!-- Filtro de estados -->
        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Estados</h3>
          <div class="flex flex-wrap gap-2">
            <label
              v-for="status in availableStatuses"
              :key="status"
              class="flex items-center px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
              <input
                v-model="selectedFilters.status"
                :value="status"
                type="checkbox"
                class="mr-2 rounded text-blue-600 focus:ring-blue-500"
              />
              <span class="text-sm text-gray-700 dark:text-gray-300">{{ status }}</span>
            </label>
          </div>
        </div>

        <!-- Filtro de ubicaciones -->
        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Ubicaciones</h3>
          <div class="flex flex-wrap gap-2">
            <label
              v-for="location in availableLocations"
              :key="location"
              class="flex items-center px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
              <input
                v-model="selectedFilters.locations"
                :value="location"
                type="checkbox"
                class="mr-2 rounded text-blue-600 focus:ring-blue-500"
              />
              <span class="text-sm text-gray-700 dark:text-gray-300">{{ location }}</span>
            </label>
          </div>
        </div>

        <!-- Selección de columnas -->
        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Columnas a Incluir</h3>
          <div class="grid grid-cols-2 gap-2">
            <label
              v-for="column in availableColumns"
              :key="column.id"
              class="flex items-center px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
              <input
                v-model="selectedFilters.columns"
                :value="column.id"
                type="checkbox"
                class="mr-2 rounded text-blue-600 focus:ring-blue-500"
              />
              <span class="text-sm text-gray-700 dark:text-gray-300">{{ column.label }}</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Acciones -->
      <div class="flex justify-end gap-3 mt-6">
        <button
          @click="emit('close')"
          class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
        >
          Cancelar
        </button>
        <button
          @click="generateReport"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        >
          Generar Reporte
        </button>
      </div>
    </div>
  </Modal>
</template>