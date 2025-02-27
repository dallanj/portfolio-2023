<script setup>
import { ref, onMounted, onUnmounted, computed, inject } from 'vue';
import SimpleButton from '@/components/Simple/SimpleButton.vue';

// Composables
const { isMobile } = inject('screenSize');

const props = defineProps({
    state: {
        type: String,
        default: 'action',
        validator: value => ['action', 'ellipsis-vertical', 'ellipsis'].includes(value),
    },
    align: {
        type: String,
        default: 'mt-2 right-0',
    },
    menuWidth: {
        type: String,
        default: 'w-48',
    },
    width: {
        type: String,
        default: 'w-48',
    },
    contentClasses: {
        type: String,
        default: 'bg-white dark:bg-gray-800',
    },
    actions: {
        type: Array,
        default: _ => ([])
    },
    item: {
        type: Object,
        default: _ => ({})
    },
    textColourClass: {
        type: String,
        default: 'text-gray-900 dark:text-gray-100'
    }
});

const emit = defineEmits(['action-selected']);
    
const isOpen = ref(false);
const button = ref(null);
const menu = ref(null);
const rotateAnimationClass = ref('');

const iconClass = computed(_ => {
    if (isMobile.value) {
        return isOpen.value ? 'chevron-down' : 'chevron-up';
    } else {
        return isOpen.value ? 'chevron-up' : 'chevron-down';
    }
});

const handleAction = (action) => {
    emit('action-selected', action);
    closeMenu(true);
};

const toggleDropdown = () => {
    closeMenu();
};

const closeOnClickOutside = (e) => {
    if (button.value && !button.value.contains(e.target) &&
        menu.value && !menu.value.contains(e.target)) {
        closeMenu(true);
    }
};

const closeOnEscape = (e) => {
    if (isOpen.value && e.key === 'Escape') {
        closeMenu(true);
    }
};

const closeMenu = (close = false) => {
    rotateAnimationClass.value = isOpen.value ? 'rotate-up' : 'rotate-down';
    setTimeout(() => {
        isOpen.value = close ? false : !isOpen.value
    }, 200);
}

onMounted(() => {
    document.addEventListener('click', closeOnClickOutside);
    document.addEventListener('keydown', closeOnEscape)
});

onUnmounted(() => {
    document.removeEventListener('click', closeOnClickOutside);
    document.removeEventListener('keydown', closeOnEscape)
});
</script>

<template>
<div
    class="simple-data-actions"
    :class="isMobile ? '' : ''">
    <div ref="button">
        <SimpleButton
            v-bind="{...$props, ...$attrs}"
            state="action"
            :class="isMobile ? 'justify-between' : ''"
            :uppercase="false"
            @click.prevent="toggleDropdown">
            
            <template v-if="state === 'action'">
                Actions
                <FontAwesomeIcon
                    class="ml-2 fa-fw"
                    :class="rotateAnimationClass"
                    :icon="iconClass" />
            </template>
            <template v-else>
                <div class="flex items-center justify-center w-8 h-8 transition duration-150 ease-in-out rounded-full hover:bg-blue-200/[.25]">
                    <FontAwesomeIcon class="fa-fw" size="lg" :icon="state" />
                </div>
            </template>
        </SimpleButton>  
    </div>
    <Transition
        enter-active-class="fade-enter-active"
        leave-active-class="fade-leave-active">
        <div
            v-if="isOpen"
            ref="menu"
            class="simple-data-actions__container"
            :class="[menuWidth, align]">
            <ul
                class="simple-data-actions__menu"
                :class="contentClasses">
                <li
                    v-for="action in actions"
                    :key="action.title"
                    class="simple-data-actions__menu__item"
                    :class="textColourClass"
                    @click="handleAction(action)">
                    <FontAwesomeIcon :icon="action.icon" class="fa-fw" />
                    {{ action.title }}
                </li>
            </ul>
        </div>
    </Transition>
</div>
</template>
    