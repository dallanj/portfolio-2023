<template>
<ModalContainer class="simple-modal__container" modal-size="sm" @close="$emit('close')">
    <template v-if="title" #header>{{ title }}</template>
    <template v-if="subtitle" #subtitle>{{ subtitle }}</template>

    <pre class="max-h-48 border border-gray-300 dark:border-app-header-bb p-4 rounded text-sm overflow-x-auto whitespace-pre-wrap bg-white dark:bg-sidebar-bg text-gray-900 dark:text-gray-100">{{ publicKey ?? 'Failed to load public key.' }}</pre>

    <template #footer>
        <div class="flex gap-x-4">
            <SimpleButton state="secondary" @click="$emit('close')">Cancel</SimpleButton>
            <SimpleButton
                :icon="copying ? 'spinner' : false"
                :icon-spin="copying"
                :is-disabled="!publicKey"
                @click="copyToClipboard">
                {{ copied ? 'Copied' : 'Copy to Clipboard' }}
            </SimpleButton>
        </div>
    </template>
</ModalContainer>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useContactsStore } from '@/stores/contacts';
import { storeToRefs } from 'pinia';

const { getPublicKey } = useContactsStore();
const { publicKey } = storeToRefs(useContactsStore());

const props = defineProps({
    title: {
        type: String,
        default: 'Confirmation',
    },
    subtitle: {
        type: String,
        required: false,
    },
    position: {
        type: String,
        default: 'justify-content: flex-end;'
    },
});

const copying = ref(false);
const copied = ref(false);

// Load the key on mount
onMounted(async () => {
    try {
        await getPublicKey();
    } catch (e) {
        $toast.error('Failed to load public key.');
    }
});

const copyToClipboard = async () => {
    try {
        copying.value = true;

        await navigator.clipboard.writeText(publicKey.value);
        copied.value = true;
        $toast.success('Public key copied to clipboard.');
    } catch (error) {
        $toast.error('Failed to copy public key.');
    } finally {
        copying.value = false;
        setTimeout(() => (copied.value = false), 2000);
    }
}
</script>
