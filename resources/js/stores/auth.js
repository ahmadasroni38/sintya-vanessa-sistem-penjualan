import { defineStore } from "pinia";
import { ref, computed } from "vue";

export const useAuthStore = defineStore("auth", () => {
    const user = ref(null);
    const token = ref(localStorage.getItem("token") || null);
    const isLoading = ref(false);

    const isAuthenticated = computed(() => !!token.value);
    const userRole = computed(() => user.value?.role || null);

    const login = async (credentials) => {
        isLoading.value = true;
        try {
            const response = await fetch("/api/auth/login", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify(credentials),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || "Invalid credentials");
            }

            if (data.success) {
                token.value = data.token;
                user.value = data.user;

                localStorage.setItem("token", data.token);
                localStorage.setItem("user", JSON.stringify(data.user));

                return { success: true, user: data.user };
            } else {
                throw new Error(data.message || "Login failed");
            }
        } catch (error) {
            console.error("Login error:", error);
            return { success: false, error: error.message };
        } finally {
            isLoading.value = false;
        }
    };

    const logout = async () => {
        try {
            if (token.value) {
                await fetch("/api/auth/logout", {
                    method: "POST",
                    headers: {
                        "Authorization": `Bearer ${token.value}`,
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    },
                });
            }
        } catch (error) {
            console.error("Logout error:", error);
        } finally {
            token.value = null;
            user.value = null;
            localStorage.removeItem("token");
            localStorage.removeItem("user");
        }
    };

    const loadUserFromStorage = () => {
        const savedUser = localStorage.getItem("user");
        if (savedUser) {
            try {
                user.value = JSON.parse(savedUser);
            } catch (error) {
                console.error("Error parsing saved user:", error);
                localStorage.removeItem("user");
            }
        }
    };

    const checkAuth = async () => {
        if (!token.value) return false;

        try {
            const response = await fetch("/api/auth/me", {
                headers: {
                    "Authorization": `Bearer ${token.value}`,
                    "Accept": "application/json",
                },
            });

            if (!response.ok) {
                throw new Error("Token invalid");
            }

            const data = await response.json();

            if (data.success) {
                user.value = data.user;
                localStorage.setItem("user", JSON.stringify(data.user));
                return true;
            } else {
                throw new Error("Auth check failed");
            }
        } catch (error) {
            await logout();
            return false;
        }
    };

    return {
        user,
        token,
        isLoading,
        isAuthenticated,
        userRole,
        login,
        logout,
        loadUserFromStorage,
        checkAuth,
    };
});