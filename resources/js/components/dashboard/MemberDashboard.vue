<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import MemberCard from '@/components/MemberCard.vue';
import { edit as editProfile } from '@/routes/profile';

const page = usePage()
const user = computed(() => page.props.auth.user as any)

const props = defineProps<{
    member: any 
}>()

// Qué significa cada estado y qué tiene que hacer el matriculado (los estados son fijos)
const statusInfo: Record<string, { icon: string; tone: string; text: string; action: string }> = {
    'Al día': {
        icon: '✅',
        tone: 'border-green-200 bg-green-50 text-green-900 dark:border-green-500/30 dark:bg-green-900/20 dark:text-green-100',
        text: 'Tu matrícula está habilitada para ejercer. No registrás deudas.',
        action: 'No tenés que hacer nada.',
    },
    Moroso: {
        icon: '⚠️',
        tone: 'border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-500/30 dark:bg-amber-900/20 dark:text-amber-100',
        text: 'Registrás una deuda mayor a 3 meses con el colegio.',
        action: 'Regularizá tu situación para evitar la suspensión de tu matrícula.',
    },
    Suspendido: {
        icon: '⛔',
        tone: 'border-red-200 bg-red-50 text-red-900 dark:border-red-500/30 dark:bg-red-900/20 dark:text-red-100',
        text: 'Tu matrícula está inhabilitada por una sanción.',
        action: 'Comunicate con el personal del colegio para conocer los pasos a seguir.',
    },
}

const status = computed(() => {
    const name: string = props.member?.account_status?.name ?? ''
    return { name, ...(statusInfo[name] ?? { icon: 'ℹ️', tone: 'border-slate-200 bg-white text-slate-800', text: props.member?.account_status?.description ?? '', action: '' }) }
})

const lastAccess = new Date().toLocaleDateString('es-ES', { 
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
});
</script>

<template>
    <Head title="Mi Perfil Profesional" />
    
    <div class="min-h-screen transition-colors duration-500 bg-slate-50 dark:bg-[#050505] p-4 md:p-12 flex flex-col items-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-80 bg-emerald-700 dark:bg-emerald-900/40 -z-10 clip-path-slant opacity-90"></div>

        <MemberCard 
            :user-name="user.name" 
            :user-image="user.image" 
            :member="member" 
        />
        
        <section :class="['mt-8 w-full max-w-2xl rounded-2xl border p-5', status.tone]" data-testid="estado-cuenta">
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] opacity-70">Estado de tu cuenta</p>
            <p class="mt-1 text-lg font-bold">{{ status.icon }} {{ status.name }}</p>
            <p class="mt-2 text-sm">{{ status.text }}</p>
            <p v-if="status.action" class="mt-1 text-sm font-semibold">{{ status.action }}</p>
        </section>

        <div class="mt-4 w-full max-w-2xl text-center">
            <Link :href="editProfile()" class="inline-block rounded-lg bg-emerald-600 px-5 py-2 text-sm font-bold text-white transition hover:bg-emerald-700">
                Actualizar mi teléfono y domicilio
            </Link>
        </div>

        <footer class="mt-12 max-w-2xl w-full text-center border-t border-slate-200 dark:border-white/5 pt-8">
            <p class="text-[10px] font-bold text-slate-400 dark:text-emerald-500/50 uppercase tracking-[0.2em]">
                Sistema Federal de Colegiación
            </p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                Sesión activa: <span class="font-semibold text-slate-700 dark:text-slate-200">{{ user.name }}</span><br>
                <span class="text-xs italic opacity-60">Acceso: {{ lastAccess }}</span>
            </p>
        </footer>
    </div>
</template>

<style scoped>
.clip-path-slant {
    clip-path: polygon(0 0, 100% 0, 100% 60%, 0% 100%);
}
</style>