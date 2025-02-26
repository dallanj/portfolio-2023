import { piniaLoader } from '@/utils/pinia';

const responseInterceptor = [
    response => {
        console.log('Piniaia Interceptor', response.precognitive);
        piniaLoader(response.data);
        return response;
    },
];

export default {
    response: responseInterceptor,
};