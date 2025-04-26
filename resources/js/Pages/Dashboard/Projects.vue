<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { capitalizeFirstLetter } from '@/utils/formatting';
import { useProjectsStore } from '@/stores/projects';
import { useTagsStore } from '@/stores/tags';

const isReady = ref(false);

// Setup stores
const projectsStore = useProjectsStore();
const tagsStore = useTagsStore();

// Destructure with aliases
const { all: allProjects, active, history: projectHistory, future: projectFuture } = storeToRefs(projectsStore);
const { all: allTags, active: tagActive, history: tagHistory, future: tagFuture } = storeToRefs(tagsStore);

onMounted(async () => {
    await projectsStore.search({ paginate: false });
    await tagsStore.search({ paginate: false });
    isReady.value = true;
});

const handleProjectClick = (project) => {
    projectsStore.setActive(project);
};

// Watch for changes to activeTag/project and record history
const handleBackClick = () => {
    // If we're viewing a project, go back to the tag
    if (active.value) {
        projectFuture.value.push(active.value);
        active.value = null;
        return;
    }

    // If a tag is selected, go back to no tag
    if (selectedTag.value) {
        tagFuture.value.push(tagActive.value);
        tagsStore.setActive(null, true);
        selectedTag.value = null;
        return;
    }
};

const handleForwardClick = () => {
    // Restore tag if it's in the future
    if (tagFuture.value.length) {
        const nextTag = tagFuture.value.pop();
        tagHistory.value.push(tagActive.value);
        tagsStore.setActive(nextTag, true);
        selectedTag.value = nextTag.name;
        return;
    }

    // Restore project if it's in the future
    if (projectFuture.value.length) {
        const nextProject = projectFuture.value.pop();
        projectHistory.value.push(active.value);
        active.value = nextProject;
        return;
    }
};

defineExpose({ handleBackClick, handleForwardClick });

const selectedTag = ref(null);

const filteredProjects = computed(() => {
    if (!selectedTag.value) return allProjects.value;

    return allProjects.value.filter(project =>
        project.tags.some(tag => tag.name === selectedTag.value)
    );
});

const handleTagClick = (tag) => {
    if (active.value) {
        projectHistory.value.push(active.value);
        projectFuture.value = [];
        active.value = null;
    }

    if (selectedTag.value) {
        tagHistory.value.push(tagActive.value);
    }

    tagFuture.value = [];
    tagsStore.setActive(tag);
    selectedTag.value = tag.name;
};

const resetActiveTag = () => {
    if (selectedTag.value) {
        tagHistory.value.push(tagActive.value);
        tagFuture.value = [];
    }

    selectedTag.value = null;
    tagsStore.setActive(null);

    if (active.value) {
        projectHistory.value.push(active.value);
        projectFuture.value = [];
        active.value = null;
    }
};

watch(() => tagActive.value, (newTag) => {
    selectedTag.value = newTag?.name || null;
});
</script>

<template>
<div class="h-full overflow-y-scroll flex flex-col">
    <div class="flex flex-1 overflow-hidden">
    <!-- Sidebar -->
        <aside class="w-48 bg-sidebar-bg text-white flex flex-col overflow-y-scroll border-r border-r-sidebar-border">
            <!-- <div class="p-4 text-lg font-semibold border-b border-gray-700">Explorer</div> -->
            <nav class="flex-1 overflow-y-scroll">
                <ul v-if="isReady" class="space-y-0.5">
                    <li
                        @click="resetActiveTag"
                        class="py-2 px-4 cursor-pointer text-sm hover:bg-sidebar-textbghover font-semibold text-gray-300">
                        All Projects
                    </li>
                    <li
                        v-for="item in allTags"
                        :key="item.id"
                        
                        @click="handleTagClick(item)"
                        :class="[
                            'py-2 px-4 cursor-pointer text-sm',
                            selectedTag === item.name ? 'bg-sidebar-selected-tag font-bold' : 'hover:bg-sidebar-textbghover'
                        ]">
                        {{ item.name }}
                    </li>
                </ul>
            </nav>
        </aside>

        <main v-if="isReady" class="flex-1 min-h-0 p-6 overflow-y-scroll">
            <transition name="fade" mode="out-in">
                <!-- Active Project View -->
                <div v-if="active" key="active" class="space-y-6">
                    <div class="text-white">
                        <h1 class="text-3xl font-bold">{{ capitalizeFirstLetter(active.title) }}</h1>
                        <p class="mt-4 text-lg">{{ capitalizeFirstLetter(active.overview) }}</p>
                        <!-- Add more details here -->
                    </div>
                </div>

                <ul v-else key="list" class="flex flex-wrap gap-8" id="projects">
                    <li
                        v-for="item in filteredProjects" 
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
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>