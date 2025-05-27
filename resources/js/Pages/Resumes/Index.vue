<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { useResumesStore } from '@/stores/resumes';
import { storeToRefs } from 'pinia';
import { watch, nextTick, ref, inject, computed } from 'vue';
import { capitalizeFirstLetter } from '@/utils/formatting';

const { openModal } = inject('modals');
const { all } = storeToRefs(useResumesStore());

const data = ref(null);

// Use the Pinia store
const store = useResumesStore();

// Search filters
const searchParams = ref({
    term: '',
    type: [],
    page: 1,
    per_page: 8,
    sortBy: [],
});

// Fix useAsyncState call
import { useAsyncState } from '@vueuse/core';
const { isLoading, state, isReady, execute } = useAsyncState(
    async () => {
        return await store.search({ ...searchParams.value });
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
            { title: 'Title', key: 'title', type: 'title', truncate: true, list: [
                { label: 'Version', key: 'version', value: item => item?.version ?? '-' },
                { label: 'Draft', key: 'is_draft', value: item => item?.is_draft ?? false },
                { label: 'Created', key: 'created_at', value: item => item?.created_at ? new Date(item.created_at).toDateString() : '-' }
            ]},
        ];
    } else {
        return [
            { title: 'Title', key: 'title', width: 'w-40', sortable: true, truncate: true },
            { title: 'Version', key: 'version', width: 'w-12', sortable: true, truncate: true },
            { title: 'Draft', key: 'is_draft', width: 'w-12', sortable: true, truncate: true },
            { title: 'Created', key: 'created_at', width: 'w-20', sortable: true, value: item => item ? new Date(item?.created_at).toDateString() : '-' },
        ];
    }
});

// API routes
const search = () => execute();
// const search = () => store.search({ ...searchParams.value });
const destroy = (item) => store.destroy(item);

// Web intertia routes
const create = () => router.visit('/resumes/create');
const show = (item) => router.visit(`/resumes/${item?.hash}`);
const edit = (item) => router.visit(`/resumes/${item?.hash}/edit`);

// Pagination
const fetchPage = ({page, itemsPerPage, sortBy}) => {
    searchParams.value.page = page ?? searchParams.value.page;
    searchParams.value.per_page = itemsPerPage ?? searchParams.value.per_page;
    searchParams.value.sortBy = sortBy ?? searchParams.value.sortBy;
    execute();
};

// Styling for active items
const activeClass = (active) => {
    let classes = 'px-3 py-1 rounded-xl text-center font-bold';

    classes += active
        ? ' text-red-500 bg-red-200 border-red-600'
        : ' text-green-500 bg-green-200 border-green-600';

    return classes
};

// Checked rows
const selectedItems = ref([]);
const selectedItem = (items) => {
    selectedItems.value = items;
};
</script>

<template>
    <Head title="Resumes" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Resumes
            </h2>
            <SimpleButton @click.prevent="create">
                Create Resume
            </SimpleButton>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-col gap-2">
                        <form class="grid grid-cols-6 gap-x-4 mb-1" @submit.prevent>
                            <SimpleSearch
                                class="col-span-3"
                                v-model="searchParams.term"
                                placeholder="Search..."
                                label="Search"
                                :disabled="!(isReady && all?.data)"
                                @input="search"
                                @search="search" />
                            <SimpleDropdown
                                v-model="selected"
                                class="col-span-3 self-end justify-self-end"
                                :options="[
                                    { 
                                        label: 'Publish', value: 'store.publish', onSelect: async () => {
                                            if (selectedItems.length > 1) {
                                                return $toast.error('Only one resume can be active at a time.');
                                            }
                                            try {
                                                await store.publish({ids: selectedItems});
                                                await execute();
                                                $toast.success('You have successfully published a resume.');
                                            } catch (error) {
                                                $toast.error('An unexpected error happened, the resume could not be published.');
                                            }
                                        }
                                    },
                                    { 
                                        label: 'Draft', value: 'store.draft', onSelect: async () => {
                                            try {
                                                await store.draft({ids: selectedItems});
                                                await execute();
                                                $toast.success('You have successfully saved a resume as a draft.');
                                            } catch (error) {
                                                $toast.error('An unexpected error happened, the resume could not be drafted.');
                                            }
                                        }
                                    },
                                    { 
                                        label: 'Delete', value: 'store.bulkDelete', onSelect: async () => {
                                            const result = await openModal('ConfirmationModal', {
                                                title: 'Confirmation',
                                                subtitle: 'Are you positive on proceeding to delete these records?',
                                                position: 'justify-content: safe center; align-items: center;',
                                            });

                                            if (result === true) {
                                                try {
                                                    await store.bulkDelete({ids: selectedItems});
                                                    await execute();
                                                    $toast.success('You have successfully deleted records.');
                                                } catch (error) {
                                                    $toast.error('An unexpected error happened, the records could not be deleted.');
                                                }
                                            }
                                        }
                                    }
                                ]"
                                placeholder="With Selected"
                                :disabled="!selectedItems.length" />
                        </form>
                        <!-- Fix SimpleDataCard -->
                        <component
                            :is="useCards ? 'SimpleDataTable' : 'SimpleDataTable'"
                            v-bind="{
                                data: data,
                                headers: headers,
                                actions: [
                                    { 
                                        title: 'Edit', 
                                        icon: 'file-pen', 
                                        action: item => edit(item) 
                                    },
                                    { 
                                        title: 'Publish',
                                        icon: 'file-export',
                                        action: async (item) => {
                                            if (!item.is_draft) {
                                                return $toast.info('This resume is currently published.');
                                            }
                                            try {
                                                await store.publish({ids: [item.id]});
                                                await execute();
                                                $toast.success('You have successfully published a resume.');
                                                // search();
                                            } catch (error) {
                                                $toast.error('An unexpected error happened, the resume could not be published.');
                                            }
                                        },
                                    },
                                    {
                                        title: 'Convert to Draft',
                                        icon: 'archive',
                                        action: async (item) => {
                                            if (item.is_draft) {
                                                return $toast.info('This resume is currently a draft copy.');
                                            }
                                            try {
                                                await store.publish({ids: [item.id]});
                                                await execute();
                                                $toast.success('You have successfully saved a resume as a draft.');
                                            } catch (error) {
                                                $toast.error('An unexpected error happened, the resume could not be drafted.');
                                            }
                                        }
                                    },
                                    {
                                        title: 'Delete',
                                        icon: 'file-circle-xmark',
                                        action: async (item) => {
                                            const result = await openModal('ConfirmationModal', {
                                                title: 'Confirmation',
                                                subtitle: 'Are you positive on proceeding to delete this resume?',
                                                position: 'justify-content: safe center; align-items: center;',
                                            });

                                            if (result === true) {
                                                try {
                                                    await store.destroy(item);
                                                    await execute();
                                                    $toast.success('You have successfully deleted records.');
                                                } catch (error) {
                                                    $toast.error('An unexpected error happened, the records could not be deleted.');
                                                }
                                            }
                                        }
                                    },
                                ],
                                selectable: true,
                                pagination: all,
                                isReady: isReady,
                            }"
                            @fetch-page="fetchPage"
                            @update:selected="selectedItem">
                            <template #is_draft="{ item }">
                                <div v-if="item" :class="[ activeClass(item?.is_draft), { 'mr-4': useCards } ]">
                                    {{ capitalizeFirstLetter(item?.is_draft ? 'DRAFT' : 'ACTIVE') }}
                                </div>
                            </template>
                        </component>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
