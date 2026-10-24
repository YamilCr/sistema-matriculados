<script setup lang="ts">
import Modal from '@/components/Modal.vue';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { computed, ref } from 'vue';

interface Column {
    key: string; // admite rutas tipo "province.name"
    label: string;
    align?: 'left' | 'center';
}

interface Field {
    key: string;
    label: string;
    type?: 'text' | 'select';
    options?: { value: number | string; label: string }[];
    placeholder?: string;
}

const props = defineProps<{
    title: string;
    subtitle: string;
    singular: string; // "Provincia", "Ciudad"...
    rows: Record<string, any>[];
    columns: Column[];
    fields: Field[];
    storeUrl: string;
    updateUrl: (id: number) => string;
    destroyUrl: (id: number) => string;
}>();

const get = (row: Record<string, any>, path: string) =>
    path.split('.').reduce((acc, k) => acc?.[k], row) ?? '—';

const search = ref('');
const filtered = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (!q) return props.rows;
    return props.rows.filter((r) =>
        props.columns.some((c) => String(get(r, c.key)).toLowerCase().includes(q)),
    );
});

// Formulario dinámico según los campos recibidos
const blank = () => Object.fromEntries(props.fields.map((f) => [f.key, f.type === 'select' ? '' : '']));
const form = useForm<Record<string, any>>(blank());
const editingId = ref<number | null>(null);
const open = ref(false);

const openCreate = () => {
    form.defaults(blank()).reset();
    form.clearErrors();
    editingId.value = null;
    open.value = true;
};

const openEdit = (row: Record<string, any>) => {
    const data = Object.fromEntries(props.fields.map((f) => [f.key, row[f.key] ?? '']));
    form.defaults(data).reset();
    form.clearErrors();
    editingId.value = row.id;
    open.value = true;
};

const close = () => {
    if (!form.processing) open.value = false;
};

const submit = () => {
    const options = { preserveScroll: true, onSuccess: () => (open.value = false) };
    if (editingId.value === null) form.post(props.storeUrl, options);
    else form.put(props.updateUrl(editingId.value), options);
};

const remove = async (row: Record<string, any>) => {
    const result = await Swal.fire({
        title: `¿Eliminar ${props.singular.toLowerCase()}?`,
        text: String(row.name),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
    });
    if (result.isConfirmed) router.delete(props.destroyUrl(row.id), { preserveScroll: true });
};
</script>

<template>
    <div class="mx-auto w-full max-w-5xl p-6">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ title }}</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ subtitle }}</p>
            </div>
            <button
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 font-medium text-white shadow-sm hover:bg-blue-700"
                @click="openCreate"
            >
                + Nueva {{ singular.toLowerCase() }}
            </button>
        </div>

        <input
            v-model="search"
            type="text"
            :placeholder="`Buscar ${title.toLowerCase()}...`"
            class="mb-2 block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
        />
        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Mostrando {{ filtered.length }} de {{ rows.length }}
        </p>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-md dark:border-gray-700 dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th
                                v-for="c in columns"
                                :key="c.key"
                                class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400"
                                :class="c.align === 'center' ? 'text-center' : 'text-left'"
                            >
                                {{ c.label }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                        <tr v-for="row in filtered" :key="row.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td
                                v-for="c in columns"
                                :key="c.key"
                                class="px-6 py-4 text-sm text-gray-900 dark:text-white"
                                :class="c.align === 'center' ? 'text-center' : ''"
                            >
                                {{ get(row, c.key) }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                <button class="mr-4 text-green-600 hover:text-green-900 dark:text-green-400" @click="openEdit(row)">
                                    Editar
                                </button>
                                <button class="text-red-600 hover:text-red-900 dark:text-red-400" @click="remove(row)">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                        <tr v-if="filtered.length === 0">
                            <td :colspan="columns.length + 1" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                No se encontraron resultados
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Modal :show="open" max-width="md" @close="close">
            <form class="p-6" @submit.prevent="submit">
                <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">
                    {{ editingId === null ? `Nueva ${singular.toLowerCase()}` : `Editar ${singular.toLowerCase()}` }}
                </h2>

                <div v-for="f in fields" :key="f.key" class="mb-4">
                    <label class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">{{ f.label }}</label>
                    <select
                        v-if="f.type === 'select'"
                        v-model="form[f.key]"
                        class="w-full rounded-lg border px-4 py-2 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white"
                    >
                        <option value="" disabled>Seleccione...</option>
                        <option v-for="o in f.options" :key="o.value" :value="o.value">{{ o.label }}</option>
                    </select>
                    <input
                        v-else
                        v-model="form[f.key]"
                        type="text"
                        :placeholder="f.placeholder"
                        class="w-full rounded-lg border px-4 py-2 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white"
                    />
                    <p v-if="form.errors[f.key]" class="mt-1 text-xs text-red-500">{{ form.errors[f.key] }}</p>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        :disabled="form.processing"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 hover:bg-gray-50 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                        @click="close"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-blue-600 px-6 py-2 font-bold text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Guardando...' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </Modal>
    </div>
</template>
