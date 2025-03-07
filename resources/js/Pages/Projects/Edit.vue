<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UploadMediaForm from './Partials/UploadMediaForm.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, watch, nextTick, computed } from 'vue';
import { useForm } from 'laravel-precognition-vue-inertia';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { useToast } from 'vue-toastification';
import { useProjectsStore } from '@/stores/projects';
import { storeToRefs } from 'pinia';
const toast = useToast();
const { active } = storeToRefs(useProjectsStore());
const { update } = useProjectsStore();
const project = ref({
    title: '',
    overview: '',
    description: '',
});

watch(active, async (obj) => {
    await nextTick();
    if (obj) {
        form.value = obj;
    }
}, {
    deep: true
});

const title = ref(null);
const overview = ref(null);
const description = ref(null);

const form = ref({ ...project.value });
const saving = ref(false);

const submit = async () => {
    try {
        saving.value = true;

        await update({...form.value});

        $toast.success(`You have successfully updated a project.`);
        // emit('close');
    } catch (error) {
        $toast.error(`An unexpected error happened, the project could not be updated.`);
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <Head title="Projects" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Projects > Edit Project
            </h2>
            <button class="cursor-pointer" @click="$toast.error('hello')">edit me</button>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-col gap-2">
                        <form @submit.prevent="submit" class="mt-6 space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <UploadMediaForm
                                    v-model="form.media"
                                    class="max-w-xl" />

                                <div class="flex flex-col gap-3">
                                    <div>
                                        <SimpleInputLabel for="title" value="Title" />

                                        <TextInput
                                            id="title"
                                            ref="title"
                                            v-model="form.title"
                                            type="text"
                                            class="mt-1 block w-full"
                                            autocomplete="title" />
                                    </div>

                                    <div>
                                        <SimpleInputLabel for="overview" value="Overview" />

                                        <TextInput
                                            id="overview"
                                            ref="overview"
                                            v-model="form.overview"
                                            type="text"
                                            class="mt-1 block w-full"
                                            autocomplete="overview" />
                                    </div>

                                    <div>
                                        <SimpleInputLabel for="description" value="Description" />

                                        <TextInput
                                            id="description"
                                            ref="description"
                                            v-model="form.description"
                                            type="text"
                                            class="mt-1 block w-full"
                                            autocomplete="description" />
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <SimpleButton
                                    :disabled="saving"
                                    @click="submit">
                                    Save
                                </SimpleButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
