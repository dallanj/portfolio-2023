<script setup>
import { inject, computed } from 'vue';

// Composables
const { isMobile, isTablet } = inject('screenSize');

const props = defineProps({
    headers: {
        type: Array,
        required: true
    },
    data: {
        type: Array,
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
    pagination: {
        type: Object,
        default: null
    },
    actionPositionX: {
        type: String,
        default: 'right',
        validator: value => ['left', 'middle', 'right'].includes(value)
    },
    actionPositionY: {
        type: String,
        default: 'middle',
        validator: value => ['top', 'middle', 'bottom'].includes(value)
    }
});

import { useHeaders } from '@/composables/useHeaders.vue';
const { getItemValue, getListItemValue, hasActions, headers } = useHeaders(props);

// Pagination - Get pages
import { usePagination } from '@/composables/usePagination.vue';
const emit = defineEmits(['fetch-page']);
const { fetchPage } = usePagination(emit);

const actionClassesX = computed(() => {
    return {
        'left': 'left-3',
        'middle': 'left-1/2 transform -translate-x-1/2',
        'right': 'right-3',
    }[props.actionPositionX];
});

const actionClassesY = computed(() => {
    return {
        'top': 'top-2',
        'middle': 'top-1/2 tranform -translate-y-1/2',
        'bottom': 'bottom-2',
    }[props.actionPositionY];
});
</script>

<template>
<section>
    <ul class="simple-data-card">
        <li
            v-for="item in data" :key="item.id"
            class="simple-data-card__container relative grid grid-cols-[auto,1fr,40px] items-center text-brand-white px-2">
            <div
                v-for="header in headers"
                :key="header.key"
                class="card-item">
                <slot :name="header.key" :item="item">
                    <div
                        v-if="header.type === 'title'"
                        class="grid px-1 pr-3">
                        <p
                            v-tooltip="`${header.truncate ? getItemValue(header, item) : ''}`"
                            class="font-bold"
                            :class="{ 'truncate': header.truncate }">
                            {{ getItemValue(header, item) }}
                        </p>
                        <ul v-if="header.list?.length > 0">
                            <li v-for="listItem in header.list" :key="listItem.key">
                                {{ listItem.label }}: {{ getListItemValue(listItem, item) }}
                            </li>
                        </ul>
                    </div>

                    <button
                        v-if="hasActions && header.type === 'action'"
                        class="absolute px-2 h-7 w-7 rounded-full bg-blue-200"
                        :class="[actionClassesX, actionClassesY]">
                        <FontAwesomeIcon :icon="'plus'" class="mr-1.5 text-white" />
                    </button>
                </slot>
            </div>
        </li>
    </ul>
    <SimpleDataPagination
        v-if="pagination"
        :pagination="pagination"
        @page-changed="fetchPage" />
</section>
</template>
