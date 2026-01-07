<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue'; // Añadimos computed

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';

// Definimos las interfaces para los datos que vienen del controlador
interface Props {
    mustVerifyEmail: boolean;
    status?: string;
    provinces: Array<{ id: number; name: string }>;
    cities: Array<{ id: number; name: string; province_id: number }>;
    member: {
        phone?: string;
        address?: string;
        province_id?: number;
        city_id?: number;
    } | null;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: edit().url,
    },
];

const page = usePage();
const user = page.props.auth.user as any;



// Lógica de filtrado dinámico de ciudades corregida
// Lógica de filtrado dinámico de ciudades corregida
const filteredCities = computed(() => {
    const form = ProfileController.update.form() as any;
    const selectedProvinceId = form.province_id;

    // Si no hay nada seleccionado, devolvemos lista vacía
    if (!selectedProvinceId) return [];

    // Forzamos que ambos sean Number para que la comparación sea exitosa
    return props.cities.filter(city => Number(city.province_id) === Number(selectedProvinceId));
});

// Previsualización de imagen
const photoPreview = ref<string | null>(null);

const updatePhotoPreview = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (!file) return;

    (ProfileController.update.form() as any).image = file;

    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    title="Profile information"
                    description="Actualiza tu información personal y profesional"
                />

                <Form
                    v-bind="(ProfileController.update.form() as any)"
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <div class="grid gap-4 border-b pb-6">
                        <Label for="image">Foto de Perfil (Personal)</Label>
                        <div class="flex items-center gap-4">
                            <div class="relative h-20 w-20 rounded-full border-2 border-dashed border-gray-300 overflow-hidden bg-gray-50 flex items-center justify-center">
                                <img v-if="photoPreview" :src="photoPreview" class="h-full w-full object-cover" />
                                <img v-else-if="user.image" :src="'/storage/' + user.image" class="h-full w-full object-cover" />
                                <span v-else class="text-2xl font-bold text-gray-400 uppercase">{{ user.name.charAt(0) }}</span>
                            </div>
                            <div class="flex-1">
                                <Input id="image" type="file" class="mt-1 block w-full text-sm" accept="image/*" @change="updatePhotoPreview" />
                            </div>
                        </div>
                        <InputError :message="errors.image" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="name">Nombre Completo</Label>
                            <Input id="name" v-model="(ProfileController.update.form() as any).name" :default-value="user.name" />
                            <InputError :message="errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="email">Email</Label>
                            <Input id="email" type="email" v-model="(ProfileController.update.form() as any).email" :default-value="user.email" />
                            <InputError :message="errors.email" />
                        </div>
                    </div>

                    <div v-if="props.member" class="grid gap-6 border-t pt-6">
                        <h3 class="text-sm font-semibold text-neutral-500 uppercase tracking-wider">Información Profesional</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="phone">Teléfono (WhatsApp)</Label>
                                <Input id="phone" v-model="(ProfileController.update.form() as any).phone" placeholder="+54 297 ..." />
                                <InputError :message="errors.phone" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="address">Dirección Particular</Label>
                                <Input id="address" v-model="(ProfileController.update.form() as any).address" placeholder="Calle y Nro" />
                                <InputError :message="errors.address" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="province">Provincia</Label>
                                <p class="text-xs text-red-500">
                                    Provincia seleccionada: {{ (ProfileController.update.form() as any).province_id }} 
                                    | Ciudades encontradas: {{ filteredCities.length }}
                                </p>
                                <select 
                                    v-model="(ProfileController.update.form() as any).province_id"
                                    @change="(ProfileController.update.form() as any).city_id = undefined"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus:ring-1 focus:ring-green-500 outline-none"
                                >
                                    <option :value="undefined" disabled>Seleccione una provincia</option>
                                    <option v-for="prov in provinces" :key="prov.id" :value="prov.id">{{ prov.name }}</option>
                                </select>
                                <InputError :message="errors.province_id" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="city">Ciudad</Label>
                                <select 
                                    v-model="(ProfileController.update.form() as any).city_id"
                                    :disabled="!filteredCities.length" 
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus:ring-1 focus:ring-green-500 outline-none disabled:opacity-50 disabled:bg-gray-100"
                                >
                                    <option :value="undefined" disabled>
                                        {{ (ProfileController.update.form() as any).province_id ? 'Seleccione una ciudad' : 'Primero elija una provincia' }}
                                    </option>
                                    <option v-for="city in filteredCities" :key="city.id" :value="city.id">
                                        {{ city.name }}
                                    </option>
                                </select>
                                <InputError :message="errors.city_id" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <Button :disabled="processing">Guardar Cambios</Button>
                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="recentlySuccessful" class="text-sm text-green-600 font-medium">¡Guardado con éxito!</p>
                        </Transition>
                    </div>
                </Form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>