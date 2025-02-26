<template>
<nav
    class="flex items-center flex-cols flex-wrap md:flex-row justify-between py-2 px-4"
    aria-label="Table navigation">
    <p class="font-semibold text-sm text-brand-dark-gray space-x-1">
        <span class="text-brand-light-gray">Showing</span>
        <span>{{ pagination.from }}-{{ pagination.to }}</span>
        <span class="text-brand-light-gray">of</span>
        <span>{{ pagination.total }}</span>
    </p>
    <ul class="inline-flex -space-x-px rtl:space-x-reverse text-sm h-8">
        <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
            <a
                class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700"
                href="#"
                @click.prevent="changePage(1)">
                <FontAwesomeIcon icon="angles-left" />
            </a>
        </li>
        <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
            <a
                class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700"
                href="#"
                @click.prevent="changePage(pagination.current_page - 1)">
                <FontAwesomeIcon icon="angle-left" />
            </a>
        </li>
        <li
            v-for="page in pages"
            :key="page"
            class="page-item"
            :class="{ 'active': page === pagination.current_page }">
            <a
                class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700"
                href="#"
                @click.prevent="changePage(page)">
                {{ page }}
            </a>
        </li>
        <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
            <a
                class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700"
                href="#"
                @click.prevent="changePage(pagination.current_page + 1)">
                <FontAwesomeIcon icon="angle-right" />
            </a>
        </li>
        <li
            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700"
            :class="{ disabled: !pagination.next_page_url }">
            <a class="page-link" href="#" @click.prevent="changePage(pagination.last_page)">
                <FontAwesomeIcon icon="angles-right" />
            </a>
        </li>
    </ul>
    <select v-model="itemsPerPage" @change="changePage(pagination.current_page)">
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="15">15</option>
    </select>
</nav>
</template>

<script setup>
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

const emits = defineEmits(['page-changed']);

const itemsPerPage = ref(10);
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
</script>
    