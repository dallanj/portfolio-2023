<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
const project = ref({
    title: '',
    overview: '',
    description: '',
});

watch(active, async (obj) => {
    await nextTick();
    if (obj) {
        form.setData(obj);
    }
}, {
    deep: true
});

const editing = computed(() => !!active?.value?.hash);

const urlroute = computed(() => editing.value
    ? `projects/${active?.value?.hash}`
    : 'projects');

const requestType = computed(() => editing.value
    ? 'patch'
    : 'post');

const form = useForm(requestType.value, `/api/v1/${urlroute.value}`, project.value);

form.setValidationTimeout(3000);
const title = ref(null);
const overview = ref(null);
const description = ref(null);

const submit = () => {
    form[requestType.value](`/api/v1/${urlroute.value}`, {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.title) {
                form.reset('title');
                title.value.focus();
            }
            if (form.errors.overview) {
                form.reset('overview');
                overview.value.focus();
            }
            if (form.errors.description) {
                form.reset('description');
                description.value.focus();
            }
        },
    });
};
</script>

<template>
    <Head title="Projects" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Projects > New Project
            </h2>
            <button class="cursor-pointer" @click="$toast.error('hello')">click me</button>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-col gap-2">
                        <form @submit.prevent="submit" class="mt-6 space-y-6">
                            <div>
                                <SimpleInputLabel for="title" value="Title" />

                                <TextInput
                                    id="title"
                                    ref="title"
                                    v-model="form.title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    autocomplete="title"
                                    @change="form.validate('title')"
                                    @focus="form.forgetError('title')" />
                                <InputError v-if="form.invalid('title')" :message="form.errors.title" class="mt-2" />
                            </div>

                            <div>
                                <SimpleInputLabel for="overview" value="Overview" />

                                <TextInput
                                    id="overview"
                                    ref="overview"
                                    v-model="form.overview"
                                    type="text"
                                    class="mt-1 block w-full"
                                    autocomplete="overview"
                                    @change="form.validate('overview')"
                                    @focus="form.forgetError('overview')" />
                                <InputError v-if="form.invalid('overview')" :message="form.errors.overview" class="mt-2" />
                            </div>

                            <div>
                                <SimpleInputLabel for="description" value="Description" />

                                <TextInput
                                    id="description"
                                    ref="description"
                                    v-model="form.description"
                                    type="text"
                                    class="mt-1 block w-full"
                                    autocomplete="description"
                                    @change="form.validate('description')"
                                    @focus="form.forgetError('description')" />
                                <InputError v-if="form.invalid('description')" :message="form.errors.description" class="mt-2" />
                            </div>

                            <div class="flex items-center gap-4">
                                <SimpleButton
                                    :disabled="form.processing || form.hasErrors"
                                    @click="form.touch(['title','overview','description']).validate({
                                        onValidationError: () => $toast.error('There was an error creating this project.'),
                                        onSuccess: () => $toast.success('This is a success message!'),
                                    })">
                                        Save
                                    </SimpleButton>

                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0">
                                    <p v-if="form.hasErrors" class="text-sm text-gray-600">Please fix the errors beforing saving.</p>
                                </Transition>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
