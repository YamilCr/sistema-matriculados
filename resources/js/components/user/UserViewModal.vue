<script setup lang="ts">
import Modal from '@/components/Modal.vue'
import { Button } from '@/components/ui/button'

interface Role { id: number; name: string }
interface Member { id: number; name: string; registration_number: string }
interface User {
  id: number; name: string; email: string; image?: string;
  role_id: number; role: Role; member_id?: number;
  member?: Member; is_active: boolean; created_at: string;
}

interface Props {
  show: boolean
  user: User | null
}

defineProps<Props>()
const emit = defineEmits<{ close: [] }>()

const getRoleBadgeColor = (roleId: number) => {
  const colors = {
    1: 'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400 border-purple-200 dark:border-purple-500/20',
    2: 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 border-blue-200 dark:border-blue-500/20',
    3: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20'
  }
  return colors[roleId as keyof typeof colors] || 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400 border-slate-200 dark:border-slate-700'
}
</script>

<template>
  <Modal :show="show" max-width="2xl" @close="emit('close')" position="center">
    <div class="p-0 overflow-hidden dark:bg-slate-900 transition-colors duration-500" v-if="user">
      
      <div class="h-24 bg-gradient-to-r from-emerald-600 to-teal-700 dark:from-emerald-900 dark:to-teal-950 relative">
        <button 
          @click="emit('close')" 
          class="absolute top-4 right-4 p-2 rounded-full bg-black/10 text-white hover:bg-black/20 transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="px-8 pb-8">
        <div class="relative -mt-12 mb-6 flex flex-col items-center sm:items-start sm:flex-row sm:gap-6">
          <div class="relative">
            <div class="h-28 w-28 rounded-full border-4 border-white dark:border-slate-900 shadow-2xl overflow-hidden bg-slate-200 dark:bg-slate-800 flex items-center justify-center">
              <img
                v-if="user.image"
                :src="'/storage/' + user.image"
                class="h-full w-full object-cover"
              />
              <div v-else class="h-full w-full flex items-center justify-center bg-emerald-500 text-white font-black text-4xl italic">
                {{ user.name.charAt(0).toUpperCase() }}
              </div>
            </div>
            <div 
              class="absolute bottom-2 right-2 h-5 w-5 rounded-full border-4 border-white dark:border-slate-900"
              :class="user.is_active ? 'bg-emerald-500' : 'bg-red-500'"
            ></div>
          </div>

          <div class="mt-4 sm:mt-14 space-y-1 text-center sm:text-left">
            <h3 class="text-2xl font-black text-slate-900 dark:text-white leading-tight">
              {{ user.name }}
            </h3>
            <p class="text-slate-500 dark:text-slate-400 font-medium">{{ user.email }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8 py-6 border-t dark:border-white/5">
          
          <div class="space-y-1.5">
            <span class="text-[10px] uppercase font-black text-slate-400 dark:text-slate-500 tracking-widest">Rol del Sistema</span>
            <div>
              <span :class="[getRoleBadgeColor(user.role_id), 'px-3 py-1 text-xs font-bold rounded-lg border']">
                {{ user.role.name }}
              </span>
            </div>
          </div>

          <div class="space-y-1.5">
            <span class="text-[10px] uppercase font-black text-slate-400 dark:text-slate-500 tracking-widest">Estado de Acceso</span>
            <div>
              <span :class="[
                user.is_active 
                  ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border-emerald-100 dark:border-emerald-500/20' 
                  : 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400 border-red-100 dark:border-red-500/20',
                'px-3 py-1 text-xs font-bold rounded-lg border'
              ]">
                {{ user.is_active ? 'Cuenta Activa' : 'Cuenta Suspendida' }}
              </span>
            </div>
          </div>

          <div v-if="user.member" class="col-span-1 md:col-span-2 bg-slate-50 dark:bg-white/5 rounded-2xl p-4 border border-slate-100 dark:border-white/5">
            <span class="text-[10px] uppercase font-black text-slate-400 dark:text-slate-500 tracking-[0.2em] mb-3 block">Matriculado Vinculado</span>
            <div class="flex items-center gap-4">
              <div class="h-10 w-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-600/20 text-lg">
                💼
              </div>
              <div>
                <p class="text-sm font-bold text-slate-900 dark:text-white">
                  {{ user.member.name }}
                </p>
                <p class="text-lg font-mono font-medium text-emerald-600 dark:text-emerald-400">
                  N° MATRÍCULA: {{ user.member.registration_number }}
                </p>
              </div>
            </div>
          </div>

          <div class="col-span-1 md:col-span-2 flex items-center gap-2 text-slate-400 dark:text-slate-600">
             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
             </svg>
             <span class="text-xs font-medium italic">
                Miembro desde el {{ new Date(user.created_at).toLocaleDateString('es-AR', { year: 'numeric', month: 'long', day: 'numeric' }) }}
             </span>
          </div>
        </div>

        <div class="flex justify-end mt-6">
          <Button @click="emit('close')" class="bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white px-8 rounded-xl font-bold">
            Cerrar Detalle
          </Button>
        </div>
      </div>
    </div>
  </Modal>
</template>