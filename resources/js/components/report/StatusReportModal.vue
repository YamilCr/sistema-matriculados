<script setup lang="ts">
import ReportShell from '@/components/report/ReportShell.vue';
import { exportCsv, memberColumns, type ReportMember } from '@/lib/exportData';
import { computed } from 'vue';

const props = defineProps<{
    show: boolean;
    members: ReportMember[];
    statuses: { id: number; name: string }[];
}>();
const emit = defineEmits<{ close: [] }>();

const total = computed(() => props.members.length);

const summary = computed(() =>
    props.statuses.map((s) => {
        const count = props.members.filter((m) => m.account_status_id === s.id).length;
        return { ...s, count, pct: total.value ? (count / total.value) * 100 : 0 };
    }),
);

// Los más recientes primero, para ver las últimas altas y su estado
const recent = computed(() => props.members.slice(0, 8));

const barColor = (name: string) => {
    const k = name.toLowerCase();
    if (k.includes('moroso')) return 'bg-amber-500';
    if (k.includes('suspendido')) return 'bg-red-500';
    if (k.includes('día') || k.includes('dia')) return 'bg-green-500';
    return 'bg-gray-400';
};

const columns = ['enrollment', 'name', 'status', 'registration_date'].map((k) => memberColumns[k]);
</script>

<template>
    <ReportShell :show="show" title="Reporte de Estados" subtitle="Cuántos matriculados hay en cada estado de cuenta" @close="emit('close')">
        <div class="mb-6 space-y-4">
            <div v-for="s in summary" :key="s.id">
                <div class="mb-1 flex justify-between text-sm">
                    <span class="font-medium text-gray-900 dark:text-white">{{ s.name }}</span>
                    <span class="text-gray-600 dark:text-gray-400">{{ s.count }} ({{ s.pct.toFixed(1) }}%)</span>
                </div>
                <div class="h-3 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                    <div :class="[barColor(s.name), 'h-3 rounded-full transition-all']" :style="{ width: s.pct + '%' }"></div>
                </div>
            </div>
            <p v-if="!total" class="text-sm text-gray-500">Todavía no hay matriculados.</p>
        </div>

        <h3 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Últimas altas</h3>
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th v-for="c in columns" :key="c.key" class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">{{ c.label }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    <tr v-for="m in recent" :key="m.id">
                        <td v-for="c in columns" :key="c.key" class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ c.value(m) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <template #actions>
            <button class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" @click="exportCsv(summary, [
                { key: 'name', label: 'Estado', value: (r) => r.name },
                { key: 'count', label: 'Cantidad', value: (r) => r.count },
                { key: 'pct', label: 'Porcentaje', value: (r) => r.pct.toFixed(1) + '%' },
            ], 'reporte-estados')">
                Exportar CSV
            </button>
        </template>
    </ReportShell>
</template>
