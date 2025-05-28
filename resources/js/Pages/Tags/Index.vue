<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { useTagsStore } from '@/stores/tags';
import { storeToRefs } from 'pinia';
import { watch, nextTick, ref, inject, computed } from 'vue';
import { capitalizeFirstLetter } from '@/utils/formatting';

const { all } = storeToRefs(useTagsStore());
const { openModal } = inject('modals');
const data = ref(null);

// Use the Pinia store
const store = useTagsStore();

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
                { label: 'Active', key: 'is_active', value: item.is_active },
                { label: 'Created', key: 'created_at', value: item => new Date(item.created_at).toDateString() }
            ]},
        ];
    } else {
        return [
            { title: 'Name', key: 'name', width: 'w-40', sortable: true, truncate: true },
            { title: 'Active', key: 'is_active', width: 'w-10', sortable: true, truncate: true },
            { title: 'Created', key: 'created_at', width: 'w-40', sortable: true, value: item => new Date(item.created_at).toDateString() },
        ];
    }
});

const search = () => {
    store.search({ ...searchParams.value });
};

const create = () => {
    tagModal({
        title: 'Create Tag',
        position: 'justify-content: safe center; align-items: center;',
    });
};

const edit = (tag) => {
    tagModal({
        title: 'Update Tag',
        subtitle: `You are updating the tag ${tag.name}.`,
        tag: tag,
        position: 'justify-content: safe center; align-items: center;',
    });
};

const tagModal = (data) => {
    openModal('TagsModal', data);
}

const destroy = (item) => router.delete(`/api/v1/tags/${item.hash}`);

const searchParams = ref({
    term: '',
    type: [],
    page: 1,
    per_page: 8,
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
        ? ' text-green-500 bg-green-200 border-green-600'
        : ' text-red-500 bg-red-200 border-red-600';

    return classes
};
</script>

<template>
    <Head title="Tags" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Tags
            </h2>
            <SimpleButton @click="create">
                Create Tag
            </SimpleButton>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div v-if="isReady && all?.data" class="p-6 text-gray-900 dark:text-gray-100 flex flex-col gap-2">
                        <form class="grid grid-cols-12 gap-x-4 mb-1" @submit.prevent>
                            <SimpleSearch
                                class="col-span-6"
                                v-model="searchParams.term"
                                placeholder="Search..."
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
                            <template #is_active="{ item }">
                                <div :class="[ activeClass(item.is_active), { 'mr-4': useCards } ]">
                                    {{ capitalizeFirstLetter(item.is_active ? 'ACTIVE' : 'NOT ACTIVE') }}
                                </div>
                            </template>
                        </component>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
