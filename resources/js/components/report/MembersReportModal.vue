<script setup lang="ts">
import ReportShell from '@/components/report/ReportShell.vue';
import { exportCsv, memberColumns, type ReportMember } from '@/lib/exportData';
import { computed } from 'vue';

const props = defineProps<{ show: boolean; members: ReportMember[] }>();
const emit = defineEmits<{ close: [] }>();

const byStatus = computed(() => {
    const map = new Map<string, number>();
    props.members.forEach((m) => map.set(m.status, (map.get(m.status) ?? 0) + 1));
    return [...map.entries()];
});

const columns = ['enrollment', 'name', 'email', 'city', 'province', 'status'].map((k) => memberColumns[k]);
</script>

<template>
    <ReportShell :show="show" title="Reporte de Matriculados" subtitle="Listado completo con su estado de cuenta" @close="emit('close')">
        <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                <p class="text-sm text-blue-600 dark:text-blue-400">Total</p>
                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ members.length }}</p>
            </div>
            <div v-for="[status, count] in byStatus" :key="status" class="rounded-lg bg-gray-50 p-4 dark:bg-gray-900">
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ status }}</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ count }}</p>
            </div>
        </div>

        <div class="max-h-96 overflow-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="sticky top-0 bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th v-for="c in columns" :key="c.key" class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                            {{ c.label }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    <tr v-for="m in members" :key="m.id">
                        <td v-for="c in columns" :key="c.key" class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ c.value(m) }}</td>
                    </tr>
                    <tr v-if="!members.length">
                        <td :colspan="columns.length" class="px-4 py-8 text-center text-gray-500">Todavía no hay matriculados.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <template #actions>
            <button
                class="rounded-lg bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700"
                @click="exportCsv(members, columns, 'reporte-matriculados')"
            >
                Exportar CSV
            </button>
        </template>
    </ReportShell>
</template>
