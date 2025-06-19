<script setup>
import { ref, inject } from 'vue';
import { useActivitiesStore } from '@/stores/activities';
import { useContactsStore } from '@/stores/contacts';
import { storeToRefs } from 'pinia';

// Setup stores
const activities = useActivitiesStore();
const contact = useContactsStore();

// Destructure with aliases
const { getActiveWindow } = storeToRefs(activities);

const { openModal } = inject('modals');

const form = ref({
    name: '',
    email: '',
    message: '',
    publicKey: '',
});

const showPublicKey = ref(false);
const submitting = ref(false);

const togglePublicKey = () => {
    showPublicKey.value = !showPublicKey.value;
};

const submit = () => {
    submitting.value = true;
    contact.create(form.value);
    // Simulate submission
    setTimeout(() => {
        submitting.value = false;
    }, 1000);
}

const publicKeyModal = () => {
    openModal('PublicKeyModal', {
        title: 'My PGP Public Key',
        subtitle: `This key may be subject to change in the future.`,
        position: 'justify-content: safe center; align-items: center;',
    });
}
</script>

<template>
<div class="h-full overflow-y-scroll flex flex-col">
    <div class="flex flex-1 overflow-y-scroll">
        <section class="px-4 py-2 max-w-xl mx-auto space-y-6">
            <hgroup class="space-y-1">
                <h2 class="text-2xl font-semibold">Book an online meeting</h2>
                <p class="text-gray-700 dark:text-gray-300">
                    Let's discuss your business or ideas.
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    This email will be encrypted using PGP (Pretty Good Privacy).
                    You can find my
                    <button
                        type="button"
                        @click="publicKeyModal"
                        aria-label="click to view public key here"
                        class="underline text-orange dark:text-orange hover:text-orange/[.75]">
                        public key here
                    </button>. Feel free to add your public key for encrypted responses.
                </p>
            </hgroup>

            <form @submit.prevent="submit" class="space-y-4">
                <div 
                    class="grid"
                    :class="getActiveWindow.width > 600 ? 'grid-cols-2 gap-x-4' : 'gap-y-4'">
                    <SimpleTextField
                        v-model="form.name"
                        name="name"
                        label="Name"
                        placeholder="Your full name" />

                    <SimpleTextField
                        v-model="form.email"
                        name="email"
                        label="Email"
                        type="email"
                        placeholder="you@example.com" />
                </div>

                <SimpleTextArea
                    v-model="form.message"
                    name="message"
                    label="Message"
                    placeholder="What would you like to talk about?" />

                <div>
                    <button
                        type="button"
                        @click="togglePublicKey"
                        :aria-expanded="showPublicKey.toString()"
                        aria-controls="publicKeyInput"
                        class="text-sm underline text-orange dark:text-orange hover:text-orange/[.75]">
                            {{ showPublicKey ? 'Omit Public Key' : 'Include Public Key' }}
                    </button>

                    <div v-if="showPublicKey" id="publicKeyInput" class="mt-2">
                        <SimpleTextArea
                            v-model="form.message"
                            id="publicKey"
                            name="publicKeys"
                            label="Your Public PGP Key"
                            :rows="2"
                            placeholder="Paste your ASCII-armored public key here..." />
                    </div>
                </div>

                <SimpleButton
                    :icon="submitting ? 'spinner' : false"
                    :icon-spin="submitting"
                    :is-disabled="submitting"
                    @click="copyToClipboard">
                    {{ submitting ? 'Sending...' : 'Send Message' }}
                </SimpleButton>
            </form>
        </section>
    </div>
</div>
</template>
