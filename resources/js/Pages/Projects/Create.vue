<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UploadMediaForm from './Partials/UploadMediaForm.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeMount, watch, nextTick, computed } from 'vue';
import { useForm } from 'laravel-precognition-vue-inertia';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { useToast } from 'vue-toastification';
import { useProjectsStore } from '@/stores/projects';
import { useTagsStore } from '@/stores/tags';
import { storeToRefs } from 'pinia';
import axios from 'axios';

// Setup stores
const projectsStore = useProjectsStore();
const tagsStore = useTagsStore();

const { create, update } = projectsStore;

// Destructure with aliases
const { active } = storeToRefs(projectsStore);
const { all: allTags } = storeToRefs(tagsStore);

const isReady = ref(false);

onMounted(async () => {
    await tagsStore.search({ paginate: false });
    isReady.value = true;
});

const toast = useToast();

const project = ref({
    title: '',
    overview: '',
    description: '',
    media: [],
    tags: [],
});

onBeforeMount(async () => {
    if (!window.location.pathname.endsWith('/edit')) {
        try {
            const response = await axios.post('/api/v1/projects');
        
            return response;
        } catch (error) {
            throw error;
        }
    }
});

watch(active, async (obj) => {
    await nextTick();
    if (obj) {
        project.value = obj;

        // If tags are full objects, extract their ids
        selectedTags.value = obj.tags?.map(tag => tag.id) ?? [];
    }
}, {
    deep: true
});

const editing = computed(() => !!active?.value?.hash);

watch(editing, async (val) => {
    await nextTick();
    if (val) {
        router.visit(`/projects/${active.value?.hash}/edit`);
    }
});


const title = ref(null);
const overview = ref(null);
const description = ref(null);

const uploadMediaFromRef = ref(null);

const selectedTags = ref([]);
const setTags = (tags) => {
    selectedTags.value = tags;
  // do something with tags...
};

const save = async () => {
    try {
        const action = editing.value ? update : create;
        const toastMessage = editing.value ? 'Project Updated' : 'Project Created';

        const response = await action({
            ...project.value,
            tags: selectedTags.value
        });

        // Now that project is created, trigger media upload
        if (uploadMediaFromRef.value) {
            uploadMediaFromRef.value.uploadMedia();
        }
    } catch (error) {
        toast.error('An unexpected error occurred');
    }
};
</script>

<template>
    <Head title="Projects" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Projects > {{ editing ? 'Edit' : 'Create' }}
            </h2>
            <button class="cursor-pointer" @click="$toast.error('hello')">click me</button>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-col gap-2">
                        <form v-if="isReady" @submit.prevent="save" class="mt-6 space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <UploadMediaForm
                                    v-model="project.media"
                                    ref="uploadMediaFromRef"
                                    class="max-w-xl" />

                                <div class="flex flex-col gap-3">
                                    <SimpleDropdown
                                        v-model="selectedTags"
                                        :options="allTags"
                                        label-key="name"
                                        value-key="id"
                                        label="Assign a tag to the project"
                                        multiple
                                        placeholder="Assign a tag"
                                        @update:modelValue="setTags(selectedTags)" />
                                    <div>
                                        <SimpleInputLabel for="title" value="Title" />

                                        <TextInput
                                            id="title"
                                            ref="title"
                                            v-model="project.title"
                                            type="text"
                                            class="mt-1 block w-full"
                                            autocomplete="title" />
                                    </div>

                                    <div>
                                        <SimpleInputLabel for="overview" value="Overview" />

                                        <TextInput
                                            id="overview"
                                            ref="overview"
                                            v-model="project.overview"
                                            type="text"
                                            class="mt-1 block w-full"
                                            autocomplete="overview" />
                                    </div>
                                    
                                    <div>
                                        <SimpleInputLabel for="description" value="Description" />

                                        <TextInput
                                            id="description"
                                            ref="description"
                                            v-model="project.description"
                                            type="text"
                                            class="mt-1 block w-full"
                                            autocomplete="description" />
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 justify-center">
                                <SimpleButton                     
                                    @click.prevent="save">
                                    {{ editing ? 'Update' : 'Create' }}
                                </SimpleButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
