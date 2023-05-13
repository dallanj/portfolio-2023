<template>
    <div>
        <TopBar ref="top-bar" />

        <div class="flex">
            <Dock />
            <slot />
        </div>
    </div>
</template>

<script>
import TopBar from '@/components/TopBar.vue';
import Dock from '@/components/Dock.vue';
import { defineComponent } from 'vue';

export default defineComponent({
    components: {
        TopBar,
        Dock
    },
});
</script>
