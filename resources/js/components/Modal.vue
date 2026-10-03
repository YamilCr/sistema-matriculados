<script setup lang="ts">
import { onMounted, onUnmounted, watch } from 'vue'

interface Props {
    show: boolean
    maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | '2xl' | '3xl' | '4xl' | '5xl' | '6xl' | '7xl'
    closeable?: boolean
    // Definimos la prop de posición
    position?: 'top' | 'center'
}

const props = withDefaults(defineProps<Props>(), {
    maxWidth: '2xl',
    closeable: true,
    position: 'top', // Valor por defecto: Arriba
})

const emit = defineEmits<{
    close: []
}>()

// Bloqueo de scroll en el body para evitar el "double scroll"
watch(() => props.show, (value) => {
    if (value) document.body.style.overflow = 'hidden'
    else document.body.style.overflow = ''
})

const close = () => {
    if (props.closeable) {
        emit('close')
    }
}

const closeOnEscape = (e: KeyboardEvent) => {
    if (e.key === 'Escape' && props.show) {
        close()
    }
}

onMounted(() => document.addEventListener('keydown', closeOnEscape))
onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape)
    document.body.style.overflow = ''
})

const maxWidthClass = {
    sm: 'sm:max-w-sm',
    md: 'sm:max-w-md',
    lg: 'sm:max-w-lg',
    xl: 'sm:max-w-xl',
    '2xl': 'sm:max-w-2xl',
    '3xl': 'sm:max-w-3xl',
    '4xl': 'sm:max-w-4xl',
    '5xl': 'sm:max-w-5xl',
    '6xl': 'sm:max-w-6xl',
    '7xl': 'sm:max-w-7xl',
}[props.maxWidth]

// Lógica de clases para la posición vertical
// 'items-start' alinea al principio (top)
// 'items-center' alinea al centro (center)
const positionClass = {
    top: 'items-start pt-6 sm:pt-10',
    center: 'items-center'
}[props.position]
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-show="show"
                class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0 flex justify-center transition-all"
                :class="positionClass"
                scroll-region
            >
                <div
                    v-show="show"
                    class="fixed inset-0 transform transition-all"
                    @click="close"
                >
                    <div class="absolute inset-0 bg-gray-900/75 dark:bg-black/80 backdrop-blur-sm" />
                </div>

                <Transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div
                        v-show="show"
                        class="mb-6 bg-white dark:bg-slate-900 rounded-[2rem] overflow-hidden shadow-2xl transform transition-all sm:w-full sm:mx-auto relative z-10 border dark:border-white/10"
                        :class="maxWidthClass"
                        @click.stop
                    >
                        <slot v-if="show" />
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>