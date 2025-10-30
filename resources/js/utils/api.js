import axios from "axios";
import { useAuthStore } from "@/stores/auth";

/**
 * Create an axios instance with default configuration
 */
function createApiInstance() {
    const authStore = useAuthStore();

    const instance = axios.create({
        baseURL: "/api",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
        },
    });

    // Add request interceptor to include auth token
    instance.interceptors.request.use(
        (config) => {
            if (authStore.token) {
                config.headers.Authorization = `Bearer ${authStore.token}`;
            }
            return config;
        },
        (error) => {
            return Promise.reject(error);
        }
    );

    // Add response interceptor to handle common errors
    instance.interceptors.response.use(
        (response) => response,
        (error) => {
            if (error.response?.status === 401) {
                // Token expired or invalid - refresh page to redirect to login
                authStore.logout();
                window.location.reload();
                return Promise.reject(error);
            }
            return Promise.reject(error);
        }
    );

    return instance;
}

/**
 * GET request
 */
export function apiGet(url, config = {}) {
    return createApiInstance()
        .get(url, config)
        .then((response) => response.data);
}

/**
 * POST request
 */
export function apiPost(url, data, config = {}) {
    return createApiInstance()
        .post(url, data, config)
        .then((response) => response.data);
}

/**
 * PUT request
 */
export function apiPut(url, data, config = {}) {
    return createApiInstance()
        .put(url, data, config)
        .then((response) => response.data);
}

/**
 * DELETE request
 */
export function apiDelete(url, config = {}) {
    return createApiInstance()
        .delete(url, config)
        .then((response) => response.data);
}

/**
 * API object with methods
 */
const api = {
    get: apiGet,
    post: apiPost,
    put: apiPut,
    delete: apiDelete,
};

export default api;
