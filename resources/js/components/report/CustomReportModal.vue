<script setup lang="ts">
import ReportShell from '@/components/report/ReportShell.vue';
import { exportCsv, exportExcel, memberColumns, type ReportMember } from '@/lib/exportData';
import { computed, ref } from 'vue';

const props = defineProps<{
    show: boolean;
    members: ReportMember[];
    statuses: { id: number; name: string }[];
    provinces: { id: number; name: string }[];
}>();
const emit = defineEmits<{ close: [] }>();

const filters = ref({
    dateFrom: '',
    dateTo: '',
    statusIds: [] as number[],
    provinceIds: [] as number[],
    columns: ['enrollment', 'name', 'email', 'status'] as string[],
});

// Si no se marca ningún estado/provincia, no se filtra por ese criterio
const filtered = computed(() =>
    props.members.filter((m) => {
        const f = filters.value;
        if (f.dateFrom && m.registration_date < f.dateFrom) return false;
        if (f.dateTo && m.registration_date > f.dateTo) return false;
        if (f.statusIds.length && !f.statusIds.includes(m.account_status_id)) return false;
        if (f.provinceIds.length && !f.provinceIds.includes(m.province_id)) return false;
        return true;
    }),
);

const selectedColumns = computed(() =>
    Object.values(memberColumns).filter((c) => filters.value.columns.includes(c.key)),
);

const canExport = computed(() => filtered.value.length > 0 && selectedColumns.value.length > 0);

const reset = () => {
    filters.value = { dateFrom: '', dateTo: '', statusIds: [], provinceIds: [], columns: ['enrollment', 'name', 'email', 'status'] };
};
</script>

<template>
    <ReportShell :show="show" title="Reporte Personalizado" subtitle="Elegí filtros y columnas, y exportá el resultado" max-width="3xl" @close="emit('close')">
        <div class="space-y-5">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Alta desde</label>
                    <input v-model="filters.dateFrom" type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Alta hasta</label>
                    <input v-model="filters.dateTo" type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                </div>
            </div>

            <div>
                <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Estado de cuenta</p>
                <div class="flex flex-wrap gap-3">
                    <label v-for="s in statuses" :key="s.id" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input v-model="filters.statusIds" type="checkbox" :value="s.id" class="rounded" /> {{ s.name }}
                    </label>
                </div>
            </div>

            <div>
                <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Provincia</p>
                <div class="grid max-h-32 grid-cols-2 gap-2 overflow-y-auto sm:grid-cols-3">
                    <label v-for="p in provinces" :key="p.id" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input v-model="filters.provinceIds" type="checkbox" :value="p.id" class="rounded" /> {{ p.name }}
                    </label>
                </div>
            </div>

            <div>
                <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Columnas a incluir</p>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                    <label v-for="c in memberColumns" :key="c.key" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input v-model="filters.columns" type="checkbox" :value="c.key" class="rounded" /> {{ c.label }}
                    </label>
                </div>
            </div>

            <p class="rounded-lg bg-blue-50 px-4 py-3 text-sm text-blue-800 dark:bg-blue-900/20 dark:text-blue-200">
                El reporte incluirá <strong>{{ filtered.length }}</strong> de {{ members.length }} matriculados.
            </p>
        </div>

        <template #actions>
            <button class="rounded-lg px-4 py-2 text-gray-600 hover:underline dark:text-gray-300" @click="reset">Limpiar</button>
            <button :disabled="!canExport" class="rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700 disabled:opacity-50" @click="exportExcel(filtered, selectedColumns, 'reporte-personalizado')">
                Exportar Excel
            </button>
            <button :disabled="!canExport" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50" @click="exportCsv(filtered, selectedColumns, 'reporte-personalizado')">
                Exportar CSV
            </button>
        </template>
    </ReportShell>
</template>
