<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { useContactsStore } from '@/stores/contacts';
import { storeToRefs } from 'pinia';
import { watch, nextTick, ref, inject, computed } from 'vue';
import { capitalizeFirstLetter } from '@/utils/formatting';

const { all } = storeToRefs(useContactsStore());

const data = ref(null);

// Use the Pinia store
const store = useContactsStore();

// Fix useAsyncState call
import { useAsyncState } from '@vueuse/core';
const { isLoading, state, isReady } = useAsyncState(
  async () => {
    return await store.search();
  },
  {},
  {
    delay: 0,
    resetOnExecute: false,
  },
);

watch(all, async (obj) => {
    await nextTick();
    if (obj) {
        data.value = obj;
    }
}, {
    deep: true
});

const { screenWidth } = inject('screenSize');
const useCards = computed(_ => !(screenWidth.value >= 901)); // Fix this

// Headers
const headers = computed(() => {
    if (useCards.value) {
        return [
            { title: 'Name', key: 'name', type: 'title', truncate: true, list: [
                { label: 'Email', key: 'email', value: item => item?.version ?? '-' },
                { label: 'Read', key: 'is_read', value: item => item?.is_read ?? false },
                { label: 'Created', key: 'created_at', value: item => item?.created_at ? new Date(item.created_at).toDateString() : '-' }
            ]},
        ];
    } else {
        return [
            { title: 'Name', key: 'name', width: 'w-40', sortable: true, truncate: true },
            { title: 'Email', key: 'email', width: 'w-12', sortable: true, truncate: true },
            { title: 'Read', key: 'is_read', width: 'w-12', sortable: true, truncate: true },
            { title: 'Created', key: 'created_at', width: 'w-20', sortable: true, value: item => item ? new Date(item?.created_at).toDateString() : '-' },
        ];
    }
});

const search = () => {
    store.search({ ...searchParams.value });
};

const create = () => router.visit('/contacts/create');

const show = (item) => router.visit(`/contacts/${item?.hash}`);

const edit = (item) => router.visit(`/contacts/${item?.hash}/edit`);

const destroy = (item) => router.delete(`/api/v1/contacts/${item?.hash}`);

const searchParams = ref({
    term: '',
    type: [],
    page: 1,
    per_page: 4,
    sortBy: [],
});

const fetchPage = ({page, itemsPerPage, sortBy}) => {
    searchParams.value.page = page ?? searchParams.value.page;
    searchParams.value.per_page = itemsPerPage ?? searchParams.value.per_page;
    searchParams.value.sortBy = sortBy ?? searchParams.value.sortBy;
    search();
};

const activeClass = (active) => {
    let classes = 'px-3 py-1 rounded-xl text-center font-bold';

    classes += active
        ? ' text-red-500 bg-red-200 border-red-600'
        : ' text-green-500 bg-green-200 border-green-600';

    return classes
};
</script>

<template>
    <Head title="Messages" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Messages
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-col gap-2">
                        <form class="grid grid-cols-12 gap-x-4 mb-1" @submit.prevent>
                            <SimpleSearch
                                class="col-span-6"
                                v-model="searchParams.term"
                                placeholder="Search..."
                                label="Search"
                                :disabled="!(isReady && all?.data)"
                                @input="search"
                                @search="search" />
                        </form>
                        <!-- Fix SimpleDataCard -->
                        <component
                            :is="useCards ? 'SimpleDataTable' : 'SimpleDataTable'"
                            v-bind="{
                                data: data,
                                headers: headers,
                                actions: [
                                    { title: 'Edit', icon: 'file-pen', action: item => edit(item) },
                                    { title: 'Delete', icon: 'file-circle-xmark', action: item => destroy(item) },
                                ],
                                selectable: true,
                                pagination: all,
                                isReady: isReady,
                            }"
                            @fetch-page="fetchPage">
                            <template #is_read="{ item }">
                                <div v-if="item" :class="[ activeClass(item?.is_read), { 'mr-4': useCards } ]">
                                    {{ capitalizeFirstLetter(item?.is_read ? 'READ' : 'UNREAD') }}
                                </div>
                            </template>
                        </component>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
