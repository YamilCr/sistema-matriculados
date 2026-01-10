<!-- MembersReportModal.vue -->
<script setup lang="ts">
import Modal from '@/components/Modal.vue'

interface Props {
  show: boolean
}

defineProps<Props>()
const emit = defineEmits<{ close: [] }>()

// Datos estáticos de ejemplo
const members = [
  { id: 1, name: 'Juan Pérez', enrollment: 'MAT-2024-001', status: 'Activo', location: 'Comodoro Rivadavia' },
  { id: 2, name: 'María González', enrollment: 'MAT-2024-002', status: 'Activo', location: 'Rada Tilly' },
  { id: 3, name: 'Carlos Rodríguez', enrollment: 'MAT-2024-003', status: 'Inactivo', location: 'Caleta Olivia' },
  { id: 4, name: 'Ana Martínez', enrollment: 'MAT-2024-004', status: 'Suspendido', location: 'Comodoro Rivadavia' },
]

const stats = {
  total: 245,
  active: 198,
  inactive: 32,
  suspended: 15
}
</script>

<template>
  <Modal :show="show" max-width="5xl" @close="emit('close')">
    <div class="p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            Reporte de Matriculados
          </h2>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Lista completa de miembros del sistema
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

      <!-- Estadísticas -->
      <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
          <p class="text-sm text-blue-600 dark:text-blue-400">Total</p>
          <p class="text-2xl font-bold text-blue-900 dark:text-blue-300">{{ stats.total }}</p>
        </div>
        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
          <p class="text-sm text-green-600 dark:text-green-400">Activos</p>
          <p class="text-2xl font-bold text-green-900 dark:text-green-300">{{ stats.active }}</p>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <p class="text-sm text-gray-600 dark:text-gray-400">Inactivos</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-gray-300">{{ stats.inactive }}</p>
        </div>
        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
          <p class="text-sm text-red-600 dark:text-red-400">Suspendidos</p>
          <p class="text-2xl font-bold text-red-900 dark:text-red-300">{{ stats.suspended }}</p>
        </div>
      </div>

      <!-- Tabla -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Matrícula</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nombre</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ubicación</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="member in members" :key="member.id">
              <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ member.enrollment }}</td>
              <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ member.name }}</td>
              <td class="px-4 py-3 text-sm">
                <span :class="[
                  member.status === 'Activo' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                  member.status === 'Inactivo' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' :
                  'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                  'px-2 py-1 rounded-full text-xs font-medium'
                ]">
                  {{ member.status }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ member.location }}</td>
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
        <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
          Exportar Excel
        </button>
      </div>
    </div>
  </Modal>
</template>