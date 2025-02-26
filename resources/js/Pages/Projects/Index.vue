<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useProjectsStore } from '@/stores/projects';
import { storeToRefs } from 'pinia';
import { ref, inject, computed } from 'vue';
import { capitalizeFirstLetter } from '@/utils/formatting';

const { all } = storeToRefs(useProjectsStore());

// Use the Pinia store
const store = useProjectsStore();

// Fix useAsyncState call
import { useAsyncState } from '@vueuse/core';
const { isLoading, state, isReady } = useAsyncState(
  async () => {
    return await store.search({});
  },
  {},
  {
    delay: 0,
    resetOnExecute: false,
  },
);

const { screenWidth } = inject('screenSize');
const useCards = computed(_ => !(screenWidth.value >= 901)); // Fix this

// Headers
const headers = computed(() => {
    if (useCards.value) {
        return [
            { title: 'Title', key: 'title', type: 'title', truncate: true, list: [
                { label: 'Created', key: 'created_at', value: item => new Date(item.created_at).toDateString() },
            ]},
        ];
    } else {
        return [
            { title: 'Title', key: 'title', width: 'w-40', truncate: true },
            { title: 'Created', key: 'created_at', width: 'w-40', value: item => new Date(item.created_at).toDateString() },
        ];
    }
});

const search = () => {
    console.log({ ...searchParams.value });
    // router.post(item.destroy_url);
    store.search({ ...searchParams.value });
};

const create = () => router.visit('/projects/create');

const show = (item) => router.visit(`/projects/${item.hash}`);

const edit = (item) => router.visit(`/projects/${item.hash}/edit`);

const destroy = (item) => router.delete(`/api/v1/projects/${item.hash}`);

const searchParams = ref({
    term: '',
    type: [],
    page: 1
});

const fetchPage = ({page, itemsPerPage, sortBy}) => {
    searchParams.value.page = page;
    searchParams.value.per_page = itemsPerPage;
    searchParams.value.sortBy = sortBy;
    search();
};

const activeClass = (active) => {
    let classes = 'px-3 py-1 rounded-xl text-center font-bold';

    classes += active
        ? ' text-green-500 bg-green-200 border-green-600'
        : ' text-red-500 bg-red-200 border-red-600';

    return classes
};

</script>

<template>
    <Head title="Projects" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Projects
            </h2>
            <a class="cursor-pointer" @click.prevent="create">Create</a>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div v-if="isReady" class="p-6 text-gray-900 dark:text-gray-100 flex flex-col gap-2">
                        <!-- Fix SimpleDataCard -->
                        <!-- Add is_active to projects table -->
                        <component
                            :is="useCards ? 'SimpleDataTable' : 'SimpleDataTable'"
                            v-bind="{
                                data: all,
                                headers: headers,
                                actions: [
                                    { title: 'View', icon: 'file', action: item => show(item) },
                                    { title: 'Edit', icon: 'file-pen', action: item => edit(item) },
                                    { title: 'Delete', icon: 'file-circle-xmark', action: item => destroy(item) },
                                ],
                                selectable: true,
                                pagination: all,
                                isReady: isReady,
                            }"
                            @fetch-page="fetchPage">
                            <template #is_active="{ item }">
                                <div :class="[ activeClass(item.created_at), { 'mr-4': useCards } ]">
                                    {{ capitalizeFirstLetter(item.created_at ? 'ACTIVE' : 'NOT ACTIVE') }}
                                </div>
                            </template>
                        </component>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
