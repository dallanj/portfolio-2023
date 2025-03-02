import { useToast } from 'vue-toastification';

const toast = useToast();

const responseInterceptor = [
    response => {
        if (response.data.toast) {
            toast.success(response.data.toast.message, response.data.toast.options ?? {});
        }
        return response;
    },
    error => {
        if (error?.response?.data?.toast) {
            toast.error(error.response.data.toast.message, error.response.data.toast.options ?? {});
        }
        return Promise.reject(error);
    }
];

export default {
    request: null,
    response: responseInterceptor,
};
