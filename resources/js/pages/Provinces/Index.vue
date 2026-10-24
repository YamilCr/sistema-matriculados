<script setup lang="ts">
import MasterCrud from '@/components/master/MasterCrud.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import provincesRoutes from '@/routes/provinces';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

defineProps<{ provinces: { id: number; name: string; cities_count: number; members_count: number }[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Inicio', href: dashboard().url },
    { title: 'Provincias', href: provincesRoutes.index().url },
];
</script>

<template>
    <Head title="Provincias" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MasterCrud
            title="Provincias"
            subtitle="Administrá las provincias disponibles para los matriculados"
            singular="Provincia"
            :rows="provinces"
            :columns="[
                { key: 'name', label: 'Provincia' },
                { key: 'cities_count', label: 'Ciudades', align: 'center' },
                { key: 'members_count', label: 'Matriculados', align: 'center' },
            ]"
            :fields="[{ key: 'name', label: 'Nombre', placeholder: 'Ej: Chubut' }]"
            :store-url="provincesRoutes.store().url"
            :update-url="(id) => provincesRoutes.update(id).url"
            :destroy-url="(id) => provincesRoutes.destroy(id).url"
        />
    </AppLayout>
</template>
