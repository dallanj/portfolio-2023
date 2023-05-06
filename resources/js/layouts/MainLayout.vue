<template>
    <div>
        <TopHeader />
        <SideNav />
        <slot />
    </div>
</template>

<script setup>
import TopHeader from '@/components/TopHeader.vue';
import SideNav from '@/components/SideNav.vue';
</script>
