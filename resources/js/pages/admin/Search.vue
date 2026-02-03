<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import { type BreadcrumbItem } from '@/types'
import { Head, router, usePage} from '@inertiajs/vue3'
import { ref, computed , watch} from 'vue'
import { route } from 'ziggy-js'
import MemberModal from '@/pages/Member/Show.vue'
import MemberEditModal from '@/pages/Member/Edit.vue'
import MemberCreateModal from '@/pages/Member/Create.vue' 
import Swal from 'sweetalert2' 
import members from '@/routes/members'

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
  province: string // Nuevo
  registration_date: string
  status: string
  dni: string     // Nuevo
  address: string // Nuevo
  image?: string  // Nuevo (opcional)
}

// Props que recibirías desde el controlador
interface Props {
  members: Member[]
  cities: Array<{ id: number; name: string; province_id: number }>
  provinces: Array<{ id: number; name: string }>
}

const props = withDefaults(defineProps<Props>(), {
  members: () => [
    /*{
      id: 1,
      enrollment_number: 'MAT-2024-001',
      name: 'Juan Pérez',
      email: 'juan@example.com',
      phone: '+54 297 123-4567',
      location: 'Comodoro Rivadavia',
      province: 'Chubut',
      status: 'active',
      registration_date: '2024-01-15',
      dni: '12345678',
      address: 'Calle 1, 123'
    },
    {
      id: 2,
      enrollment_number: 'MAT-2024-002',
      name: 'María González',
      email: 'maria@example.com',
      phone: '+54 297 234-5678',
      location: 'Rada Tilly',
      province: 'Chubut',
      status: 'active',
      registration_date: '2024-02-20',
      dni: '23456789',
      address: 'Calle 2, 456'
    },
    {
      id: 3,
      enrollment_number: 'MAT-2024-003',
      name: 'Carlos Rodríguez',
      email: 'carlos@example.com',
      phone: '+54 297 345-6789',
      location: 'Comodoro Rivadavia',
      province: 'Chubut',
      status: 'inactive',
      registration_date: '2024-03-10',
      dni: '34567890',
      address: 'Calle 3, 789'
    },
    {
      id: 4,
      enrollment_number: 'MAT-2024-004',
      name: 'Ana Martínez',
      email: 'ana@example.com',
      phone: '+54 297 456-7890',
      location: 'Caleta Olivia',
      province: 'Chubut',
      status: 'suspended',
      registration_date: '2024-04-05',
      dni: '45678901',
      address: 'Calle 4, 012'
    }
  */]
})

const showModal = ref(false)
const selectedMember = ref<Member | null>(null)
const searchQuery = ref('')
const showCreateModal = ref(false)

// --- CONFIGURACIÓN DE ALERTAS (SWEETALERT) ---
const page = usePage<any>();
  
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
});

// Detectar mensajes del backend automáticamente
watch(() => page.props.flash?.message, (message) => {
  if (message) {
    Toast.fire({
      icon: 'success',
      title: message
    });
  }
}, { deep: true });


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


const openCreateModal = () => {
  showCreateModal.value = true
}

const closeCreateModal = () => {
  showCreateModal.value = false
}

const viewMember = (member: Member) => {
  // En lugar de navegar, guardamos el miembro y abrimos el modal
  selectedMember.value = member
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  // Opcional: limpiar seleccionado después de una animación
  setTimeout(() => selectedMember.value = null, 300) 
}

// --- LÓGICA MODAL "EDITAR" (NUEVA) ---
const showEditModal = ref(false)
const memberToEdit = ref<Member | null>(null)

// MODIFICAMOS LA FUNCIÓN EXISTENTE
const editMember = (member: Member) => {
  // Antes: router.visit(route('members.edit', { member: id }))
  // Ahora: Abrimos el modal y le pasamos el objeto completo
  memberToEdit.value = member
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
  // No limpiamos memberToEdit inmediatamente para evitar parpadeos si el modal se cierra lento
  setTimeout(() => memberToEdit.value = null, 300) 
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
          @click="openCreateModal"
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
                      @click="viewMember(member)"
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
                      @click="editMember(member)"
                      class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 transition-colors"
                      title="Editar"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
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
    <MemberModal 
        :show="showModal" 
        :member="selectedMember" 
        @close="closeModal" 
    />
    <MemberEditModal 
      :show="showEditModal" 
      :member="memberToEdit" 
      :provinces="props.provinces"  :cities="props.cities"        
      @close="closeEditModal" 
    />
    <MemberCreateModal
        :show="showCreateModal"
        :provinces="props.provinces"
        :cities="props.cities"
        @close="closeCreateModal"
    />
  </AppLayout>
</template>