<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ projects: Object });
</script>

<template>
    <Head title="Projects" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Projects
            </h2>
            <Link :href="route('projects.create')">Create</Link>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-col gap-2">
                        <div
                            v-for="project in projects.data"
                            :key="project.id"
                            class="">
                            <h2>{{ project.title }}</h2>
                            <p>{{ project.description }}</p>
                            <img v-if="project.media.length" :src="project.media[0].path" class="w-20 h-20"/>
                            <div>
                                <Link :href="`/projects/${project.id}`">View</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
