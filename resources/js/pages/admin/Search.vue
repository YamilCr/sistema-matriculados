<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import { type BreadcrumbItem } from '@/types'
import { Head, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Matriculados', href: dashboard().url },
]

interface Member {
  id: number
  enrollment_number: string
  name: string
  email: string
  phone: string
  location: string
  status: 'active' | 'inactive' | 'suspended'
  registration_date: string
}

// Props que recibirías desde el controlador
interface Props {
  members: Member[]
}

const props = withDefaults(defineProps<Props>(), {
  members: () => [
    {
      id: 1,
      enrollment_number: 'MAT-2024-001',
      name: 'Juan Pérez',
      email: 'juan@example.com',
      phone: '+54 297 123-4567',
      location: 'Comodoro Rivadavia',
      status: 'active',
      registration_date: '2024-01-15'
    },
    {
      id: 2,
      enrollment_number: 'MAT-2024-002',
      name: 'María González',
      email: 'maria@example.com',
      phone: '+54 297 234-5678',
      location: 'Rada Tilly',
      status: 'active',
      registration_date: '2024-02-20'
    },
    {
      id: 3,
      enrollment_number: 'MAT-2024-003',
      name: 'Carlos Rodríguez',
      email: 'carlos@example.com',
      phone: '+54 297 345-6789',
      location: 'Comodoro Rivadavia',
      status: 'inactive',
      registration_date: '2024-03-10'
    },
    {
      id: 4,
      enrollment_number: 'MAT-2024-004',
      name: 'Ana Martínez',
      email: 'ana@example.com',
      phone: '+54 297 456-7890',
      location: 'Caleta Olivia',
      status: 'suspended',
      registration_date: '2024-04-05'
    }
  ]
})

const searchQuery = ref('')

const filteredMembers = computed(() => {
  if (!searchQuery.value) return props.members
  
  const query = searchQuery.value.toLowerCase()
  return props.members.filter(member => 
    member.enrollment_number.toLowerCase().includes(query) ||
    member.name.toLowerCase().includes(query)
  )
})

const getStatusColor = (status: string) => {
  const colors = {
    active: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    inactive: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    suspended: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
  }
  return colors[status as keyof typeof colors] || colors.inactive
}

const getStatusText = (status: string) => {
  const texts = {
    active: 'Activo',
    inactive: 'Inactivo',
    suspended: 'Suspendido'
  }
  return texts[status as keyof typeof texts] || status
}

const viewMember = (id: number) => {
  router.visit(route('members.show', { member: id }))
}

const editMember = (id: number) => {
  router.visit(route('members.edit', { member: id }))
}

const deleteMember = (id: number, name: string) => {
  if (confirm(`¿Estás seguro de eliminar al matriculado ${name}?`)) {
    router.delete(route('members.destroy', { member: id }), {
      preserveScroll: true,
      onSuccess: () => {
        alert('Matriculado eliminado exitosamente')
      }
    })
  }
}
</script>

<template>
  <Head title="Matriculados" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Matriculados
          </h1>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Gestiona todos los matriculados del sistema
          </p>
        </div>
        <button
          @click="router.visit(route('members.create'))"
          class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Nuevo Matriculado
        </button>
      </div>

      <!-- Search Bar -->
      <div class="mb-6">
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Buscar por número de matrícula o nombre..."
            class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
          Mostrando {{ filteredMembers.length }} de {{ members.length }} matriculados
        </p>
      </div>

      <!-- Table -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  N° Matrícula
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Nombre
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Email
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Teléfono
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Ubicación
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Estado
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Fecha Registro
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Acciones
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr
                v-for="member in filteredMembers"
                :key="member.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              >
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                  {{ member.enrollment_number }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  {{ member.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                  {{ member.email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                  {{ member.phone }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                  {{ member.location }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[getStatusColor(member.status), 'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full']">
                    {{ getStatusText(member.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                  {{ new Date(member.registration_date).toLocaleDateString('es-AR') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end gap-2">
                    <!-- Ver -->
                    <button
                      @click="viewMember(member.id)"
                      class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                      title="Ver detalles"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>
                    
                    <!-- Editar -->
                    <button
                      @click="editMember(member.id)"
                      class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 transition-colors"
                      title="Editar"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    
                    <!-- Eliminar -->
                    <button
                      @click="deleteMember(member.id, member.name)"
                      class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                      title="Eliminar"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              
              <!-- Empty State -->
              <tr v-if="filteredMembers.length === 0">
                <td colspan="8" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-lg font-medium">No se encontraron resultados</p>
                    <p class="text-sm mt-1">Intenta con otro término de búsqueda</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>