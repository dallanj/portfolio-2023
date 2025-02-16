<template>
<ModalContainer class="simple-modal__container" modal-size="md" @close="$emit('close')">
    <template #header>{{ title }}</template>
    <template v-if="subtitle" #subtitle>{{ subtitle }}</template>

    <form @submit.prevent="submit">
        <div class="rounded-lg space-y-4">
            <!-- First Name -->
            <div class="grid sm:grid-cols-5 items-center gap-1 sm:gap-4 hover:bg-dropdown-hover rounded-md px-2 py-1">
                <label for="first_name" class="col-span-2 text-left sm:text-right md:text-left flex gap-4 items-center">
                    <FontAwesomeIcon icon="laptop-file" />
                    Dock position
                </label>
                <div class="col-span-3">
                    <div class="w-full">
                        <SimpleDropdown
                            v-model="selected"
                            :options="options"
                            placeholder="Choose an option"
                            @update:modelValue="setDockPosition(selected)" />
                        <!-- <p class="mt-2 text-sm">Selected: {{ selected }}</p> -->
                    </div>
                    <!-- https://chatgpt.com/canvas/shared/67b0ce094a948191b67ec630984722a0 -->
                    <!-- <SimpleRoundSwitch
                        label="Is Activated"
                        :classes="'grid grid-cols-1 sm:grid-cols-4 items-center gap-1 sm:gap-4'"
                        @click="setDockPosition" /> -->
                <!-- <input
                    v-model="form.first_name"
                    id="first_name"
                    type="text"
                    placeholder="First name"
                    class="w-full p-2 border border-gray-300 rounded-md" /> -->
                </div>
            </div>

            <div class="items-center flex justify-between gap-1 sm:gap-4 hover:bg-dropdown-hover rounded-md px-2 py-1">
                <label for="first_name" class="col-span-2 text-left sm:text-right md:text-left flex gap-4 items-center">
                    <FontAwesomeIcon icon="times" />
                    Dock position
                </label>
                <FontAwesomeIcon icon="chevron-right" />
                <!-- <div class="col-span-3"> -->
                    <!-- <FontAwesomeIcon icon="chevron-right" /> -->
                <!-- </div> -->
            </div>

            <!-- Last Name -->
            

        </div>
    </form>

    <template #footer>
        <div class="flex gap-x-4">
            <!-- <SimpleButton state="secondary" @click="$emit('close')">Cancel</SimpleButton>
            <SimpleButton
                :icon="saving ? 'spinner' : false"
                :icon-spin="saving"
                @click="submit">
                {{ existingUser ? 'Update' : 'Create' }}
            </SimpleButton> -->
        </div>
    </template>
</ModalContainer>
</template>

<script setup>
// import SimpleButton from '@/Components/SimpleButton.vue';
import { ref, computed } from 'vue';

// Import user store
import { useSettingsStore } from '@/stores/settings';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
// const { createUser, updateUser } = useUserStore();
const {
    setDockPosition,
    toggleSettingsMenu,
    settingsMenu,
    dockPosition
} = useSettingsStore();
const selected = ref(dockPosition);
const options = ['left', 'bottom'];
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
    user: {
        type: Object,
        default: _ => ({
            first_name: '',
            last_name: '',
            test: true,
            phone_number: '',
        })
    }
});

// Determine if the user is being created or updating by checking if they have an existing ID
const existingUser = computed(() => props.user?.id);

const form = ref({ ...props.user });

const showPasswordFields = ref(false);

const saving = ref(false);

const submit = async () => {
    // const action = existingUser.value ? updateUser : createUser;
    // const mode = existingUser.value ? 'updated' : 'created';

    // try {
    //     saving.value = true;

    //     await action({...form.value});

    //     $toast.success(`You have successfully ${mode} a user.`);
    // } catch (error) {
    //     $toast.error(`An unexpected error happened, the user could not be ${mode}.`);
    // } finally {
    //     saving.value = false;
    // }
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