<script>
export function usePagination(emit) {
    const fetchPage = (page) => {
        emit('fetch-page', page);
    };

    return {
        fetchPage,
    };
}
</script>
