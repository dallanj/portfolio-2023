<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { computed, ref } from 'vue';

const props = defineProps({
    pagination: {
        type: Object,
        required: true
    },
    maxPageButtons: {
        type: Number,
        default: 5,
    }
});

const emits = defineEmits(['page-changed', 'clear-sorting']);

const clearSorting = () => {
    emits('clear-sorting');
}

const itemsPerPage = ref(8);
const sortBy = ref('');

const pages = computed(() => {
    const pages = [];
    const half = Math.floor(props.maxPageButtons / 2);
    let start = props.pagination.current_page - half;
    let end = props.pagination.current_page + half;

    if (start < 1) {
        start = 1;
        end = start + props.maxPageButtons - 1;
    }

    if (end > props.pagination.last_page) {
        end = props.pagination.last_page;
        start = end - props.maxPageButtons + 1;
        if (start < 1) {
            start = 1;
        }
    }

    for (let i = start; i <= end; i++) {
        pages.push(i);
    }

    return pages;
});

const changePage = (page) => {
    if (page >= 1 && page <= props.pagination.last_page) {
        emits('page-changed', {
            page: page,
            itemsPerPage: itemsPerPage.value,
            sortBy: sortBy.value,
        });
    }
};

const options = ref([1, 2, 3, 4, 8]);

const extractPage = (url) => {
    const match = url.match(/page=(\d+)/);
    const page = match ? match[1] : null;
    return page;
}
</script>

<template>
<nav
    class="flex items-center flex-cols flex-wrap md:flex-row justify-between py-2 px-4"
    aria-label="Table navigation">
    <div class="flex gap-2">
        <button @click="clearSorting">
            <FontAwesomeIcon icon="filter-circle-xmark" />
        </button>
        <p class="font-semibold text-sm text-brand-white space-x-1">
            <span class="text-brand-light-gray">Showing</span>
            <span>{{ pagination.from }}-{{ pagination.to }}</span>
            <span class="text-brand-light-gray">of</span>
            <span>{{ pagination.total }}</span>
        </p>
    </div>
    
    <ul class="inline-flex items-center -space-x-px space-x-1">
        <li class="page-item" :class="{ disabled: !pagination.first_page_url }">
            <a
                class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white dark:bg-gray-700 dark:border-gray-800 hover:dark:bg-gray-600 text-gray-900 dark:text-gray-100 dark:hover:text-gray-200 border border-gray-300 hover:bg-gray-100 hover:text-gray-700"
                :href="pagination.first_page_url"
                @click.prevent="changePage(1)">
                <FontAwesomeIcon icon="angles-left" />
            </a>
        </li>
        <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
            <a
                class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white dark:bg-gray-700 dark:border-gray-800 hover:dark:bg-gray-600 text-gray-900 dark:text-gray-100 dark:hover:text-gray-200 border border-gray-300 hover:bg-gray-100 hover:text-gray-700"
                :href="pagination.prev_page_url"
                @click.prevent="changePage(extractPage(pagination.prev_page_url))">
                <FontAwesomeIcon icon="angle-left" />
            </a>
        </li>
        <li
            v-for="page in pages"
            :key="page"
            class="page-item"
            :class="{ 'active': page === pagination.current_page }">
            <span
                v-if="page === pagination.current_page"
                class="flex items-center justify-center px-3 h-8 ms-0 leading-tight font-bold text-white bg-brand-primary border border-brand-primary cursor-default rounded">
                {{ page }}
            </span>
            <a
                v-else
                class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white dark:bg-gray-700 dark:border-gray-800 hover:dark:bg-gray-600 text-gray-900 dark:text-gray-100 dark:hover:text-gray-200 border border-gray-300 hover:bg-gray-100 hover:text-gray-700 rounded"
                :href="`${pagination.path}?page=${page}`"
                @click.prevent="changePage(page)">
                {{ page }}
            </a>
        </li>

        <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
            <a
                class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white dark:bg-gray-700 dark:border-gray-800 hover:dark:bg-gray-600 text-gray-900 dark:text-gray-100 dark:hover:text-gray-200 border border-gray-300 hover:bg-gray-100 hover:text-gray-700"
                :href="pagination.next_page_url"
                @click.prevent="changePage(extractPage(pagination.next_page_url))">
                <FontAwesomeIcon icon="angle-right" />
            </a>
        </li>
        <li
            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white dark:bg-gray-700 dark:border-gray-800 hover:dark:bg-gray-600 text-gray-900 dark:text-gray-100 dark:hover:text-gray-200 border border-gray-300 hover:bg-gray-100 hover:text-gray-700"
            :class="{ disabled: !pagination.last_page_url }">
            <a class="page-link" :href="pagination.last_page_url" @click.prevent="changePage(pagination.last_page)">
                <FontAwesomeIcon icon="angles-right" />
            </a>
        </li>
    </ul>
    <SimpleDropdown
        v-model="itemsPerPage"
        :options="options"
        label="Items Per Page"
        side-label
        placeholder="Items Per Page"
        @update:modelValue="changePage(pagination.current_page)" />
</nav>
</template>
    