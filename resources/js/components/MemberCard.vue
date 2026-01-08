<script setup lang="ts">
import { ref } from 'vue';
import { exportToPdf } from '../PdfExport';

const props = defineProps<{
  userName: string;
  userImage?: string;
  member: {
    registration_number?: string;
    image?: string;
    account_status_id: number;
    account_status: { name: string };
    province: { name: string };
  }
}>()

const carnetRef = ref<HTMLElement | null>(null);

const descargarCarnet = () => {
    const nombreArchivo = `Carnet_${props.userName.replace(/\s+/g, '_')}.pdf`;
    exportToPdf(carnetRef.value, nombreArchivo);
};
</script>

<template>
    <div class="w-full max-w-xl group transition-all duration-500">
        
        <div class="mb-10 text-center">
            <h1 class="text-2xl font-black text-white md:text-gray-900 dark:md:text-white mb-1">
                Mi Identidad Digital
            </h1>
            <p class="text-green-100 md:text-gray-500 dark:md:text-emerald-400/80 font-medium tracking-wide">
                Panel de Gestión Profesional
            </p>
        </div>

        <div ref="carnetRef" class="printable-card-container relative bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl shadow-green-900/10 dark:shadow-black/50 border border-gray-100 dark:border-white/5 overflow-hidden transition-colors duration-500">
            
            <div class="h-3 w-full bg-gradient-to-r from-green-400 via-emerald-500 to-teal-600 dark:from-emerald-600 dark:to-teal-800"></div>

            <div class="p-8 md:p-10">
                <div class="flex flex-col md:flex-row gap-8 items-center md:items-start">
                    
                    <div class="relative">
                        <div :class="[
                            'p-1.5 rounded-full ring-4 transition-all duration-700',
                            member.account_status_id === 1 
                                ? 'ring-green-500/20 dark:ring-emerald-500/10' 
                                : 'ring-red-500/20 dark:ring-red-500/10'
                        ]">
                            <div class="w-32 h-32 rounded-full border-4 border-white dark:border-slate-800 shadow-xl overflow-hidden bg-gray-50 dark:bg-slate-800 flex items-center justify-center">
                                <img v-if="member.image" :src="'/storage/' + member.image" class="w-full h-full object-cover" />
                                <img v-else-if="userImage" :src="'/storage/' + userImage" class="w-full h-full object-cover" />
                                <div v-else class="text-emerald-700 dark:text-emerald-500 font-black text-4xl italic">
                                    {{ userName.charAt(0) }}
                                </div>
                            </div>
                        </div>
                        
                        <div :class="[
                            'absolute -bottom-2 left-1/2 -translate-x-1/2 w-auto px-3 py-1.5 rounded-full text-[9px] leading-none font-black uppercase tracking-tighter shadow-sm border border-white dark:border-slate-800 flex items-center justify-center text-center whitespace-nowrap',
                            member.account_status_id === 1 ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                        ]">
                            {{ member.account_status.name }}
                        </div>
                    </div>

                    <div class="flex-1 text-center md:text-left space-y-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">
                                {{ userName }}
                            </h2>
                            <p class="text-emerald-600 dark:text-emerald-400 font-semibold text-sm">
                                Matriculado Profesional
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 pt-4 border-t border-gray-100 dark:border-white/5">
                            
                            <div class="bg-gray-50 dark:bg-slate-700/60 rounded-2xl p-4 border border-gray-200 dark:border-slate-600 group-hover:bg-emerald-50 dark:group-hover:bg-slate-700 transition-colors">
                                <span class="block text-[10px] uppercase font-bold text-gray-500 dark:text-slate-400 mb-1 tracking-widest">
                                    N° Matrícula Oficial
                                </span>
                                <span class="text-2xl font-mono font-black text-gray-800 dark:text-white tracking-tighter">
                                    {{ member.registration_number || 'PENDIENTE' }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3 px-2">
                                <div class="h-8 w-8 rounded-lg bg-gray-100 dark:bg-white/5 flex items-center justify-center text-gray-500 dark:text-emerald-500">
                                    📍
                                </div>
                                <div>
                                    <span class="block text-[10px] uppercase font-bold text-gray-400 dark:text-slate-500">Jurisdicción</span>
                                    <span class="text-sm font-bold text-gray-700 dark:text-slate-300">{{ member.province.name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex flex-wrap gap-3 justify-center md:justify-end hide-on-print">
                    <button 
                        @click="descargarCarnet" 
                        class="px-5 py-2.5 bg-gray-900 dark:bg-emerald-600 text-white dark:text-white rounded-xl text-sm font-bold hover:bg-gray-800 dark:hover:bg-emerald-500 transition-all shadow-lg shadow-black/5 dark:shadow-emerald-900/20 flex items-center gap-2"
                    >
                        <span>Descargar Carnet</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* Estilos para ocultar elementos al imprimir */
</style>