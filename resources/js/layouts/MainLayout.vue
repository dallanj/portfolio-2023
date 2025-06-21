<script setup>
import { computed, inject } from 'vue';
import TopBar from '@/components/TopBar.vue';
import Dock from '@/components/Dock.vue';
import { useSettingsStore } from '@/stores/settings';
import { storeToRefs } from 'pinia';
import { useComponentValidator } from '@/composables/useComponentValidator';

const { isValidComponent } = useComponentValidator();
const { activeModal } = inject('modals');

const {
    boundaries,
    dockPosition
} = storeToRefs(useSettingsStore());

// Validate that the modal has been registered
const isModalValid = computed(() => {
    return isValidComponent(activeModal.value);
});
</script>

<template>
    <div
        id="mainLayout"
        class="overflow-hidden wallpaper"
        :class="{
            'grid-template-layout-1': dockPosition === 'left',
            'grid-template-layout-2': dockPosition === 'bottom',
        }">
        <TopBar id="top-bar" ref="top-bar" />

        <Dock />
        <main><slot /></main>
        <ModalBase v-if="isModalValid" />
    </div>
</template>

<style scoped lang="scss">
#mainLayout {
  @apply grid min-h-screen h-screen;
  display: grid;
  width: 100%;
}

.grid-template-layout-1 {
  grid-template-areas:
    "head head"
    "nav  main";
  grid-template-rows: 32px 1fr;
  grid-template-columns: 80px 1fr;
}

.grid-template-layout-2 {
  grid-template-areas:
    "head"
    "main"
    "nav";
  grid-template-rows: 32px 1fr 80px;
}

#mainLayout > header {
  @apply grid justify-between grid-cols-2 lg:grid-cols-3 w-full bg-topbar-grey py-1 drop-shadow-md relative z-50;
  grid-area: head;
}

#mainLayout > nav {
  @apply relative z-50;
  grid-area: nav;
  height: 100%; // override the 100dvh that’s pushing things off screen
}

#mainLayout > main {
  grid-area: main;
}
</style>