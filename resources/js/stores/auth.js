import { defineStore } from "pinia";
import { ref, computed } from "vue";

export const useAuthStore = defineStore("auth", () => {
    const user = ref(null);
    const roles = ref([]);
    const roleActive = ref(null);
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
                roles.value = data.roles;
                roleActive.value = data.role_active;

                localStorage.setItem("token", data.token);
                localStorage.setItem("user", JSON.stringify(data.user));
                localStorage.setItem("roles", JSON.stringify(data.roles));
                localStorage.setItem("role_active", JSON.stringify(data.role_active));

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
            roles.value = [];
            roleActive.value = null;
            localStorage.removeItem("token");
            localStorage.removeItem("user");
            localStorage.removeItem("roles");
            localStorage.removeItem("role_active");
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

        const savedRoles = localStorage.getItem("roles");
        if (savedRoles) {
            try {
                roles.value = JSON.parse(savedRoles);
            } catch (error) {
                console.error("Error parsing saved roles:", error);
                localStorage.removeItem("roles");
            }
        }

        const savedRoleActive = localStorage.getItem("role_active");
        if (savedRoleActive) {
            try {
                roleActive.value = JSON.parse(savedRoleActive);
            } catch (error) {
                console.error("Error parsing saved role_active:", error);
                localStorage.removeItem("role_active");
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
                roles.value = data.roles;
                roleActive.value = data.role_active;

                localStorage.setItem("user", JSON.stringify(data.user));
                localStorage.setItem("roles", JSON.stringify(data.roles));
                localStorage.setItem("role_active", JSON.stringify(data.role_active));
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
        roles,
        roleActive,
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
