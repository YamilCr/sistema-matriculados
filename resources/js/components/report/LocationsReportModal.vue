<script setup lang="ts">
import ReportShell from '@/components/report/ReportShell.vue';
import { exportCsv, type ExportColumn, type ReportMember } from '@/lib/exportData';
import { computed } from 'vue';

const props = defineProps<{
    show: boolean;
    members: ReportMember[];
    statuses: { id: number; name: string }[];
}>();
const emit = defineEmits<{ close: [] }>();

interface Row {
    province: string;
    city: string;
    total: number;
    byStatus: Record<string, number>;
}

// Agrupa por provincia + ciudad y cuenta cada estado
const rows = computed<Row[]>(() => {
    const map = new Map<string, Row>();
    props.members.forEach((m) => {
        const key = `${m.province}|${m.city}`;
        const row = map.get(key) ?? { province: m.province, city: m.city, total: 0, byStatus: {} };
        row.total++;
        row.byStatus[m.status] = (row.byStatus[m.status] ?? 0) + 1;
        map.set(key, row);
    });
    return [...map.values()].sort((a, b) => b.total - a.total);
});

const percent = (n: number) => (props.members.length ? ((n / props.members.length) * 100).toFixed(1) : '0.0');

const columns = computed<ExportColumn<Row>[]>(() => [
    { key: 'province', label: 'Provincia', value: (r) => r.province },
    { key: 'city', label: 'Ciudad', value: (r) => r.city },
    { key: 'total', label: 'Total', value: (r) => r.total },
    ...props.statuses.map((s) => ({ key: s.name, label: s.name, value: (r: Row) => r.byStatus[s.name] ?? 0 })),
    { key: 'pct', label: '% del total', value: (r) => percent(r.total) + '%' },
]);
</script>

<template>
    <ReportShell :show="show" title="Reporte por Ubicación" subtitle="Distribución de matriculados por provincia y ciudad" @close="emit('close')">
        <div class="max-h-96 overflow-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="sticky top-0 bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Ubicación</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Total</th>
                        <th v-for="s in statuses" :key="s.id" class="px-4 py-3 text-center text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                            {{ s.name }}
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase text-gray-500 dark:text-gray-400">% del total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    <tr v-for="r in rows" :key="r.province + r.city">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ r.city }}, {{ r.province }}</td>
                        <td class="px-4 py-3 text-center text-sm font-semibold text-gray-900 dark:text-white">{{ r.total }}</td>
                        <td v-for="s in statuses" :key="s.id" class="px-4 py-3 text-center text-sm text-gray-600 dark:text-gray-300">
                            {{ r.byStatus[s.name] ?? 0 }}
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-white">{{ percent(r.total) }}%</td>
                    </tr>
                    <tr v-if="!rows.length">
                        <td :colspan="statuses.length + 3" class="px-4 py-8 text-center text-gray-500">Sin datos para mostrar.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <template #actions>
            <button class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" @click="exportCsv(rows, columns, 'reporte-ubicacion')">
                Exportar CSV
            </button>
        </template>
    </ReportShell>
</template>
