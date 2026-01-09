<script setup lang="ts">
import { computed } from 'vue';

// Definimos las props
const props = defineProps<{
    show: boolean;
    member: any;
}>();

// Evento para cerrar
const emit = defineEmits(['close']);

const close = () => {
    emit('close');
};

// Lógica de colores según el estado
const statusColorClasses = computed(() => {
    const status = props.member?.status?.toLowerCase() || '';
    
    if (['active', 'activo', 'al día'].includes(status)) {
        return 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/40 dark:text-green-300 dark:border-green-800';
    }
    if (['suspended', 'suspendido', 'deudor'].includes(status)) {
        return 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/40 dark:text-red-300 dark:border-red-800';
    }
    return 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600';
});
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            
            <div 
                class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" 
                aria-hidden="true"
                @click="close"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative z-10 inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-gray-200 dark:border-gray-700">
                
                <div class="bg-gray-50 dark:bg-slate-900/50 px-6 py-4 flex justify-between items-center border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span>📋</span> Ficha Técnica del Matriculado
                    </h3>
                    <button @click="close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div v-if="member" class="px-6 py-8">
                    <div class="flex flex-col md:flex-row gap-8">
                        
                        <div class="flex flex-col items-center md:w-1/3 border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-700 pb-6 md:pb-0 md:pr-6">
                            
                            <div class="w-40 h-40 rounded-full p-1 border-2 border-dashed border-gray-300 dark:border-gray-600 mb-4">
                                <div class="w-full h-full rounded-full overflow-hidden bg-gray-100 dark:bg-slate-700 shadow-inner">
                                    <img 
                                        v-if="member.image" 
                                        :src="member.image.startsWith('http') ? member.image : '/storage/' + member.image" 
                                        class="w-full h-full object-cover"
                                        alt="Foto de perfil"
                                    >
                                    <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-5xl font-bold">
                                        {{ member.name.charAt(0) }}
                                    </div>
                                </div>
                            </div>

                            <span :class="['px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest border', statusColorClasses]">
                                {{ member.status }}
                            </span>

                            <div class="mt-6 w-full text-center">
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest mb-1">N° Matrícula</p>
                                <p class="text-2xl font-mono font-black text-gray-800 dark:text-white">
                                    {{ member.enrollment_number }}
                                </p>
                            </div>
                        </div>

                        <div class="flex-1">
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                                {{ member.name }}
                            </h2>
                            <p class="text-blue-600 dark:text-blue-400 font-medium mb-6 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                {{ member.email }}
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-4">
                                
                                <div class="group">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">DNI</label>
                                    <div class="text-gray-900 dark:text-gray-200 font-medium border-b border-gray-100 dark:border-gray-700 pb-1">
                                        {{ member.dni || 'No registrado' }}
                                    </div>
                                </div>

                                <div class="group">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Teléfono</label>
                                    <div class="text-gray-900 dark:text-gray-200 font-medium border-b border-gray-100 dark:border-gray-700 pb-1">
                                        {{ member.phone || 'No registrado' }}
                                    </div>
                                </div>

                                <div class="sm:col-span-2 group">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Dirección</label>
                                    <div class="text-gray-900 dark:text-gray-200 font-medium border-b border-gray-100 dark:border-gray-700 pb-1 flex items-start gap-2">
                                        <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        {{ member.address }}
                                    </div>
                                </div>

                                <div class="group">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Localidad</label>
                                    <div class="text-gray-900 dark:text-gray-200 font-medium">
                                        {{ member.location }}
                                    </div>
                                </div>

                                <div class="group">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Provincia</label>
                                    <div class="text-gray-900 dark:text-gray-200 font-medium">
                                        {{ member.province }}
                                    </div>
                                </div>

                            </div>

                            <div class="mt-8 pt-4 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-500 text-right">
                                Fecha de alta: {{ member.registration_date }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-slate-900/50 px-6 py-4 flex flex-row-reverse gap-3">
                    <button 
                        @click="close"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-slate-800 dark:text-white dark:border-slate-600 dark:hover:bg-slate-700 transition-all focus:outline-none"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>