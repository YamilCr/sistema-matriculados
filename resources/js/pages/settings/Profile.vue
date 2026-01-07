<script setup lang="ts">
import { Head, usePage, useForm, router } from '@inertiajs/vue3'; // Usamos useForm de Inertia directamente
import { ref, computed } from 'vue';
import { edit, update } from '@/routes/profile';
import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import type { BreadcrumbItem } from '@/types';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
    provinces: Array<{ id: number; name: string }>;
    cities: Array<{ id: number; name: string; province_id: number }>;
    member: any | null;
}

const props = defineProps<Props>();
const page = usePage();
const user = page.props.auth.user as any;

// 1. Inicialización estándar del Formulario de Inertia
// Esto asegura que la página cargue y que los datos sean reactivos
const form = useForm({
    name: user.name,
    email: user.email,
    image: null as File | null,
    phone: props.member?.phone || '',
    address: props.member?.address || '',
    province_id: props.member?.province_id,
    city_id: props.member?.city_id,
});

// 2. Lógica de filtrado de ciudades
const filteredCities = computed(() => {
    if (!form.province_id) return [];
    return props.cities.filter(city => Number(city.province_id) === Number(form.province_id));
});

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Ajustes de Perfil', href: edit().url },
];

// Previsualización de imagen
const photoPreview = ref<string | null>(null);
const updatePhotoPreview = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => { photoPreview.value = e.target?.result as string; };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    // IMPORTANTE: Cuando envías imágenes con PATCH en Laravel, 
    // debes usar POST y agregar '_method: patch'
    form.transform((data) => ({
        ...data,
        _method: 'patch',
    })).post(update().url, {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
        },
        onError: (errors) => {
            console.error('Errores:', errors);
        }
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Ajustes de Perfil" />

        <SettingsLayout>
            <div class="flex flex-col space-y-8 animate-in fade-in duration-500 text-slate-900 dark:text-slate-100">
                <HeadingSmall
                    title="Información del Perfil"
                    description="Actualiza tu información personal y profesional para tu credencial digital."
                />

                <form @submit.prevent="submit" class="space-y-8">
                    <div class="flex flex-col md:flex-row md:items-center gap-6 border-b border-slate-100 dark:border-white/5 pb-8">
                        <div class="h-24 w-24 rounded-full border-4 border-white dark:border-slate-800 shadow-xl overflow-hidden bg-slate-100 dark:bg-slate-800 shrink-0">
                            <img v-if="photoPreview" :src="photoPreview" class="h-full w-full object-cover" />
                            <img v-else-if="user.image" :src="'/storage/' + user.image" class="h-full w-full object-cover" />

                            <div v-else class="h-full w-full flex items-center justify-center text-3xl font-black text-emerald-600 uppercase">
                                {{ user.name.charAt(0) }}
                            </div>
                            
                        </div>
                        <div class="space-y-2 flex-1">
                            <Label for="image" class="font-bold">Foto de Perfil</Label>
                            <Input id="image" type="file" class="max-w-xs dark:bg-slate-900 dark:border-white/10" accept="image/*" @change="updatePhotoPreview" />
                            <InputError :message="form.errors.image" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <Label for="name">Nombre Completo</Label>
                            <Input id="name" v-model="form.name" class="dark:bg-slate-900 dark:border-white/10" />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="space-y-2">
                            <Label for="email">Correo Electrónico</Label>
                            <Input id="email" type="email" v-model="form.email" class="dark:bg-slate-900 dark:border-white/10" />
                            <InputError :message="form.errors.email" />
                        </div>
                    </div>

                    <div v-if="props.member" class="pt-6 border-t border-slate-100 dark:border-white/5 space-y-6">
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <span class="h-1 w-4 bg-emerald-500 rounded-full"></span>
                            Datos de Colegiación
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="phone">Teléfono (WhatsApp)</Label>
                                <Input id="phone" v-model="form.phone" class="dark:bg-slate-900 dark:border-white/10" />
                                <InputError :message="form.errors.phone" />
                            </div>
                            <div class="space-y-2">
                                <Label for="address">Domicilio Profesional</Label>
                                <Input id="address" v-model="form.address" class="dark:bg-slate-900 dark:border-white/10" />
                                <InputError :message="form.errors.address" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label>Provincia</Label>
                                <select 
                                    v-model="form.province_id"
                                    @change="form.city_id = undefined"
                                    class="flex h-10 w-full rounded-md border border-input bg-white dark:bg-slate-900 dark:border-white/10 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all"
                                >
                                    <option :value="undefined" disabled>Seleccione provincia</option>
                                    <option v-for="p in provinces" :key="p.id" :value="p.id">{{ p.name }}</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <Label>Ciudad</Label>
                                <select 
                                    v-model="form.city_id"
                                    :disabled="!filteredCities.length"
                                    class="flex h-10 w-full rounded-md border border-input bg-white dark:bg-slate-900 dark:border-white/10 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none disabled:opacity-50 transition-all"
                                >
                                    <option :value="undefined" disabled>
                                        {{ form.province_id ? 'Seleccione ciudad' : 'Elija provincia primero' }}
                                    </option>
                                    <option v-for="c in filteredCities" :key="c.id" :value="c.id">{{ c.name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button type="submit" :disabled="form.processing" class="bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 text-white font-bold">
                            {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                        </Button>
                        <p v-if="form.recentlySuccessful" class="text-sm text-emerald-600 font-bold animate-pulse">
                            ¡Guardado exitosamente!
                        </p>
                    </div>
                </form>

                <DeleteUser />
            </div>
        </SettingsLayout>
    </AppLayout>
</template>