<script>
export function usePagination(emit) {
    const fetchPage = ({ page = 1, itemsPerPage = 10, sortBy = null }) => {
        emit('fetch-page', {
            page,
            itemsPerPage,
            sortBy: sortBy ?? sortedColumns.value,
        });
    };

    return {
        fetchPage,
    };
}
</script>
