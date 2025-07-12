<template>
<ul class="absolute right-2 flex justify-end space-x-1">
    <li
        v-for="action in actions"
        :key="`application-${action.value}-action`"
        class="cursor-pointer rounded-full"
        :class="{ 'hover:bg-app-header-actions-icon': action.value !== 'close' }"
        @click="action.event && action.event()"
        @click.prevent>
        <img
            :src="`/images/icons/actions/${action.icon}`"
            :alt="`${action.label} ${model.data.label} Application`">
    </li>
</ul>
</template>

<script setup>
import { useActivitiesStore } from '@/stores/activities';
import { computed, inject, watch, ref, onMounted } from 'vue';

const model = defineModel({
    type: Object,
    required: true,
});

const { isMobile } = inject('screenSize');

const {
    removeActivity,
    maximizeWindow,
    unMaximizeWindow,
    minimizeWindow,
} = useActivitiesStore();

const actions = computed(() => {
    return [
        {
            label: 'Minimize',
            value: 'minimize',
            icon: 'minimize_new.png',
            event: () => minimizeWindow(model)
        },
        // Show Maximize only if NOT maximized
        ...(!model.value.maximized || model.value.halfScreen ? [{
            label: 'Maximize',
            value: 'maximize',
            icon: 'maximize_new.png',
            event: () => maximizeWindow(model)
        }] : []),
        // Show Unmaximize only if IS maximized
        ...(model.value.maximized && !model.value.halfScreen ? [{
            label: 'Un-maximize',
            value: 'unmaximize',
            icon: 'restore_down_new.png',
            event: () => unMaximizeWindow(model)
        }] : []),
        {
            label: 'Close',
            value: 'close',
            icon: 'close_new.png',
            event: () => removeActivity(model),
        },
    ];
});

onMounted(() => {
	if (isMobile.value) {
		if (!alreadyMaximizedForMobile.value && !model.value.maximized) {
            maximizeWindow(model);
            alreadyMaximizedForMobile.value = true;
        }
	} else {
        alreadyMaximizedForMobile.value = false;
    }
});

// Track if this app has already been auto-maximized for mobile (prevent repeat)
const alreadyMaximizedForMobile = ref(false);
watch(isMobile, (newVal) => {
    console.log('watcjer for mobile,', model.value, newVal)
    if (newVal && !alreadyMaximizedForMobile.value && !model.value.maximized) {
        maximizeWindow(model);
        alreadyMaximizedForMobile.value = true;
    }

    // Optional: If switching back to desktop, you could reset the flag
    if (!newVal) {
        alreadyMaximizedForMobile.value = false;
    }
});
</script>

<style scoped lang="scss">
li img {
    transition: filter 0.2s ease;
}

li:hover img {
    filter: brightness(1.2);
}
</style>
