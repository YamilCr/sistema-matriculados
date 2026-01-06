<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue'; // Para la previsualización

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: edit().url,
    },
];

const page = usePage();
const user = page.props.auth.user;

// Lógica para previsualizar la imagen antes de subirla
const photoPreview = ref<string | null>(null);

const updatePhotoPreview = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (!file) return;

    // Usamos "as any" para evitar el error de "Property image does not exist"
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
                    description="Update your name, email address and profile picture"
                />

                <Form
                    v-bind="ProfileController.update.form()"
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <div class="grid gap-4">
                        <Label for="image">Profile Picture</Label>
                        
                        <div class="flex items-center gap-4">
                            <div class="relative h-20 w-20 rounded-full border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden bg-gray-50">
                                <img 
                                    v-if="photoPreview" 
                                    :src="photoPreview" 
                                    class="h-full w-full object-cover" 
                                />
                                <img 
                                    v-else-if="user.image" 
                                    :src="'/storage/' + user.image" 
                                    class="h-full w-full object-cover" 
                                />
                                <span v-else class="text-2xl font-bold text-gray-400 uppercase">
                                    {{ user.name.charAt(0) }}
                                </span>
                            </div>

                            <div class="flex-1">
                                <Input
                                    id="image"
                                    type="file"
                                    class="mt-1 block w-full text-sm"
                                    name="image"
                                    accept="image/*"
                                    @change="updatePhotoPreview"
                                />
                                <p class="text-[12px] text-muted-foreground mt-1">
                                    JPG, PNG o JPEG. Máximo 2MB.
                                </p>
                            </div>
                        </div>
                        <InputError class="mt-2" :message="errors.image" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            class="mt-1 block w-full"
                            name="name"
                            :default-value="user.name"
                            required
                            autocomplete="name"
                            placeholder="Full name"
                        />
                        <InputError class="mt-2" :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            name="email"
                            :default-value="user.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="errors.email" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="processing">Save Changes</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="recentlySuccessful" class="text-sm text-green-600 font-medium">
                                Saved successfully.
                            </p>
                        </Transition>
                    </div>
                </Form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>