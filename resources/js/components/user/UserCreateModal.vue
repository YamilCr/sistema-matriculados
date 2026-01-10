<script setup lang="ts">
import Modal from '@/components/Modal.vue'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3' // Cambiado a useForm para consistencia con Profile
import users from '@/routes/users'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

interface Role { id: number; name: string }
interface Member { id: number; name: string; enrollment_number: string }

interface Props {
  show: boolean
  roles: Role[]
  members: Member[]
}

const props = defineProps<Props>()
const emit = defineEmits<{ close: [] }>()

// 1. Inicialización con useForm para manejar errores y estados automáticamente
const form = useForm({
  name: '',
  email: '',
  role_id: 2,
  member_id: null as number | null,
  is_active: true,
  password: '',
  password_confirmation: '',
  image: null as File | null,
})

const imagePreview = ref<string | null>(null)

const handleImageChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  
  if (file) {
    form.image = file
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target?.result as string
    }
    reader.readAsDataURL(file)
  }
}

const closeModal = () => {
  form.reset()
  imagePreview.value = null
  emit('close')
}

const handleSubmit = () => {
  form.post(users.store(), {
    preserveScroll: true,
    onSuccess: () => {
      closeModal()
      // Podrías usar una notificación tipo toast aquí en lugar de alert
    },
  })
}
</script>

<template>
  <Modal :show="show" max-width="3xl" @close="closeModal" position="center">
    <div class="p-8 dark:bg-slate-900 transition-colors duration-500">
      <div class="flex items-center justify-between mb-8 border-b dark:border-white/5 pb-4">
        <div>
          <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            Crear Nuevo Usuario
          </h2>
          <p class="text-sm text-slate-500 dark:text-slate-400">Registra una nueva cuenta en el sistema administrativo.</p>
        </div>
        <button @click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-8">
        <div class="flex flex-col items-center justify-center space-y-4 py-4 bg-slate-50 dark:bg-white/5 rounded-3xl border-2 border-dashed border-slate-200 dark:border-white/10">
          <div class="relative group">
            <div class="h-28 w-28 rounded-full border-4 border-white dark:border-slate-800 shadow-xl overflow-hidden bg-slate-200 dark:bg-slate-700 flex items-center justify-center">
              <img v-if="imagePreview" :src="imagePreview" class="h-full w-full object-cover" />
              <div v-else class="text-slate-400 dark:text-slate-500 flex flex-col items-center">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
            </div>
            <label class="absolute bottom-0 right-0 bg-emerald-600 hover:bg-emerald-500 text-white rounded-full p-2.5 cursor-pointer shadow-lg transition-all hover:scale-110">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
              </svg>
              <input type="file" accept="image/*" @change="handleImageChange" class="hidden" />
            </label>
          </div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Foto de Perfil</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-2">
            <Label for="name">Nombre Completo</Label>
            <Input id="name" v-model="form.name" placeholder="Ej: Juan Pérez" class="dark:bg-slate-800" />
            <InputError :message="form.errors.name" />
          </div>

          <div class="space-y-2">
            <Label for="email">Email</Label>
            <Input id="email" type="email" v-model="form.email" placeholder="juan@ejemplo.com" class="dark:bg-slate-800" />
            <InputError :message="form.errors.email" />
          </div>

          <div class="space-y-2">
            <Label>Rol de Usuario</Label>
            <select v-model="form.role_id" class="flex h-10 w-full rounded-md border border-input bg-white dark:bg-slate-800 dark:border-white/10 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
              <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
            </select>
          </div>

          <div class="space-y-2">
            <Label>Asociar Matriculado (Opcional)</Label>
            <select v-model="form.member_id" class="flex h-10 w-full rounded-md border border-input bg-white dark:bg-slate-800 dark:border-white/10 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
              <option :value="null">Sin asociar</option>
              <option v-for="member in members" :key="member.id" :value="member.id">
                {{ member.enrollment_number }} - {{ member.name }}
              </option>
            </select>
          </div>

          <div class="space-y-2">
            <Label for="password">Contraseña</Label>
            <Input id="password" type="password" v-model="form.password" class="dark:bg-slate-800" />
            <InputError :message="form.errors.password" />
          </div>

          <div class="space-y-2">
            <Label for="password_confirmation">Confirmar Contraseña</Label>
            <Input id="password_confirmation" type="password" v-model="form.password_confirmation" class="dark:bg-slate-800" />
          </div>
        </div>

        <div class="flex items-center space-x-3 p-4 bg-emerald-50 dark:bg-emerald-500/5 rounded-2xl border border-emerald-100 dark:border-emerald-500/10 transition-colors">
          <input
            v-model="form.is_active"
            type="checkbox"
            id="is_active_create"
            class="h-5 w-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:bg-slate-800 dark:border-white/10"
          />
          <label for="is_active_create" class="text-sm font-bold text-emerald-900 dark:text-emerald-400">
            Habilitar acceso al sistema inmediatamente
          </label>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t dark:border-white/5">
          <Button type="button" variant="outline" @click="closeModal" class="rounded-xl px-6">
            Cancelar
          </Button>
          <Button 
            type="submit" 
            :disabled="form.processing" 
            class="bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 text-white font-black px-8 rounded-xl shadow-lg shadow-emerald-600/20"
          >
            {{ form.processing ? 'Creando...' : 'Crear Usuario' }}
          </Button>
        </div>
      </form>
    </div>
  </Modal>
</template>