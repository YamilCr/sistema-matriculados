<!-- ExportReportModal.vue -->
<script setup lang="ts">
import Modal from '@/components/Modal.vue'
import { ref } from 'vue'

interface Props {
  show: boolean
}

defineProps<Props>()
const emit = defineEmits<{ close: [] }>()

const exportFormats = [
  { id: 'excel', name: 'Excel (.xlsx)', icon: '📊', description: 'Ideal para análisis y edición de datos' },
  { id: 'pdf', name: 'PDF (.pdf)', icon: '📄', description: 'Formato para imprimir o compartir' },
  { id: 'csv', name: 'CSV (.csv)', icon: '📋', description: 'Compatible con cualquier aplicación' },
]

const selectedFormat = ref('excel')

const exportData = () => {
  console.log('Exportando en formato:', selectedFormat.value)
  alert(`Exportando datos en formato ${selectedFormat.value.toUpperCase()}...`)
  // Aquí iría la lógica real de exportación
}
</script>

<template>
  <Modal :show="show" max-width="3xl" @close="emit('close')">
    <div class="p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            Exportar Datos
          </h2>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Selecciona el formato de exportación
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

      <!-- Opciones de formato -->
      <div class="space-y-3 mb-6">
        <label
          v-for="format in exportFormats"
          :key="format.id"
          class="flex items-start p-4 bg-white dark:bg-gray-800 border-2 rounded-lg cursor-pointer transition-all"
          :class="selectedFormat === format.id 
            ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' 
            : 'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500'"
        >
          <input
            v-model="selectedFormat"
            :value="format.id"
            type="radio"
            class="mt-1 mr-3 text-blue-600 focus:ring-blue-500"
          />
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
              <span class="text-2xl">{{ format.icon }}</span>
              <span class="font-semibold text-gray-900 dark:text-white">{{ format.name }}</span>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ format.description }}</p>
          </div>
        </label>
      </div>

      <!-- Opciones adicionales -->
      <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 mb-6">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Opciones de Exportación</h3>
        <div class="space-y-2">
          <label class="flex items-center">
            <input type="checkbox" class="mr-2 rounded text-blue-600 focus:ring-blue-500" checked />
            <span class="text-sm text-gray-700 dark:text-gray-300">Incluir encabezados</span>
          </label>
          <label class="flex items-center">
            <input type="checkbox" class="mr-2 rounded text-blue-600 focus:ring-blue-500" checked />
            <span class="text-sm text-gray-700 dark:text-gray-300">Incluir fecha de generación</span>
          </label>
          <label class="flex items-center">
            <input type="checkbox" class="mr-2 rounded text-blue-600 focus:ring-blue-500" />
            <span class="text-sm text-gray-700 dark:text-gray-300">Solo registros activos</span>
          </label>
        </div>
      </div>

      <!-- Información -->
      <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
        <div class="flex items-start">
          <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <div class="text-sm text-blue-800 dark:text-blue-300">
            <p class="font-medium mb-1">Información sobre la exportación</p>
            <p>Se exportarán todos los registros visibles según los filtros aplicados. El archivo se descargará automáticamente.</p>
          </div>
        </div>
      </div>

      <!-- Acciones -->
      <div class="flex justify-end gap-3">
        <button
          @click="emit('close')"
          class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
        >
          Cancelar
        </button>
        <button
          @click="exportData"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
          </svg>
          Exportar
        </button>
      </div>
    </div>
  </Modal>
</template>