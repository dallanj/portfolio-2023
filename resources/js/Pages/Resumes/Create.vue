<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed, watch, nextTick } from 'vue';
import { useResumesStore } from '@/stores/resumes';
import { Head } from '@inertiajs/vue3';
import { storeToRefs } from 'pinia';

const { create, update } = useResumesStore();
const { active } = storeToRefs(useResumesStore());

const form = ref({
    title: '',
    version: '',
    content: {
        html: '',
        delta: {},
    },
    is_draft: false,
});

watch(active, async (obj) => {
    await nextTick();
    console.log(obj)
    if (obj) {
        form.value.title = obj.title || '';
        form.value.version = obj.version || '';
        form.value.is_draft = obj.is_draft || false;
        form.value.content = {
            html: obj.html || '',
            delta: obj.delta || {},
        };
    }
}, {
    deep: true
});

// Determine if the tag is being created or updating by checking if they have an existing ID
const existingResume = computed(() => active.value?.hash);

const saving = ref(false);

const submit = async (draft = false) => {
    const action = existingResume.value ? update : create;
    const mode = existingResume.value ? 'updated' : 'created';

    try {
        saving.value = true;

        const payload = {
            title: form.value.title,
            html: form.value.content.html,
            delta: form.value.content.delta,
            is_draft: draft
        };

        if (existingResume.value && active.value?.hash) {
            payload.hash = active.value.hash;
        }

        await action(payload);

        $toast.success(`You have successfully ${mode} a resume.`);
    } catch (error) {
        $toast.error(`An unexpected error happened, the resume could not be ${mode}.`);
    } finally {
        saving.value = false;
    }
}
</script>

<template>
<Head title="Resumes - Create" />

<AuthenticatedLayout>
    <template #header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Create Resume
        </h2>
    </template>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 grid gap-4">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="p-4 grid gap-4">
                        <SimpleTextField
                            v-model="form.title"
                            name="title"
                            label="Resume Title *"
                            placeholder="Start your resume with title" />
                        <SimpleQuillEditor v-model="form.content" @update:modelValue="model => form.content = model" />
                    </div>
                </div>
            </div>

            <div class="flex gap-x-4">
                <SimpleButton
                    :icon="saving ? 'spinner' : false"
                    :icon-spin="saving"
                    @click="submit(false)">
                    {{ existingResume ? 'Update' : 'Create' }}
                </SimpleButton>
                <SimpleButton
                    state="secondary"
                    :icon="saving ? 'spinner' : false"
                    :icon-spin="saving"
                    @click="submit(true)">
                    {{ existingResume ? 'Update Draft' : 'Create Draft' }}
                </SimpleButton>
            </div>
        </div>
    </div>
</AuthenticatedLayout>
</template>
