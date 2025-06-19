import { ref, onMounted, onUnmounted } from 'vue';

export function useLiveDateTime(formatOptions = {
    timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone,
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: 'numeric',
    second: 'numeric',
    hour12: false,
}) {
    const dateTime = ref('');

    const updateDateTime = () => {
        dateTime.value = new Intl.DateTimeFormat([], formatOptions)
            .format(new Date())
            .replace(/,/g, ' ');
    };

    let interval;

    onMounted(() => {
        updateDateTime();
        interval = setInterval(updateDateTime, 1000); // update every second
    });

    onUnmounted(() => {
        clearInterval(interval);
    });

    return { dateTime };
}