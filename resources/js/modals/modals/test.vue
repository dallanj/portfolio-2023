<script setup>
import { Link, router } from '@inertiajs/vue3';
import { reactive, ref, inject } from 'vue';
import { storeToRefs } from 'pinia';
import { useOptionsStore } from '@/stores/options';

defineProps({
    href: {
        type: String,
        default: '/',
    },
});

const logout = () => {
    router.post('/logout');
};

const { isSideNavOpen, showMenuOverlay, isDesktopMenu, isMobileMenu, getMenuClass, closeSideNav, isMobileMenuOpen } = inject('menuLayout');

const optionStore = useOptionsStore();
const { navigation } = storeToRefs(optionStore);

const activeMenus = reactive({
    navs: [],
    menus: [],
    subMenus: []
});

const toggleMenu = (menu, key) => {
    const deepToggle = (items, key) => {
        items.forEach((item, key) => {
            if (item === menu) {
                // Toggle the toggled state
                item.toggled = !item.toggled;

                // Add or update the state in activeMenus
                if (item.toggled) {
                    activeMenus[key[item.name]] = true;
                } else {
                    delete activeMenus[key[item.name]]
                }

                console.log('activeMenus:', activeMenus, item);
            }

            // Recursively handle submenus
            if (item.menus && Array.isArray(item.menus)) {
                if (!activeMenus.menus[item.name]) {
                    activeMenus.menus[item.name] = [];
                }
                deepToggle(item.menus, 'menus');
            }
            if (item.submenus && Array.isArray(item.submenus)) {
                if (!activeMenus.subMenus[item.name]) {
                    activeMenus.subMenus[item.name] = [];
                }
                deepToggle(item.submenus, 'subMenus');
            }
        });
    };

    // Perform deep toggle on navigation structure
    deepToggle(navigation.value, key);
};
</script>

<template>
<div>
    <Transition
        enter-active-class="slide-menu-to-left"
        leave-active-class="slide-menu-to-right">
        <SimpleCard
            class="simple-nav"
            :class="getMenuClass">
            <slot>
                <nav>
                    <menu
                        v-for="nav in navigation"
                        :key="`navigation-${nav.title}`"
                        class="simple-nav__container">
                        <li class="simple-nav__container__item">
                            <h2 @click="toggleMenu(nav)">
                                <div class="flex w-full items-center justify-between mb-3">
                                    <span class="simple-nav__container__title">{{ nav.title }}</span>
                                    <FontAwesomeIcon
                                        :icon="nav.toggled ? 'chevron-down' : 'chevron-right'"
                                        class="fa-fw" />
                                </div>
                            </h2>

                            <template v-if="nav.toggled">
                                <menu
                                    v-for="menu in nav.menus"
                                    :key="`navigation-${nav.title}-route-${menu.path}`"
                                    class="flex flex-col">
                                    
                                    <Link
                                        :href="menu.path"
                                        :except="['navigation']"
                                        class="simple-nav__container__item flex justify-between"
                                        :class="{ 'active': $page.url == menu.path}"
                                        method="get" as="button" type="button" preserve-state preserve-scroll
                                        :onBefore="() => toggleMenu(menu, 'menu')"
                                        @click="closeSideNav">

                                        <div class="flex items-center">
                                            <FontAwesomeIcon :icon="menu.icon" class="mr-3 fa-fw" />
                                            {{ menu.label }}
                                        </div>

                                        <button v-if="menu.submenus" class="justify-self-end">
                                            <FontAwesomeIcon
                                                :icon="menu.toggled ? 'chevron-down' : 'chevron-right'"
                                                class="fa-fw" />
                                        </button>
                                    </Link>

                                    <template v-if="menu.toggled">
                                        <menu
                                            v-for="submenu in menu.submenus"
                                            :key="`submenu-${submenu.path}`"
                                            class="ml-8 flex flex-col text-sm">
                                    
                                            <Link
                                                :class="{ 'active': $page.url == submenu.path}"
                                                :href="submenu.path"
                                                @click="closeSideNav">
                                                {{ submenu.label }}
                                            </Link>
                                        </menu>
                                    </template>
                                </menu>
                            </template>
                           
                        </li>
                    </menu>
                    <button class="simple-nav__container__item w-full bg-white absolute bottom-0 left-0" @click.prevent="logout">
                        <div class="flex items-center w-full">
                            <FontAwesomeIcon icon="right-from-bracket" class="mr-3 fa-fw" />
                            Logout
                        </div>
                    </button>
                    
                    
                </nav>
            </slot>
        </SimpleCard>
    </Transition>
</div>
</template>

<style lang="scss">
.simple-nav {
    @apply fixed w-72 filter drop-shadow-lg overflow-y-scroll whitespace-nowrap;
    
    &__desktop {
        @apply top-24 left-8 z-40 h-[calc(100vh-8rem)];
        
        header {
            @apply hidden;
        }
    }

    &__mobile,
    &__tablet,
    &__overlay {
        @apply top-0 left-0 z-50 h-full;
    }

    &__mobile {
        @apply w-screen;
    }

    &__overlay {
        @apply fixed z-50 w-full cursor-pointer bg-black/[.5];
    }

    &__header {
        @apply shrink-0 flex items-center justify-center mb-3 pb-3 border-b pt-1;
    }

    &__container {
        @apply space-y-4;
        
        &__title {
            @apply uppercase font-semibold text-brand-dark-gray;
        }

        &__item {
            a, > div {
                @apply flex w-full py-2 pl-3 mb-1 rounded font-bold text-brand-light-gray tracking-wide transition duration-150 ease-in-out;
                
                &.active{
                    @apply text-brand-orange/[.80];
                }

                &:hover {
                    @apply bg-brand-orange/[.80] text-white;
                }
    
                &:focus {
                    @apply focus:outline-none text-brand-dark-gray;
                }
            }

        }
    }
}

@keyframes slideMenuToLeft {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(0);
    }
}
  
.slide-menu-to-left {
    animation: slideMenuToLeft 300ms ease-in-out forwards;
}

@keyframes slideMenuToRight {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-100%);
    }
}
  
.slide-menu-to-right {
    animation: slideMenuToRight 300ms ease-in-out forwards;
}
</style>