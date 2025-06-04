import { ref } from 'vue';

/**
 * Handles paginated delete logic (safe fallback to previous page if last page is emptied).
 */
export function usePaginatedDelete({ storeRef, searchParams, search }) {
    const isLoading = ref(false);

    const safeRefresh = async () => {
        const meta = storeRef?.value?.meta;
        const data = storeRef?.value?.data;

        if (!Array.isArray(data) || !meta) return;

        const isLastItemOnPage = data.length === 1;
        const isNotFirstPage = meta.current_page > 1;

        if (isLastItemOnPage && isNotFirstPage) {
            searchParams.value.page = meta.current_page - 1;
        }

        await search(); // Always refresh
    };

    const destroy = async (hash) => {
        isLoading.value = true;
        try {
            await axios.delete(`/api/v1/resumes/${hash}`);
            await safeRefresh();
        } finally {
            isLoading.value = false;
        }
    };

    const bulkDelete = async (payload) => {
        isLoading.value = true;
        try {
            await axios.post(`/api/v1/resumes/bulk-delete`, payload);
            await safeRefresh();
        } finally {
            isLoading.value = false;
        }
    };

    return {
        destroy,
        bulkDelete,
        isLoading,
    };
}
