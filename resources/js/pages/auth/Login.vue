<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { demoAccounts } from '@/lib/demoAccounts';
import { Form, Head, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const page = usePage<{ demo?: boolean }>();
const loadingDemo = ref<string | null>(null);

// Entra directo con una cuenta de prueba (solo modo demo)
const loginAs = (account: (typeof demoAccounts)[number]) => {
    loadingDemo.value = account.email;
    router.post(
        store().url,
        { email: account.email, password: account.password },
        { onFinish: () => (loadingDemo.value = null) },
    );
};
</script>

<template>
    <AuthBase
        title="Iniciá sesión"
        description="Ingresá tu email y contraseña para acceder al sistema"
    >
        <Head title="Iniciar sesión" />

        <div
            v-if="status"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            {{ status }}
        </div>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="correo@ejemplo.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Contraseña</Label>
                        <TextLink
                            v-if="canResetPassword && !page.props.demo"
                            :href="request()"
                            class="text-sm"
                            :tabindex="5"
                        >
                            ¿Olvidaste tu contraseña?
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="Contraseña"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" name="remember" :tabindex="3" />
                        <span>Recordarme</span>
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <Spinner v-if="processing" />
                    Ingresar
                </Button>
            </div>

        </Form>

        <div
            v-if="page.props.demo"
            class="mt-2 rounded-lg border border-dashed border-indigo-300 bg-indigo-50 p-4 dark:border-indigo-800 dark:bg-indigo-950/40"
            data-test="demo-accounts"
        >
            <p class="mb-3 text-center text-sm font-semibold text-indigo-900 dark:text-indigo-200">
                Modo demo: entrá con un clic
            </p>
            <div class="grid gap-2">
                <button
                    v-for="a in demoAccounts"
                    :key="a.email"
                    type="button"
                    :disabled="loadingDemo !== null"
                    class="flex items-center gap-3 rounded-md border border-indigo-200 bg-white px-3 py-2 text-left transition hover:border-indigo-500 disabled:opacity-60 dark:border-indigo-900 dark:bg-slate-900"
                    @click="loginAs(a)"
                >
                    <span class="text-xl">{{ a.icon }}</span>
                    <span class="flex-1">
                        <span class="block text-sm font-semibold">{{ a.role }}</span>
                        <span class="block text-xs text-muted-foreground">{{ a.note }}</span>
                    </span>
                    <Spinner v-if="loadingDemo === a.email" />
                </button>
            </div>
        </div>
    </AuthBase>
</template>
