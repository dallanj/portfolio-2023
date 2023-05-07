<template>
    <div>
        <TopHeader />

        <div class="flex">
            <SideNav />
            <slot />
        </div>
    </div>
</template>

<script setup>
import TopHeader from '@/components/TopHeader.vue';
import SideNav from '@/components/SideNav.vue';
</script>
