<template>
<div class="relative">
    <button
    @click="toggleDropdown"
    class="w-full px-4 py-1 text-left border rounded-lg bg-white focus:outline-none text-black">
    {{ selectedLabel || placeholder }}
    </button>
    <ul v-if="isOpen" class="absolute w-full mt-1 bg-white border rounded-lg shadow-lg">
        <li
            v-for="option in options"
            :key="getOptionValue(option)"
            @click="selectOption(option)"
            class="px-4 py-2 cursor-pointer hover:bg-gray-100 text-black">
            {{ getOptionLabel(option) }}
        </li>
    </ul>
</div>
</template>

<script setup>
import { ref, computed, defineProps, defineEmits } from 'vue';

const modelValue = defineModel({
    type: [String, Number, Object],
    required: true,
});

const props = defineProps({
options: {
    type: Array,
    required: true,
},
// modelValue: {
//     type: [String, Number, Object],
//     default: null,
// },
placeholder: {
    type: String,
    default: 'Select an option',
},
labelKey: {
    type: String,
    default: 'label',
},
valueKey: {
    type: String,
    default: 'value',
},
});

const emit = defineEmits(['update:modelValue']);
const isOpen = ref(false);

const toggleDropdown = () => {
isOpen.value = !isOpen.value;
};

const getOptionLabel = (option) =>
typeof option === 'object' ? option[props.labelKey] : option;

const getOptionValue = (option) =>
typeof option === 'object' ? option[props.valueKey] : option;

const selectedLabel = computed(() => {
const selected = props.options.find(
    (opt) => getOptionValue(opt) === props.modelValue
);
return selected ? getOptionLabel(selected) : '';
});

const selectOption = (option) => {
emit('update:modelValue', getOptionValue(option));
isOpen.value = false;
};
</script>

<style scoped>
.relative { position: relative; }
.absolute { position: absolute; }
</style>
  