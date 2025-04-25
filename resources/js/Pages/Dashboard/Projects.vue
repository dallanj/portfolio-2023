<script setup>
import { ref, onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import { capitalizeFirstLetter } from '@/utils/formatting';

import { useProjectsStore } from '@/stores/projects';
import { useTagsStore } from '@/stores/tags';

const isReady = ref(false);

// Setup stores
const projectsStore = useProjectsStore();
const tagsStore = useTagsStore();

// Destructure with aliases
const { all: allProjects, active } = storeToRefs(projectsStore);
const { all: allTags } = storeToRefs(tagsStore);

onMounted(async () => {
    await projectsStore.search({ paginate: false });
    await tagsStore.search({ paginate: false });
    isReady.value = true;
});

const handleProjectClick = (project) => {
    projectsStore.setActiveProject(project);
};

const handleBackClick = async () => {
    await projectsStore.search({ paginate: false });
    projectsStore.$reset('actives');
};

</script>

<template>
<div class="h-full overflow-y-scroll flex flex-col">
    <div class="flex flex-1 overflow-hidden">
    <!-- Sidebar -->
        <aside class="w-48 bg-sidebar-bg text-white flex flex-col overflow-y-scroll border-r border-r-sidebar-border">
            <!-- <div class="p-4 text-lg font-semibold border-b border-gray-700">Explorer</div> -->
            <nav class="flex-1 overflow-y-scroll">
                <ul v-if="isReady" class="space-y-2">
                    <li v-for="item in allTags" :key="item" class="hover:bg-sidebar-textbghover py-2 px-4 cursor-pointer text-sm">
                    {{ item.name }}
                    </li>
                </ul>
            </nav>
        </aside>

        <main v-if="isReady" class="flex-1 min-h-0 p-6 overflow-y-scroll">
            <transition name="fade" mode="out-in">
                <!-- Active Project View -->
                <div v-if="active" key="active" class="space-y-6">
                    <button @click="handleBackClick" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-600">
                        ← Back to Projects
                    </button>

                    <div class="text-white">
                        <h1 class="text-3xl font-bold">{{ capitalizeFirstLetter(active.title) }}</h1>
                        <p class="mt-4 text-lg">{{ capitalizeFirstLetter(active.overview) }}</p>
                        <!-- Add more details here -->
                    </div>
                </div>

                <ul v-else key="list" class="flex flex-wrap gap-8" id="projects">
                    <li
                        v-for="item in allProjects" 
                        :key="`project-${item.id}`"
                        class="flex-none basis-1/4 min-w-128 rounded shadows overflow-hidden h-[36rem] flex"
                        style="box-shadow: 0 8px 8px 2px rgba(0, 0, 0, 0.15);"
                        @click="handleProjectClick(item)">
                        <div class="group cursor-pointer flex flex-col gap-4 h-full w-full">
                            <figure class="overflow-hidden">
                                <img
                                    v-if="item.media[0].url"
                                    :src="item.media[0].url"
                                    :alt="`Preview of ${item.title}`"
                                    class="object-cover rounded-t opacity-75 aspect-video hover:scale-110 transition-transform size-full">
                                <img v-else
                                    src="/images/placeholder.png"
                                    alt="No preview available"
                                    class="w-full h-32 object-cover rounded" />
                            </figure>
                            <article class="p-2 space-y-3 flex-grow">
                                <hgroup class="space-y-3 font-bold">
                                    <h2 class="text-base text-gray-500 uppercase">personal project</h2>
                                    <p class="text-xl text-gray-300">{{ capitalizeFirstLetter(item.title) }}</p>
                                </hgroup>

                                <div>
                                    <ol class="flex flex-wrap gap-3">
                                        <li
                                            v-for="tag in item.tags"
                                            :key="`tag-${tag.id}`">
                                            <span class="py-1 px-2 rounded bg-sidebar-bg">{{ tag.name }}</span>
                                        </li>
                                    </ol>
                                </div>

                                <p class="text-base mt-2">{{ capitalizeFirstLetter(item.overview) }}</p>
                            </article>

                            <footer class="self-end p-3">
                                <SimpleButton state="secondary" @click="$emit('close')">Click to see more!</SimpleButton>
                            </footer>
                        </div>
                    </li>
                </ul>
            </transition>
        </main>
    </div>
</div>
</template>

<style scoped>
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.3);
  border-radius: 10px;
}
</style>