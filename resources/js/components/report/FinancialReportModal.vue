<!-- FinancialReportModal.vue -->
<!-- =========================================== -->
<script setup lang="ts">
import Modal from '@/components/Modal.vue'

interface Props {
  show: boolean
}

defineProps<Props>()
const emit = defineEmits<{ close: [] }>()

const financialData = {
  totalIncome: 2450000,
  pendingPayments: 345000,
  collected: 2105000,
  expenses: 890000,
  balance: 1215000
}

const recentTransactions = [
  { id: 1, member: 'Juan Pérez', amount: 15000, date: '2024-01-05', type: 'Pago' },
  { id: 2, member: 'María González', amount: 15000, date: '2024-01-06', type: 'Pago' },
  { id: 3, member: 'Gastos Admin', amount: -5000, date: '2024-01-07', type: 'Gasto' },
  { id: 4, member: 'Carlos Rodríguez', amount: 15000, date: '2024-01-08', type: 'Pago' },
]
</script>

<template>
  <Modal :show="show" max-width="5xl" @close="emit('close')">
    <div class="p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            Reporte Financiero
          </h2>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Resumen de ingresos y gastos del período
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

      <!-- Métricas financieras -->
      <div class="grid grid-cols-5 gap-4 mb-6">
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
          <p class="text-sm text-blue-600 dark:text-blue-400">Ingresos Totales</p>
          <p class="text-xl font-bold text-blue-900 dark:text-blue-300">
            ${{ financialData.totalIncome.toLocaleString() }}
          </p>
        </div>
        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
          <p class="text-sm text-yellow-600 dark:text-yellow-400">Pendientes</p>
          <p class="text-xl font-bold text-yellow-900 dark:text-yellow-300">
            ${{ financialData.pendingPayments.toLocaleString() }}
          </p>
        </div>
        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
          <p class="text-sm text-green-600 dark:text-green-400">Cobrado</p>
          <p class="text-xl font-bold text-green-900 dark:text-green-300">
            ${{ financialData.collected.toLocaleString() }}
          </p>
        </div>
        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
          <p class="text-sm text-red-600 dark:text-red-400">Gastos</p>
          <p class="text-xl font-bold text-red-900 dark:text-red-300">
            ${{ financialData.expenses.toLocaleString() }}
          </p>
        </div>
        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
          <p class="text-sm text-purple-600 dark:text-purple-400">Balance</p>
          <p class="text-xl font-bold text-purple-900 dark:text-purple-300">
            ${{ financialData.balance.toLocaleString() }}
          </p>
        </div>
      </div>

      <!-- Transacciones recientes -->
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Transacciones Recientes</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fecha</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Concepto</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipo</th>
              <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Monto</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="transaction in recentTransactions" :key="transaction.id">
              <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ transaction.date }}</td>
              <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ transaction.member }}</td>
              <td class="px-4 py-3 text-sm">
                <span :class="[
                  transaction.type === 'Pago' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                  'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                  'px-2 py-1 rounded-full text-xs font-medium'
                ]">
                  {{ transaction.type }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm text-right font-semibold" :class="transaction.amount > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                {{ transaction.amount > 0 ? '+' : '' }}${{ transaction.amount.toLocaleString() }}
              </td>
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