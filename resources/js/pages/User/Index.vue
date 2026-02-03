<script setup lang="ts">
import UserCreateModal from '@/components/user/UserCreateModal.vue'
import UserEditModal from '@/components/user/UserEditModal.vue'
import UserViewModal from '@/components/user/UserViewModal.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import users from '@/routes/users'
import { type BreadcrumbItem } from '@/types'
import { Head, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'


const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Usuarios', href: dashboard().url },
]

interface Role {
  id: number
  name: string
}

interface Member {
  id: number
  first_name: string
  last_name: string
  image?: string
  registration_number: string
}

interface User {
  id: number
  name: string
  email: string
  image?: string
  role_id: number
  role: Role
  member_id?: number
  member?: Member
  is_active: boolean
  created_at: string
}

interface Props {
  users: User[]
  roles: Role[]
  members: Member[]
}

const props = withDefaults(defineProps<Props>(), {
  users: () => [],
  roles: () => [
    { id: 1, name: 'Admin' },
    { id: 2, name: 'Member' },
    { id: 3, name: 'Staff' }
  ],
  members: () => []
})

const searchQuery = ref('')
const selectedRole = ref<number | null>(null)
const selectedStatus = ref<string | null>(null)

// Modales
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showViewModal = ref(false)
const selectedUser = ref<User | null>(null)

const filteredUsers = computed(() => {
  let filtered = props.users

  // Filtro por búsqueda
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(user => 
      user.name.toLowerCase().includes(query) ||
      user.email.toLowerCase().includes(query)
    )
  }

  // Filtro por rol
  if (selectedRole.value !== null) {
    filtered = filtered.filter(user => user.role_id === selectedRole.value)
  }

  // Filtro por estado
  if (selectedStatus.value !== null) {
    const isActive = selectedStatus.value === 'active'
    filtered = filtered.filter(user => user.is_active === isActive)
  }

  return filtered
})

const getRoleBadgeColor = (roleId: number) => {
  const colors = {
    1: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200', // Admin
    2: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',       // Member
    3: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'    // Staff
  }
  return colors[roleId as keyof typeof colors] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
}

const openCreateModal = () => {
  showCreateModal.value = true
}

const closeCreateModal = () => {
  showCreateModal.value = false
}

const viewUser = (user: User) => {
  selectedUser.value = user
  showViewModal.value = true
}

const closeViewModal = () => {
  showViewModal.value = false
  setTimeout(() => selectedUser.value = null, 300)
}

const editUser = (user: User) => {
  selectedUser.value = user
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
  setTimeout(() => selectedUser.value = null, 300)
}

const toggleUserStatus = (userId: number, currentStatus: boolean) => {
  const action = currentStatus ? 'desactivar' : 'activar'
  if (confirm(`¿Estás seguro de ${action} este usuario?`)) {
    router.patch((users.toggle.status(userId)), {
      preserveScroll: true,
      onSuccess: () => {
        alert(`Usuario ${action === 'activar' ? 'activado' : 'desactivado'} exitosamente`)
      }
    })
  }
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedRole.value = null
  selectedStatus.value = null
}
</script>

<template>
  <Head title="Gestión de Usuarios" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Gestión de Usuarios
          </h1>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Administra los usuarios del sistema y sus roles
          </p>
        </div>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Nuevo Usuario
        </button>
      </div>

      <!-- Filtros -->
      <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Búsqueda -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Buscar usuario
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Buscar por nombre o email..."
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>

          <!-- Filtro por Rol -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Rol
            </label>
            <select
              v-model="selectedRole"
              class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option :value="null">Todos los roles</option>
              <option v-for="role in roles" :key="role.id" :value="role.id">
                {{ role.name }}
              </option>
            </select>
          </div>

          <!-- Filtro por Estado -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Estado
            </label>
            <select
              v-model="selectedStatus"
              class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option :value="null">Todos</option>
              <option value="active">Activos</option>
              <option value="inactive">Inactivos</option>
            </select>
          </div>
        </div>

        <!-- Botón limpiar filtros -->
        <div class="mt-4 flex items-center justify-between">
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Mostrando {{ filteredUsers.length }} de {{ users.length }} usuarios
          </p>
          <button
            v-if="searchQuery || selectedRole !== null || selectedStatus !== null"
            @click="clearFilters"
            class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium"
          >
            Limpiar filtros
          </button>
        </div>
      </div>

      <!-- Tabla de Usuarios -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Usuario
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Email
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Rol
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Matriculado
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
                v-for="user in filteredUsers"
                :key="user.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              >
                <!-- Usuario (con imagen) -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="h-10 w-10 flex-shrink-0">
                      <img
                        v-if="user.image"
                        :src="'/storage/' + user.image"
                        :alt="user.name"
                        class="h-10 w-10 rounded-full object-cover"
                      />
                      <div
                        v-else
                        class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center"
                      >
                        <span class="text-gray-600 dark:text-gray-300 font-medium text-sm">
                          {{ user.name.charAt(0).toUpperCase() }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ user.name }}
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Email -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                  {{ user.email }}
                </td>

                <!-- Rol -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[getRoleBadgeColor(user.role_id), 'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full']">
                    {{ user.role.name }}
                  </span>
                </td>

                <!-- Matriculado -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                  <span v-if="user.member">
                    {{ user.member.registration_number }}
                  </span>
                  <span v-else class="text-gray-400 dark:text-gray-500">
                    N/A
                  </span>
                </td>

                <!-- Estado -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    user.is_active 
                      ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' 
                      : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                    'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full'
                  ]">
                    {{ user.is_active ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>

                <!-- Fecha Registro -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                  {{ new Date(user.created_at).toLocaleDateString('es-AR') }}
                </td>

                <!-- Acciones -->
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end gap-2">
                    <!-- Ver -->
                    <button
                      @click="viewUser(user)"
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
                      @click="editUser(user)"
                      class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 transition-colors"
                      title="Editar"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>

                    <!-- Toggle Estado -->
                    <button
                      @click="toggleUserStatus(user.id, user.is_active)"
                      :class="[
                        user.is_active 
                          ? 'text-orange-600 hover:text-orange-900 dark:text-orange-400 dark:hover:text-orange-300' 
                          : 'text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300',
                        'transition-colors'
                      ]"
                      :title="user.is_active ? 'Desactivar' : 'Activar'"
                    >
                      <svg v-if="user.is_active" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                      </svg>
                      <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              
              <!-- Empty State -->
              <tr v-if="filteredUsers.length === 0">
                <td colspan="7" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <p class="text-lg font-medium">No se encontraron usuarios</p>
                    <p class="text-sm mt-1">Intenta ajustar los filtros de búsqueda</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modales -->
    <UserViewModal 
      :show="showViewModal" 
      :user="selectedUser" 
      @close="closeViewModal" 
    />
    
    <UserEditModal 
      :show="showEditModal" 
      :user="selectedUser" 
      :roles="roles" 
      :members="members" 
      @close="closeEditModal" 
    />
    
    <UserCreateModal 
      :show="showCreateModal" 
      :roles="roles" 
      :members="members" 
      @close="closeCreateModal" 
    />
  </AppLayout>
</template>