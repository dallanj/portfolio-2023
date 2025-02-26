import { piniaLoader } from '@/utils/pinia';

const responseInterceptor = [
    response => {
        piniaLoader(response.data);
        return response;
    },
];

export default {
    response: responseInterceptor,
};