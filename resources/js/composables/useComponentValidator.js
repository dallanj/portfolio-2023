import { getCurrentInstance } from 'vue';

export function useComponentValidator() {
    const { appContext } = getCurrentInstance();
    // Get list of dynamically registered components
    const components = Object.keys(appContext.app._context.components);
    // Create a method to check if component is registered
    const isValidComponent = (componentName) => {
        return components.some(component => component === componentName);
    };
    
    return { isValidComponent };
};
