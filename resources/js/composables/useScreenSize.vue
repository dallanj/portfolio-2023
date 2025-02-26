<script>
import { ref, toRefs, reactive, computed, onMounted, onUnmounted } from 'vue';

const screenState = reactive({
    isMobile: null,
    isTablet: null,
    isDesktop: null,
    screenWidth: null,
    screenHeight: null,
});
export function useScreenSize() {
    const width = ref(window.innerWidth);
    const height = ref(window.innerHeight);

    const onResize = () => {
        // Window dimensions
        width.value = window.innerWidth;
        height.value = window.innerHeight;
        // Screen sizes
        screenState.screenWidth = width.value;
        screenState.screenHeight = width.value;
        screenState.isMobile = width.value <= 475;
        screenState.isTablet = width.value >= 475 && width.value < 1024;
        screenState.isDesktop = width.value >= 1024;
    };

    const checkIfMobile = computed(() => width.value < 475);
    const checkIfTablet = computed(() => width.value >= 475 && width.value < 1024);
    const checkIfDesktop = computed(() => width.value >= 1024);

    onMounted(() => {
        window.addEventListener('resize', onResize);
        onResize();
    });

    onUnmounted(() => {
        window.removeEventListener('resize', onResize);
    });

    return toRefs(screenState);
}
</script>
