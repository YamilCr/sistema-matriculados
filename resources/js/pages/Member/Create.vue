<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue'; 
import { route } from 'ziggy-js';

// Props: Recibimos las listas para los selectores
const props = defineProps<{
    show: boolean;
    provinces: Array<{ id: number; name: string }>;
    cities: Array<{ id: number; name: string; province_id: number }>;
    // accountStatuses? Si los pasas desde el controlador, agrégalos aquí
}>();

const emit = defineEmits(['close']);

// Previsualización de foto
const photoPreview = ref<string | null>(null);

// Formulario limpio
const form = useForm({
    registration_number: '', // Campo obligatorio único
    first_name: '',
    last_name: '',
    dni: '',
    phone: '',
    email: '', // <--- NUEVO CAMPO IMPORTANTE
    address: '',
    city_id: null as number | null,
    province_id: null as number | null,
    account_status_id: 1, // Por defecto "Activo" (ID 1)
    image: null as File | null,
    is_active: true
});

// Lógica de filtrado de ciudades
const filteredCities = computed(() => {
    if (!form.province_id || !props.cities) return [];
    return props.cities.filter(city => Number(city.province_id) === Number(form.province_id));
});

// Manejo de imagen
const updatePhotoPreview = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => { photoPreview.value = e.target?.result as string; };
        reader.readAsDataURL(file);
    }
};

const close = () => {
    if (!form.processing) {
        form.reset();
        photoPreview.value = null;
        form.clearErrors();
        emit('close');
    }
};

const submit = () => {
    // Para crear usamos POST directo a 'members.store'
    form.post(route('members.store'), {
        forceFormData: true, 
        preserveScroll: true,
        onSuccess: () => {
            close();
        },
        onError: (errors) => {
            console.error('Errores al crear:', errors);
        },
    });
};

// Estados hardcodeados (o pásalos por props si prefieres)
const statuses = [
    { id: 1, name: 'Activo' },
    { id: 2, name: 'Inactivo' },
    { id: 3, name: 'Suspendido' },
];
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            
            <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="close"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative z-10 inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-200 dark:border-gray-700">
                
                <div class="bg-blue-600 px-6 py-4 border-b border-blue-500 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        Nuevo Matriculado
                    </h3>
                     <button @click="close" class="text-blue-100 hover:text-white focus:outline-none transition-colors">
                        <span class="text-2xl">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="submit" class="px-6 py-6">
                    
                    <div class="flex flex-col sm:flex-row items-center gap-6 mb-8 pb-6 border-b border-gray-100 dark:border-gray-700">
                        <div class="relative group">
                            <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-white dark:border-slate-700 shadow-md bg-gray-100 dark:bg-slate-900">
                                <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                            </div>
                        </div>

                        <div class="flex-1 w-full text-center sm:text-left">
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">Foto de Perfil</label>
                            <input 
                                type="file" 
                                accept="image/*"
                                @change="updatePhotoPreview"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-700 dark:file:text-blue-400 cursor-pointer"
                            />
                            <p v-if="form.errors.image" class="text-red-500 text-xs mt-2">{{ form.errors.image }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">N° Matrícula <span class="text-red-500">*</span></label>
                            <input type="text" v-model="form.registration_number" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white uppercase" placeholder="Ej: MAT-2024-001">
                            <p v-if="form.errors.registration_number" class="text-red-500 text-xs mt-1">{{ form.errors.registration_number }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Email (Usuario) <span class="text-red-500">*</span></label>
                            <input type="email" v-model="form.email" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="correo@ejemplo.com">
                            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nombre</label>
                            <input type="text" v-model="form.first_name" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <p v-if="form.errors.first_name" class="text-red-500 text-xs mt-1">{{ form.errors.first_name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Apellido</label>
                            <input type="text" v-model="form.last_name" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <p v-if="form.errors.last_name" class="text-red-500 text-xs mt-1">{{ form.errors.last_name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">DNI</label>
                            <input type="text" v-model="form.dni" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <p v-if="form.errors.dni" class="text-red-500 text-xs mt-1">{{ form.errors.dni }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Teléfono</label>
                            <input type="text" v-model="form.phone" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <p v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</p>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Dirección Postal</label>
                            <input type="text" v-model="form.address" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <p v-if="form.errors.address" class="text-red-500 text-xs mt-1">{{ form.errors.address }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Provincia</label>
                            <select 
                                v-model="form.province_id"
                                @change="form.city_id = null" 
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                            >
                                <option :value="null" disabled>Seleccione...</option>
                                <option v-for="p in provinces" :key="p.id" :value="p.id">{{ p.name }}</option>
                            </select>
                            <p v-if="form.errors.province_id" class="text-red-500 text-xs mt-1">{{ form.errors.province_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Ciudad</label>
                            <select 
                                v-model="form.city_id"
                                :disabled="!filteredCities.length"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white disabled:opacity-50"
                            >
                                <option :value="null" disabled>
                                    {{ form.province_id ? 'Seleccione ciudad' : 'Elija provincia' }}
                                </option>
                                <option v-for="c in filteredCities" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                            <p v-if="form.errors.city_id" class="text-red-500 text-xs mt-1">{{ form.errors.city_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Estado</label>
                            <select v-model="form.account_status_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                <option v-for="status in statuses" :key="status.id" :value="status.id">{{ status.name }}</option>
                            </select>
                            <p v-if="form.errors.account_status_id" class="text-red-500 text-xs mt-1">{{ form.errors.account_status_id }}</p>
                        </div>

                    </div>

                    <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" @click="close" :disabled="form.processing" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 dark:bg-slate-800 dark:text-white dark:border-slate-600 dark:hover:bg-slate-700 transition-all">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all disabled:opacity-50 flex items-center">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            {{ form.processing ? 'Creando...' : 'Crear Matriculado' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>