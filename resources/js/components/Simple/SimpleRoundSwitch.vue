<template>
<div :class="classes">
    <label v-if="label" class="col-span-1 text-left sm:text-right md:text-left">{{ label }}</label>
    <div class="col-span-3 flex items-center justify-between">
        <label class="switch">
            <input
                type="checkbox"
                :checked="modelValue"
                class="hidden"
                :disabled="disabled"
                @change="$emit('update:modelValue', $event.target.checked)" />
            <span class="slider round" />
        </label>
        <small v-if="!modelValue && sideMessage" class="font-bold text-brand-dark-gray">
            {{ sideMessage }}
        </small>
    </div>
</div>
</template>
    
<script setup>
const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    label: {
        type: String,
        required: false,
    },
    sideMessage: {
        type: String,
        required: false
    },
    classes: {
        type: String,
        default: 'grid grid-cols-1 sm:grid-cols-4 items-center gap-1 sm:gap-4'
    },
    disabled: {
        type: Boolean,
        default: false,
    }
});
</script>
    
<style lang="scss">
.switch {
    @apply relative inline-block h-9 w-16;
    width: 60px;
    height: 34px;
}
    
.switch input {
    @apply opacity-0 w-0 h-0;
}
    
.slider {
    @apply absolute cursor-pointer top-0 left-0 right-0 bottom-0 duration-500;
    background-color: #ccc;
    // transition: 0.4s;
}
    
.slider:before {
    @apply absolute bg-blue-200 w-7 h-7 duration-500 inset-y-1;
    content: "";
    // height: 26px;
    // width: 26px;
    // left: 4px;
    // bottom: 4px;
    // background-color: white;
    // transition: 0.4s;
}
    
input:checked + .slider {
    @apply bg-red-200
}
    
input:checked + .slider:before {
    transform: translateX(26px);
}
    
.slider.round {
    border-radius: 34px;
}
    
.slider.round:before {
    border-radius: 50%;
}
</style>
    