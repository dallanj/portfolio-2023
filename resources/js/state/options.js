import { reactive } from 'vue';

export const applications = reactive([
    {
        label: 'Terminal',
        value: 'terminal',
        application: true,
    },
    {
        label: 'About',
        value: 'about',
        application: true,
    },
    {
        label: 'Projects',
        value: 'projects',
        application: true,
    },
    {
        label: 'Contact',
        value: 'contact',
        application: true,
    },
    {
        label: 'Linked-In',
        value: 'linkedin',
        application: false,
        action: () => window.open('https://www.linkedin.com/in/dallanj', '_blank'),
    },
    {
        label: 'Github',
        value: 'github',
        application: false,
        action: () => window.open('https://github.com/dallanj', '_blank'),
    },
    {
        label: 'Resume',
        value: 'resume',
        application: false,
        action: () => window.open('https://dallan.ca/resume.pdf', '_blank'),
    },
]);

export const topBar = reactive({
    activities: {
        label: 'Activities',
        value: 'activities',
    },
    current: {
        label: 'Current Window',
        value: 'current',
        action: () => {
            // TODO: Window options dropdown
        }
    },
    date: {
        label: () => {
            return new Intl.DateTimeFormat([], {
                timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                month: 'short',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                hour12: false,
            }).format(new Date()).replace(/,/g, ' ');
        },
        value: 'date',
    },
    settings: {
        label: 'Settings',
        value: 'settings',
    }
});
