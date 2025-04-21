<template>
<div class="m-3">
    <div class="pdf-controls">
        <!-- TODO: Add buttons to export -->
    </div>
    
    <object v-if="resume" :data="resume" type="application/pdf" width="100%" height="800px">
        <p>Your browser does not support PDFs. Please download the PDF to view ist.</p>
    </object>
</div>
</template>
      
<script setup>
import { ref, onMounted, watch } from 'vue';
import { useResumesStore } from '@/stores/resumes';
import { storeToRefs } from 'pinia';

const isReady = ref(false);
// Use the Pinia store
const store = useResumesStore();
const { resume } = storeToRefs(useResumesStore());

onMounted(async () => {
    await store.show();
    isReady.value = true;
});
</script>
      
<style scoped>
.pdf-viewer-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.pdf-controls {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    justify-content: center;
}

.pdf-canvas {
    max-width: 100%;
    border: 1px solid #ccc;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
}
</style>
      