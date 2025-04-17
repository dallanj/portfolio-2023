<template>
<div class="h-full overflow-y-scroll flex flex-col">
    <div class="flex flex-1 overflow-hidden m-3">
    <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white flex flex-col overflow-y-scroll">
            <div class="p-4 text-lg font-semibold border-b border-gray-700">Explorer</div>
            <nav class="flex-1 overflow-y-scroll p-4">
                <ul class="space-y-2">
                    <li v-for="item in sidebarItems" :key="item" class="hover:bg-gray-700 p-2 rounded cursor-pointer">
                    {{ item }}
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-h-0 bg-gray-800 p-6 overflow-y-scroll">
            <ul class="flex flex-wrap gap-4" id="projects">
                <li v-for="item in all?.data" :key="item.id" class="w-64 flex-shrink-0">
                    <div class="rounded shadow p-2 group cursor-pointer">
                    <div class="relative">
                        <img
                            v-if="item.media[0].url"
                            :src="item.media[0].url"
                            :alt="`Preview of ${item.title}`"
                            class="w-full h-40 object-cover rounded opacity-75"
                            style="box-shadow: 8px 8px 0 rgba(0, 0, 0, 0.15);">
                        <img v-else
                            src="/images/placeholder.png"
                            alt="No preview available"
                            class="w-full h-32 object-cover rounded"
                            />
                        <div class="absolute inset-0 bg-black bg-opacity-60 opacity-0 group-hover:opacity-100 transition-opacity text-white p-2 overflow-y-auto text-xs">
                            <ul class="flex flex-wrap gap-1">
                                <li v-for="tag in item.tags" :key="tag" class="bg-gray-700 px-2 py-1 rounded">{{ tag }}</li>
                            </ul>
                            <div class="mt-2 underline">more..</div>
                        </div>
                        <p class="text-lg text-center text-gray-300 p-2">
                            {{ item.title }}
                        </p>
                    </div>
                    <p class="text-sm text-center mt-2 truncate">{{ item.name }}</p>
                    </div>
                </li>
            </ul>
        </main>
    </div>
</div>
</template>
  
<script setup>
import { ref, onMounted, onBeforeMount } from 'vue';
import { useProjectsStore } from '@/stores/projects';
import { storeToRefs } from 'pinia';
const isReady = ref(false);
// Use the Pinia store
const store = useProjectsStore();
const { all } = storeToRefs(useProjectsStore());
onMounted(async () => {
    await store.search();
    isReady.value = true;
});
const sidebarItems = [
  'Home',
  'Documents',
  'Downloads',
  'Music',
  'Pictures',
  'Videos'
];

</script>
  
<style scoped>
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.3);
  border-radius: 10px;
}
</style>
  