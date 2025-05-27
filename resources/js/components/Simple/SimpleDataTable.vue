<script setup>
import { ref, computed, watch } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    headers: {
        type: Array,
        required: true
    },
    data: {
        type: Object,
        default: () => {}
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
const allSelected = computed(() => selectedItems.value.length === props?.data?.data?.length);
const toggleSelectAll = () => {
    selectedItems.value = allSelected.value
        ? []
        : props?.data?.data?.map(item => item.id);
};
const toggleSelectItem = (id) => {
    if (selectedItems.value.includes(id)) {
        selectedItems.value = selectedItems.value.filter(itemId => itemId !== id);
    } else {
        selectedItems.value = [...selectedItems.value, id];
    }
};

const expandedRows = ref([]);
const isRowExpanded = (id) => expandedRows.value.includes(id);
const toggleRowExpansion = (id) => {
    expandedRows.value = isRowExpanded(id)
    ? expandedRows.value.filter(rowId => rowId !== id)
    : [...expandedRows.value, id];
};

// Pagination - Get pages
const emit = defineEmits(['fetch-page', 'update:selected']);

const fetchPage = ({ page = 1, itemsPerPage = 10, sortBy = [] }) => {
    emit('fetch-page', {
        page,
        itemsPerPage,
        sortBy,
    });
};

// Tracks sorting state
const sortedColumns = ref([]);

// Sort order toggling logic
const getNextSortOrder = (currentOrder) => {
    if (currentOrder === "asc") return "desc";
    if (currentOrder === "desc") return null;
    return "asc";
};

// Check if column is sorted
const isSorted = (key) => sortedColumns.value.some((sort) => sort.key === key);

// Get sorting icon
const getSortIcon = (key) => {
    const sort = sortedColumns.value.find((s) => s.key === key);
    return sort ? (sort.order === 'asc' ? 'sort-up' : 'sort-down') : "sort";
};

// Handle click (Single or Shift + Click)
const handleSort = (key, sortable, event) => {
    if (!sortable) return;
    const existingSort = sortedColumns.value.find((s) => s.key === key);
    const newOrder = getNextSortOrder(existingSort?.order);

    if (event.shiftKey) {
        // Shift + Click: Add multiple columns to sorting
        if (newOrder) {
        sortedColumns.value = [
            ...sortedColumns.value.filter((s) => s.key !== key),
            { key, order: newOrder },
        ];
        } else {
        sortedColumns.value = sortedColumns.value.filter((s) => s.key !== key);
        }
    } else {
        // Normal Click: Reset sorting to only this column
        sortedColumns.value = newOrder ? [{ key, order: newOrder }] : [];
    }

    emitSorting();
};

// Emit sorting changes
const emitSorting = () => {
    emit('fetch-page', { sortBy: sortedColumns.value });
};

// Clear sorting
const clearSorting = () => {
    sortedColumns.value = [];
    emitSorting();
};

watch(selectedItems, (val) => {
    emit('update:selected', val);
});
</script>

<template>
<section class="simple-data-table">
    <table class="simple-data-table__container">
        <thead class="simple-data-table__headers">
            <tr>
                <th v-if="selectable" class="header w-1">
                    <Checkbox
                        :checked="allSelected"
                        @change="toggleSelectAll" />
                </th>
                <th v-if="expandable" class="header w-1">
                </th>
                <th
                    v-for="header in headers"
                    :key="header.key"
                    scope="col"
                    class="header"
                    :class="[
                        header.width,
                        { 'cursor-pointer': header.sortable }
                    ]"
                    @click="handleSort(header.key, header.sortable, $event)">
                    <span v-if="header.title">
                        {{ header.title }}
                    </span>
                    <span v-if="!header.title && header?.icon">
                        <FontAwesomeIcon :icon="header.icon" size="sm" class="fa-fw" />
                    </span>
                    <span v-if="isSorted(header.key)">
                        <FontAwesomeIcon class="fa-fw" :icon="getSortIcon(header.key)" />
                    </span>
                </th>
            </tr>
        </thead>
        <tbody v-if="isReady && data">
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
                                <template v-if="header?.icon">
                                    <span v-if="getItemValue(header, item)">
                                        <FontAwesomeIcon :icon="header.icon" size="sm" class="fa-fw" />
                                    </span>
                                </template>
                                <span v-else>{{ getItemValue(header, item) }}</span>
                                <span v-if="isSorted(header.key)">
                                    <!-- <FontAwesomeIcon class="fa-fw" :icon="getSortIcon(header.key)" /> -->
                                </span>
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

    <p v-if="!data || data?.data?.length === 0" class="p-4 text-center w-full">
        Sorry, but there are no results found.
    </p>

    <SimpleLoadingSpinner v-if="!isReady || !data?.data" :size="'2xl'" />
    
    <SimpleDataPagination
        v-if="pagination"
        v-bind="$attrs"
        :pagination="pagination"
        @page-changed="fetchPage"
        @clear-sorting="clearSorting" />
</section>
</template>
    