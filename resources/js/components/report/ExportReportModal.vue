<script setup lang="ts">
import ReportShell from '@/components/report/ReportShell.vue';
import { exportCsv, exportExcel, memberColumns, type ReportMember } from '@/lib/exportData';
import { ref } from 'vue';

const props = defineProps<{ show: boolean; members: ReportMember[] }>();
const emit = defineEmits<{ close: [] }>();

const formats = [
    { id: 'excel', name: 'Excel (.xls)', icon: '📊', description: 'Ideal para análisis y edición de datos' },
    { id: 'csv', name: 'CSV (.csv)', icon: '📋', description: 'Compatible con cualquier aplicación' },
] as const;

const selected = ref<'excel' | 'csv'>('excel');

const exportData = () => {
    const columns = Object.values(memberColumns);
    if (selected.value === 'excel') exportExcel(props.members, columns, 'matriculados');
    else exportCsv(props.members, columns, 'matriculados');
    emit('close');
};
</script>

<template>
    <ReportShell :show="show" title="Exportar Datos" subtitle="Descargá el padrón completo de matriculados" max-width="2xl" @close="emit('close')">
        <div class="space-y-3">
            <label
                v-for="f in formats"
                :key="f.id"
                class="flex cursor-pointer items-center gap-4 rounded-lg border-2 p-4 transition-colors"
                :class="selected === f.id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700'"
            >
                <input v-model="selected" type="radio" :value="f.id" class="sr-only" />
                <span class="text-3xl">{{ f.icon }}</span>
                <span>
                    <span class="block font-semibold text-gray-900 dark:text-white">{{ f.name }}</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ f.description }}</span>
                </span>
            </label>
            <p class="text-sm text-gray-600 dark:text-gray-400">Se exportarán {{ members.length }} matriculados con todas sus columnas.</p>
        </div>

        <template #actions>
            <button :disabled="!members.length" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50" @click="exportData">
                Descargar
            </button>
        </template>
    </ReportShell>
</template>
