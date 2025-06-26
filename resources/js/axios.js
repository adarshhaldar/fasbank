import axios from 'axios';
import { toast } from 'vue3-toastify';
import router from './router';

const call = axios.create({
    baseURL: '/api/',
    headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
    },
});

call.interceptors.response.use(
    response => response,
    error => {
        if (error.response) {
            handleErrorResponse(error.response);
        } else if (error.request) {
            toast.error('No response from server');
        } else {
            toast.error(error.message);
        }
        return Promise.reject(error);
    }
);

function handleErrorResponse(response) {
    let errorMessage = `Unexpected error (Status: ${response.status}):`;

    switch (response.status) {
        case 400:
            errorMessage = response.data.message;
            break;
        case 401:
            errorMessage = 'Unauthorized: Please login.';
            break;
        case 403:
            errorMessage = 'Forbidden: You do not have permission.';
            break;
        case 404:
            errorMessage = 'Not Found: The requested resource was not found.';
            break;
        case 408:
            errorMessage = 'Request Timeout';
            break;
        case 422:
            errorMessage = response.data.message;
            break;
        case 429:
            errorMessage = 'Too Many Requests: Slow down!';
            break;
        case 500:
            errorMessage = 'Internal Server Error';
            break;
        case 502:
            errorMessage = 'Bad Gateway: Server issue.';
            break;
        case 503:
            errorMessage = 'Service Unavailable: Try again later.';
            break;
        case 504:
            errorMessage = 'Gateway Timeout';
            break;
        default:
            errorMessage = errorMessage;
    }

    if (response.status == 401) {
        localStorage.removeItem('token');
        router.push({ name: 'login' });
        setTimeout(() => {
            toast.error(errorMessage);
        }, 200);
    } else {
        toast.error(errorMessage);
    }
}

export default call;
