<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UploadMediaForm from './Partials/UploadMediaForm.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onBeforeMount, watch, nextTick, computed } from 'vue';
import { useForm } from 'laravel-precognition-vue-inertia';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { useToast } from 'vue-toastification';
import { useProjectsStore } from '@/stores/projects';
import { storeToRefs } from 'pinia';
import axios from 'axios';

const projectStore = useProjectsStore();
const { create, update } = projectStore;

const toast = useToast();
const { active } = storeToRefs(useProjectsStore());
const project = ref({
    title: '',
    overview: '',
    description: '',
    media: [],
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

const save = async () => {
    try {
        const action = editing.value ? update : create;
        const toastMessage = editing.value ? 'Project Updated' : 'Project Created';

        const response = await action({
            ...project.value,
        });

        // Now that project is created, trigger media upload
        if (uploadMediaFromRef.value) {
            uploadMediaFromRef.value.uploadMedia();
        }
    } catch (error) {
        toast.error('An unexpected error occurred');
    }
};

const uploadMediaFromRef = ref(null);
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
                        <form @submit.prevent="save" class="mt-6 space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <UploadMediaForm
                                    v-model="project.media"
                                    ref="uploadMediaFromRef"
                                    class="max-w-xl" />

                                <div class="flex flex-col gap-3">
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
