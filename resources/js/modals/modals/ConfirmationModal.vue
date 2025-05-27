<script setup>
const props = defineProps({
    title: {
        type: String,
        default: 'Confirmation',
    },
    content: {
        type: String,
        default: 'Are you sure you would like to proceed with this action?'
    },
    subtitle: {
        type: String,
        required: false,
    },
    hideCancel: {
        type: Boolean,
        default: false,
    },
    position: {
        type: String,
        default: 'justify-content: flex-end;'
    },
});
</script>

<template>
<ModalContainer class="simple-modal__container" modal-size="sm" @close="$emit('close')">
    <template #header>{{ title }}</template>
    <template v-if="subtitle" #subtitle>{{ subtitle }}</template>
    <template #footer>
        <div class="flex gap-x-4">
            <SimpleButton v-if="!hideCancel" state="secondary" @click="$emit('close', false)">Cancel</SimpleButton>
            <SimpleButton @click="$emit('close', true)">Confirm</SimpleButton>
        </div>
    </template>
</ModalContainer>
</template>
    