<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useContactsStore } from '@/stores/contacts';
import { storeToRefs } from 'pinia';
import { watch, nextTick, ref, onMounted, inject, computed } from 'vue';

const { active } = storeToRefs(useContactsStore());

const contact = ref(null);
const isReady = ref(false);

onMounted(async () => {
    // await useContactsStore().show();
    isReady.value = true;
});


watch(active, async (obj) => {
    await nextTick();
    if (obj) {
        contact.value = obj;
    }
});
</script>

<template>
    <Head title="Messages" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Messages > Viewing Message
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-col gap-2">
                        <pre class="max-h-48 border border-gray-300 dark:border-app-header-bb p-4 rounded text-sm overflow-x-auto whitespace-pre-wrap bg-white dark:bg-sidebar-bg text-gray-900 dark:text-gray-100">{{ contact?.message }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
