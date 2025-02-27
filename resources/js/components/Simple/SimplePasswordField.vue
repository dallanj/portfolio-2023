<script setup>
import { ref, watch } from 'vue';

// Define model binding
const model = defineModel({
    type: String,
    required: true,
});

// Reactive references
const input = ref(null);
const showPassword = ref(false); // Password visibility toggle
const passwordStrength = ref(0);
const strengthLabel = ref('Weak');

// Expose focus method
defineExpose({ focus: () => input.value.focus() });

// Toggle visibility
const toggleVisibility = () => {
    showPassword.value = !showPassword.value;
};

// Generate a strong random password
const generatePassword = () => {
    const characters =
        'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+[]{}|;:,.<>?';
    let generatedPassword = '';
    for (let i = 0; i < 12; i++) {
        generatedPassword += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    model.value = generatedPassword; // Update model
    calculateStrength(); // Update strength
    showPassword.value = true;
};

// Calculate password strength
const calculateStrength = () => {
    let strength = 0;

    if (model.value.length >= 8) strength += 30;
    if (/[A-Z]/.test(model.value)) strength += 20; // Uppercase
    if (/[a-z]/.test(model.value)) strength += 20; // Lowercase
    if (/[0-9]/.test(model.value)) strength += 15; // Numbers
    if (/[^A-Za-z0-9]/.test(model.value)) strength += 15; // Special characters

    passwordStrength.value = Math.min(strength, 100);

    // Update strength label
    if (passwordStrength.value < 40) strengthLabel.value = 'Weak';
    else if (passwordStrength.value < 70) strengthLabel.value = 'Moderate';
    else strengthLabel.value = 'Strong';
};

// Watch for changes in the password field
watch(model, calculateStrength);
</script>

<template>
    <div class="relative">
        <SimpleInputLabel for="password" value="New Password" />
        <!-- Password Input -->
        <div v-tooltip="`Atleast 8 characters long`" class="relative flex items-center">
            <input
                :type="showPassword ? 'text' : 'password'"
                v-model="model"
                ref="input"
                autocomplete="new-password"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 w-full"
                placeholder="Enter password"
            />

            <!-- Generate Password Icon -->
            <button
                type="button"
                class="absolute right-10 inset-y-0 flex items-center text-gray-500 hover:text-blue-500"
                @click="generatePassword">
                <FontAwesomeIcon icon="cog" size="sm" class="fa-fw text-brand-light-gray transition ease-in-out duration-300 hover:text-brand-orange" />
            </button>

            <!-- Toggle Password Visibility Icon -->
            <button
                type="button"
                class="absolute right-2 inset-y-0 flex items-center text-gray-500 hover:text-blue-500"
                @click="toggleVisibility">
                <FontAwesomeIcon :icon="showPassword ? 'eye' : 'eye-slash'" size="sm" class="fa-fw text-brand-light-gray transition ease-in-out duration-300 hover:text-brand-orange" />
            </button>
        </div>

        <!-- Progress Bar -->
        <div class="mt-2 h-2 rounded-lg border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div
                class="h-2 rounded-lg transition-all duration-300"
                :class="{
                    'bg-red-500': passwordStrength < 40,
                    'bg-yellow-500': passwordStrength >= 40 && passwordStrength < 70,
                    'bg-green-500': passwordStrength >= 70,
                }"
                :style="{ width: passwordStrength + '%' }"
            ></div>
        </div>

        <!-- Strength Label -->
        <p
            class="mt-1 text-sm font-medium"
            :class="{
                'text-red-500': passwordStrength < 40,
                'text-yellow-500': passwordStrength >= 40 && passwordStrength < 70,
                'text-green-500': passwordStrength >= 70,
            }">
            {{ strengthLabel }}
        </p>
    </div>
</template>

<style scoped>
button {
    cursor: pointer;
}
</style>
