<script setup lang="ts">
import MasterCrud from '@/components/master/MasterCrud.vue';
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
</script>

<template>
    <Head title="Estados de cuenta" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MasterCrud
            title="Estados de cuenta"
            subtitle="Definí los estados en los que puede estar un matriculado"
            singular="Estado"
            :rows="statuses"
            :columns="[
                { key: 'name', label: 'Estado' },
                { key: 'description', label: 'Descripción' },
                { key: 'members_count', label: 'Matriculados', align: 'center' },
            ]"
            :fields="[
                { key: 'name', label: 'Nombre', placeholder: 'Ej: Al día' },
                { key: 'description', label: 'Descripción', placeholder: 'Ej: Sin deuda' },
            ]"
            :store-url="statusRoutes.store().url"
            :update-url="(id) => statusRoutes.update(id).url"
            :destroy-url="(id) => statusRoutes.destroy(id).url"
        />
    </AppLayout>
</template>
