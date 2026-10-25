<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import statusRoutes from '@/routes/accountstatuses';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

defineProps<{
    statuses: { id: number; name: string; description: string | null; members_count: number }[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Inicio', href: dashboard().url },
    { title: 'Estados de cuenta', href: statusRoutes.index().url },
];

const badge: Record<string, string> = {
    'Al día': 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
    Moroso: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
    Suspendido: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
};
</script>

<template>
    <Head title="Estados de cuenta" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-4xl p-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Estados de cuenta</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Son valores fijos del sistema. Al cargar o editar un matriculado se elige uno de esta lista, sin escribirlo.
            </p>

            <div class="mt-6 overflow-hidden rounded-lg border bg-white shadow dark:border-slate-700 dark:bg-slate-800">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-slate-700 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Descripción</th>
                            <th class="px-4 py-3 text-center">Matriculados</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-slate-700">
                        <tr v-for="s in statuses" :key="s.id" class="dark:text-gray-200">
                            <td class="px-4 py-3">
                                <span :class="['rounded-full px-3 py-1 text-xs font-semibold', badge[s.name] ?? 'bg-gray-100 text-gray-700']">{{ s.name }}</span>
                            </td>
                            <td class="px-4 py-3">{{ s.description }}</td>
                            <td class="px-4 py-3 text-center">{{ s.members_count }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
