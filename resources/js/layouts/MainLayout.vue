<template>
    <div
        id="mainLayout"
        :class="{
            'grid-template-layout-1': layoutType === 'type1',
            'grid-template-layout-2': layoutType === 'type2',
        }">
        <TopBar ref="top-bar" />

        <Dock />
        <main><slot /></main>
    </div>
</template>

<script setup>
import TopBar from '@/components/TopBar.vue';
import Dock from '@/components/Dock.vue';
import { computed } from 'vue';

const layoutType = 'type1';
</script>

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
  @apply grid grid-cols-3 w-full bg-topbar-grey py-1 drop-shadow-md relative z-50;
  grid-area: head;
}

#mainLayout > nav {
  @apply relative z-50;
  height: 100dvh;
  grid-area: nav;
}

#mainLayout > main {
  @apply object-cover bg-cover bg-center bg-no-repeat;
  grid-area: main;
  background-image: url('../images/wallpaper.jpg');
}
</style>