const registerGlobalInterceptors = (options = { axios: window.axios }) => {
    const axios = options.axios;
    const interceptors = import.meta.glob('../interceptors/*.js', { eager: true });

    // Initialize axios interceptors        
    Object.entries(interceptors).forEach(([path, module]) => {
        // Apply request interceptors if present
        if (module.default?.request) {
            axios.interceptors.request.use(...module.default.request);
            console.log('Global Interceptor Plugin request present');
        }

        // Apply response interceptors if present
        if (module.default?.response) {
            axios.interceptors.response.use(...module.default.response);
            console.log('Global Interceptor Plugin response present');
        }
    });
};

export default registerGlobalInterceptors;