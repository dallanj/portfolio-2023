<template>
<div>
    <svg 
        :class="width"
        viewBox="15 20 80 60">
        <defs>
            <linearGradient id="linear-gradient" x1="31" y1="80.78" x2="47.96" y2="41.12" gradientUnits="userSpaceOnUse">
                <stop offset="0" stop-color="#ba4b00"/>
                <stop offset=".12" stop-color="#c55100"/>
                <stop offset=".47" stop-color="#e16200"/>
                <stop offset=".77" stop-color="#f26c00"/>
                <stop offset="1" stop-color="#f97000"/>
            </linearGradient>
            <linearGradient id="linear-gradient-2" x1="37.88" y1="50.88" x2="90.9" y2="50.88" gradientUnits="userSpaceOnUse">
                <stop offset="0" stop-color="#ad4600"/>
                <stop offset=".22" stop-color="#c55300"/>
                <stop offset=".53" stop-color="#e16300"/>
                <stop offset=".8" stop-color="#f26c00"/>
                <stop offset="1" stop-color="#f97000"/>
            </linearGradient>
        </defs>
        <polygon class="cls-1" points="51.99 64.2 35.49 64.2 29.88 77.29 18.59 77.29 38.1 31.86 43.74 44.99 39.94 53.83 47.55 53.83 51.99 64.2"/>
        <g>
            <path class="cls-2" d="M93.11,76.95h-8.58l-11.3-20.04-4.93-8.73h6.3c3.49,0,6.33-2.84,6.33-6.33v-1.43c0-3.49-2.84-6.33-6.33-6.33h-19.07l-3.98-9.26h23.94c8.87,0,16.05,7.18,16.05,16.05v3.03c0,6.27-4.43,11.51-10.34,12.75h-.05s11.95,20.31,11.95,20.31Z"/>
            <polygon class="cls-3" points="74.09 77.29 63.48 77.29 54.74 56.91 40.95 24.81 51.56 24.81 55.54 34.08 65.34 56.91 74.09 77.29"/>
        </g>
    </svg>
</div>
</template>

<script setup>
defineProps({
    width: {
        type: String,
        default: 'w-24'
    }
});
</script>

<style scoped>
.cls-1 {
    fill: url(#linear-gradient);
}

.cls-1, .cls-2, .cls-3 {
    stroke-width: 0px;
}

.cls-2 {
    fill: url(#linear-gradient-2);
}

.cls-3 {
    fill: #f97000;
}
</style>
    