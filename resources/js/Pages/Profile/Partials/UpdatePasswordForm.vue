<script setup>
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, watch, nextTick } from 'vue';
import { useForm } from 'laravel-precognition-vue-inertia';

import { useUserStore } from '@/stores/user';
import { storeToRefs } from 'pinia';
const { profile } = storeToRefs(useUserStore());
const user = ref(null);

watch(profile, async (profile) => {
    await nextTick();
    user.value = profile;
}, { deep: true });

const form = (null);

watch(user, () => {
  if (user.value) {
    form.value = useForm('post', `/profile/${user.value.hash}/password`, {
      current_password: '',
      password: '',
      password_confirmation: '',
    });

    form.value.setValidationTimeout(3000);
  }
});


form.setValidationTimeout(3000);

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Update Password
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Ensure your account is using a long, random password to stay
                secure.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <SimpleInputLabel for="current_password" value="Password" />

                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                    @change="form.validate('current_password')"
                    @focus="form.forgetError('current_password')" />
                <InputError v-if="form.invalid('current_password')" :message="form.errors.current_password" class="mt-2" />
            </div>

            <div>
                <SimplePasswordField
                    id="password"
                    ref="passwordInput"
                    name="password"
                    v-model="form.password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                    @change="form.validate('password')"
                    @focus="form.forgetError('password')" />
            </div>

            <div>
                <SimpleInputLabel for="password_confirmation" value="Confirm Password" />

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                    @change="form.validate('password_confirmation')"
                    @focus="form.forgetError('password_confirmation')" />
    
                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>

            <div class="flex items-center gap-4">
                <SimpleButton
                    :disabled="form.processing || form.hasErrors"
                    @click="form.touch(['password','current_password','password_confirmation']).validate({
                        onValidationError: () => $toast.error('There was an error processing your password.'),
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
    </section>
</template>
