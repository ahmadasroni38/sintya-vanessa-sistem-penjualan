import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";

// Auth Views
import Login from "../views/Auth/Login.vue";

// Dashboard Views
import DashboardLayout from "../layouts/DashboardLayout.vue";
import Dashboard from "../views/Dashboard/Dashboard.vue";
import Profile from "../views/Dashboard/Profile.vue";
import Settings from "../views/Dashboard/Settings.vue";
import Users from "../views/Dashboard/Users.vue";
import Roles from "../views/Dashboard/Roles.vue";
import Permissions from "../views/Dashboard/Permissions.vue";
import AssetCategories from "../views/Dashboard/AssetCategories.vue";
import Assets from "../views/Dashboard/Assets.vue";
import Locations from "../views/Dashboard/Locations.vue";
import Vendors from "../views/Dashboard/Vendors.vue";

// Maintenance Management Views
import WorkOrders from "../views/Dashboard/WorkOrders.vue";
import PreventiveMaintenance from "../views/Dashboard/PreventiveMaintenance.vue";
import RepairRequests from "../views/Dashboard/RepairRequests.vue";

// Public Views
import NotFound from "../views/Errors/NotFound.vue";
import AssetDetailPublic from "../views/AssetDetailPublic.vue";

const routes = [
    {
        path: "/login",
        name: "login",
        component: Login,
        meta: { requiresGuest: true },
    },
    {
        path: "/assets/:identifier",
        name: "asset-detail-public",
        component: AssetDetailPublic,
        meta: { public: true },
    },
    {
        path: "/",
        component: DashboardLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: "",
                name: "Dashboard",
                component: Dashboard,
            },
            {
                path: "/profile",
                name: "Profile",
                component: Profile,
            },
            {
                path: "/settings",
                name: "Settings",
                component: Settings,
            },
            {
                path: "/users",
                name: "Users",
                component: Users,
            },
            {
                path: "/roles",
                name: "Roles",
                component: Roles,
            },
            {
                path: "/permissions",
                name: "Permissions",
                component: Permissions,
            },
            {
                path: "/asset-categories",
                name: "asset-categories.index",
                component: AssetCategories,
            },
            {
                path: "/assets",
                name: "assets.index",
                component: Assets,
            },
            {
                path: "/locations",
                name: "locations.index",
                component: Locations,
            },
            {
                path: "/vendors",
                name: "vendors.index",
                component: Vendors,
            },
            {
                path: "/work-orders",
                name: "work-orders",
                component: WorkOrders,
            },
            {
                path: "/preventive-maintenance",
                name: "preventive-maintenance",
                component: PreventiveMaintenance,
            },
            {
                path: "/repair-requests",
                name: "repair-requests",
                component: RepairRequests,
            },
        ],
    },
    {
        path: "/:pathMatch(.*)*",
        name: "NotFound",
        component: NotFound,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation guards
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    // Check if route requires authentication
    if (to.meta.requiresAuth) {
        if (!authStore.isAuthenticated) {
            next({ name: "Login", query: { redirect: to.fullPath } });
            return;
        }

        // Verify token is still valid
        const isValid = await authStore.checkAuth();
        if (!isValid) {
            next({ name: "Login", query: { redirect: to.fullPath } });
            return;
        }
    }

    // Check if route requires guest (not authenticated)
    if (to.meta.requiresGuest && authStore.isAuthenticated) {
        next({ name: "Dashboard" });
        return;
    }

    next();
});

export default router;
