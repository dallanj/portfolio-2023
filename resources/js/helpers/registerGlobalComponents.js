import { defineAsyncComponent } from 'vue';

// Function to dynamically import and register all components from a directory
const registerGlobalComponents = (app, components) => {
    // Get the import.meta.globa object and iterate over each component
    Object.values(components).forEach((pathing) => {
        // Iterate over each component and register it globally
        Object.entries(pathing).forEach(([path, definition]) => {
            // Get the component name from the path
            const componentName = path.split('/').pop().replace(/\.\w+$/, '');
            // Register the component to the app globally
            app.component(componentName, defineAsyncComponent(definition));
        });
    });
};

export default registerGlobalComponents;
