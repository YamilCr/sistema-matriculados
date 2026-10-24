<script setup lang="ts">
import MasterCrud from '@/components/master/MasterCrud.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import citiesRoutes from '@/routes/cities';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    cities: { id: number; name: string; province_id: number; members_count: number; province?: { name: string } }[];
    provinces: { id: number; name: string }[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Inicio', href: dashboard().url },
    { title: 'Ciudades', href: citiesRoutes.index().url },
];

const provinceOptions = computed(() => props.provinces.map((p) => ({ value: p.id, label: p.name })));
</script>

<template>
    <Head title="Ciudades" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MasterCrud
            title="Ciudades"
            subtitle="Administrá las ciudades de cada provincia"
            singular="Ciudad"
            :rows="cities"
            :columns="[
                { key: 'name', label: 'Ciudad' },
                { key: 'province.name', label: 'Provincia' },
                { key: 'members_count', label: 'Matriculados', align: 'center' },
            ]"
            :fields="[
                { key: 'province_id', label: 'Provincia', type: 'select', options: provinceOptions },
                { key: 'name', label: 'Nombre', placeholder: 'Ej: Trelew' },
            ]"
            :store-url="citiesRoutes.store().url"
            :update-url="(id) => citiesRoutes.update(id).url"
            :destroy-url="(id) => citiesRoutes.destroy(id).url"
        />
    </AppLayout>
</template>
