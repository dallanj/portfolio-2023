<template>
<ModalContainer class="simple-modal__container" modal-size="sm" @close="$emit('close')">
    <template v-if="title" #header>{{ title }}</template>
    <template v-if="subtitle" #subtitle>{{ subtitle }}</template>

    <form @submit.prevent="submit">
        <div class="shadow p-6 rounded-lg space-y-4">
            <div class="grid items-center gap-1 sm:gap-4">
                <SimpleTextField
                    v-model="form.name"
                    name="name"
                    label="Tag Name *"
                    placeholder="Name of tag"
                    side-label
                    class="col-span-4" />
            </div>

            <SimpleRoundSwitch
                v-model="form.is_active"
                label="Is Activated"
                :classes="'grid grid-cols-1 sm:grid-cols-4 items-center gap-1 sm:gap-4'" />
        </div>
    </form>

    <template #footer>
        <div class="flex gap-x-4">
            <SimpleButton state="secondary" @click="$emit('close')">Cancel</SimpleButton>
            <SimpleButton
                :icon="saving ? 'spinner' : false"
                :icon-spin="saving"
                @click="submit">
                {{ existingTag ? 'Update' : 'Create' }}
            </SimpleButton>
        </div>
    </template>
</ModalContainer>
</template>

<script setup>
import { ref, computed } from 'vue';

// Import tag store
import { useTagsStore } from '@/stores/tags';
const { create, update } = useTagsStore();

const props = defineProps({
    title: {
        type: String,
        default: 'Confirmation',
    },
    subtitle: {
        type: String,
        required: false,
    },
    tag: {
        type: Object,
        default: _ => ({
            name: '',
            is_active: false
        })
    },
    position: {
        type: String,
        default: 'justify-content: flex-end;'
    },
});

// Determine if the tag is being created or updating by checking if they have an existing ID
const existingTag = computed(() => props.tag?.id);

const form = ref({ ...props.tag });

const saving = ref(false);

const submit = async () => {
    const action = existingTag.value ? update : create;
    const mode = existingTag.value ? 'updated' : 'created';

    try {
        saving.value = true;

        await action({...form.value});

        $toast.success(`You have successfully ${mode} a tag.`);
    } catch (error) {
        $toast.error(`An unexpected error happened, the tag could not be ${mode}.`);
    } finally {
        saving.value = false;
    }
}
</script>

<style scoped>
/* Custom switch styles */
/* .slider {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
    background-color: #ccc;
    border-radius: 34px;
    transition: background-color 0.4s;
}

.slider:before {
    content: "";
    position: absolute;
    height: 26px;
    width: 26px;
    background-color: white;
    border-radius: 50%;
    bottom: 4px;
    left: 4px;
    transition: transform 0.4s;
}

input:checked + .slider {
    background-color: #2196F3;
}

input:checked + .slider:before {
    transform: translateX(26px);
} */
</style>