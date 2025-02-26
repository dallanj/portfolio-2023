<template>
<section class="simple-data-table">
    <table class="simple-data-table__container">
        <thead class="simple-data-table__headers">
            <tr>
                <th v-if="selectable" class="header w-10">
                    <Checkbox
                        :checked="allSelected"
                        @change="toggleSelectAll" />
                </th>
                <th v-if="expandable" class="header w-10">
                </th>
                <th
                    v-for="header in headers"
                    :key="header.key"
                    scope="col"
                    class="header"
                    :class="header.width">
                    {{ header.title }}
                </th>
            </tr>
        </thead>
        <tbody v-if="isReady && data?.data">
            <template v-for="item in data.data" :key="item.id">
                <tr class="simple-data-table__body">
                    <td v-if="selectable" class="row">
                        <Checkbox
                            :disabled="item.unselectable"
                            :checked="selectedItems.includes(item.id)"
                            @change="toggleSelectItem(item.id)" />
                    </td>
                    <td v-if="expandable" class="text-center">
                    <!-- Expander Button -->
                        <button
                            v-if="isRowExpanded(item.id)"
                            class="h-8 w-8 rounded-full text-center hover:bg-blue-200 hover:text-white"
                            @click="toggleRowExpansion(item.id)">
                            <FontAwesomeIcon icon="chevron-down" />
                        </button>
                        <button
                            v-else
                            class="h-8 w-8 rounded-full text-center hover:bg-blue-200 hover:text-white"
                            @click="toggleRowExpansion(item.id)">
                            <FontAwesomeIcon icon="chevron-right" />
                        </button>
                    </td>
                    <td
                        v-for="header in headers"
                        :key="header.key"
                        class="row">
                        <slot :name="header.key" :item="item">
                            <span
                                v-tooltip="`${header.truncate ? getItemValue(header, item) : ''}`"
                                :class="{ 'truncate': header.truncate }">
                                {{ getItemValue(header, item) }}
                            </span>
                        </slot>
                        <SimpleDataActions
                            v-if="hasActions && header.key === 'actions'"
                            state="action"
                            size="action"
                            :actions="actions"
                            @action-selected="(e) => e.action(item)">
                        </SimpleDataActions>
                    </td>
                </tr>
                <!-- Expanded Row Content -->
                <tr v-if="isRowExpanded(item.id)" class="simple-data-table__expanded-content">
                    <td :colspan="headers.length + 2">
                        <slot name="expanded" :item="item" />
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

    <SimpleLoadingSpinner v-if="!isReady || !data?.data" :size="'2xl'" />
    
    <SimpleDataPagination
        v-if="pagination"
        v-bind="$attrs"
        :pagination="pagination"
        @page-changed="fetchPage" />
</section>
</template>
    
<script setup>
import { ref, computed } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    headers: {
        type: Array,
        required: true
    },
    data: {
        type: Object,
        required: true
    },
    actions: {
        type: Array,
        default: () => []
    },
    selectable: {
        type: Boolean,
        default: false,
    },
    expandable: {
        type: Boolean,
        default: false,
    },
    pagination: {
        type: Object,
        default: null
    },
    isReady: {
        type: Boolean,
        default: false
    }
});

import { useHeaders } from '@/composables/useHeaders.vue';
const { getItemValue, hasActions, headers } = useHeaders(props);

// Selectable - Select data rows
const selectedItems = ref([]);
const allSelected = computed(() => selectedItems.value.length === props.data?.data?.length);
const toggleSelectAll = () => {
    selectedItems.value = allSelected.value
        ? []
        : props.data?.data?.map(item => item.id);
};
const toggleSelectItem = (id) => {
    selectedItems.value = selectedItems.value.includes(id)
        ? selectedItems.value.filter(itemId => itemId !== id)
        : selectedItems.value.push(id);
};

const expandedRows = ref([]);
const isRowExpanded = (id) => expandedRows.value.includes(id);
const toggleRowExpansion = (id) => {
    expandedRows.value = isRowExpanded(id)
    ? expandedRows.value.filter(rowId => rowId !== id)
    : [...expandedRows.value, id];
};

// Pagination - Get pages
import { usePagination } from '@/composables/usePagination.vue';
const emit = defineEmits(['fetch-page']);
const fetchPage = ({page, itemsPerPage, sortBy}) => {
    console.log('SimpleDtatabable', page, itemsPerPage, sortBy)
    emit('fetch-page', {
            page: page,
            itemsPerPage: itemsPerPage,
            sortBy: sortBy,
        });
};
</script>
    