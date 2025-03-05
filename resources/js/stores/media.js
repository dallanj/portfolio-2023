import axios from 'axios';
import { acceptHMRUpdate, defineStore } from 'pinia';
import { ref, computed, reactive } from 'vue';

export const useMediaStore = defineStore('media', () => {
    const all = ref(null)
    const media = ref(null)
      

    function $reset(key = null) {
        const resetMap = {
            all: () => { all.value = null; },
            media: () => { media.value = null; },
        };
    
        if (key && resetMap[key]) {
            // Reset only the specific key passed
            resetMap[key]();
        } else {
            // Reset all if no key or invalid key is provided
            Object.values(resetMap).forEach(resetFn => resetFn());
        }
    }

    // Function the takes file size in bytes and returns new count plus file size type
    const shortenFileSize = (bits, base = 1024) => {
        let units = ['Bytes', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB']
            .map(unit => base === 1000 && unit.replace('i',''));

        let l = 0, n = parseInt(bits, 10) || 0;

        while(n >= base && ++l){
            n = n / base;
        }

        return(n.toFixed(n < 10 && l > 0 ? 1 : 0) + ' ' + units[l]);
    }

    // Function to format a date to "time ago" format
    const getTimeAgo = (createdAt) => {
        const now = new Date();
        const past = new Date(createdAt);
        
        const diffInSeconds = Math.floor((now - past) / 1000);
        if (diffInSeconds < 60) return `${diffInSeconds} seconds ago`;

        const diffInMinutes = Math.floor(diffInSeconds / 60);
        if (diffInMinutes < 60) return `${diffInMinutes} minutes ago`;

        const diffInHours = Math.floor(diffInMinutes / 60);
        if (diffInHours < 24) return `${diffInHours} hours ago`;

        const diffInDays = Math.floor(diffInHours / 24);
        if (diffInDays < 7) return `${diffInDays} days ago`;

        const diffInWeeks = Math.floor(diffInDays / 7);
        if (diffInWeeks < 4) return `${diffInWeeks} weeks ago`;

        const diffInMonths = Math.floor(diffInDays / 30);
        if (diffInMonths < 12) return `${diffInMonths} months ago`;

        const diffInYears = Math.floor(diffInDays / 365);
        return `${diffInYears} years ago`;
    };

    return {
        all,
        media,
        getTimeAgo,
        shortenFileSize,
        $reset,
    };
});

// HMR
if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useMediaStore, import.meta.hot))
}
