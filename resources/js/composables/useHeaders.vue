<script>
import { computed } from 'vue';

export function useHeaders(props) {
    const getItemValue = (header, item) => {
        return header.value ? header.value(item) : item[header.key];
    };

    const getListItemValue = (listItem, item) => {
        return listItem.value ? listItem.value(item) : item[listItem.key];
    };

    const hasActions = computed(() => props.actions.length > 0);

    const headers = computed(() => {
        const headers = props.headers.map(header => {
            return { ...header };
        });

        if (hasActions.value) {
            headers.push({
                key: 'actions',
                title: 'Actions',
                type: 'action',
                sortable: false,
                width: 'w-20',
            });
        }

        return headers;
    });

    return {
        getItemValue,
        getListItemValue,
        hasActions,
        headers,
    };
}
</script>
