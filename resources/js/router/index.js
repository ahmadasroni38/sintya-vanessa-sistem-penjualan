import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";

// Auth Views
import Login from "../views/Auth/Login.vue";

// Dashboard Views
import DashboardLayout from "../layouts/DashboardLayout.vue";
import Dashboard from "../views/Dashboard/Dashboard.vue";
import Profile from "../views/Dashboard/Profile.vue";
import SystemSetting from "../views/Settings/SystemSetting.vue";
import Users from "../views/Dashboard/Users.vue";
import Roles from "../views/Dashboard/Roles.vue";
import Permissions from "../views/Dashboard/Permissions.vue";
import AssetCategories from "../views/Dashboard/AssetCategories.vue";
import Assets from "../views/Dashboard/Assets.vue";
import Locations from "../views/Dashboard/Locations.vue";
import Vendors from "../views/Dashboard/Vendors.vue";

// Warehouse Management Views
import Products from "../views/Dashboard/Warehouse/Products.vue";
import StockMasuk from "../views/Dashboard/Warehouse/StockMasuk.vue";
import Mutasi from "../views/Dashboard/Warehouse/Mutasi.vue";
import Adjustment from "../views/Dashboard/Warehouse/Adjustment.vue";
import Stockopname from "../views/Dashboard/Warehouse/Stockopname.vue";
import BukuStock from "../views/Dashboard/Warehouse/BukuStock.vue";

// Maintenance Management Views
import WorkOrders from "../views/Dashboard/WorkOrders.vue";
import PreventiveMaintenance from "../views/Dashboard/PreventiveMaintenance.vue";
import RepairRequests from "../views/Dashboard/RepairRequests.vue";

// Accounting Views
import ChartOfAccounts from "../views/Dashboard/ChartOfAccounts.vue";
import JournalEntries from "../views/Dashboard/JournalEntries.vue";
import Sales from "../views/Dashboard/Sales.vue";
import Customers from "../views/Dashboard/Customers.vue";

// Report Views
import NeracaLajur from "../views/Dashboard/Reports/NeracaLajur.vue";
import Neraca from "../views/Dashboard/Reports/Neraca.vue";
import LabaRugi from "../views/Dashboard/Reports/LabaRugi.vue";
import PerubahanModal from "../views/Dashboard/Reports/PerubahanModal.vue";
import ArusKas from "../views/Dashboard/Reports/ArusKas.vue";

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
                component: SystemSetting,
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
            // Warehouse Management Routes
            {
                path: "/products",
                name: "products.index",
                component: Products,
            },
            {
                path: "/stock-masuk",
                name: "stock-masuk.index",
                component: StockMasuk,
            },
            {
                path: "/mutasi",
                name: "mutasi.index",
                component: Mutasi,
            },
            {
                path: "/adjustment",
                name: "adjustment.index",
                component: Adjustment,
            },
            {
                path: "/stockopname",
                name: "stockopname.index",
                component: Stockopname,
            },
            {
                path: "/buku-stock",
                name: "buku-stock.index",
                component: BukuStock,
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
            // Accounting Routes
            {
                path: "/chart-of-accounts",
                name: "chart-of-accounts",
                component: ChartOfAccounts,
            },
            {
                path: "/journal-entries",
                name: "journal-entries",
                component: JournalEntries,
            },
            // Sales Routes
            {
                path: "/sales",
                name: "sales.index",
                component: Sales,
            },
            // Customer Routes
            {
                path: "/customers",
                name: "customers.index",
                component: Customers,
            },
            // Report Routes
            {
                path: "/reports/neraca-lajur",
                name: "reports.neraca-lajur",
                component: NeracaLajur,
            },
            {
                path: "/reports/neraca",
                name: "reports.neraca",
                component: Neraca,
            },
            {
                path: "/reports/laba-rugi",
                name: "reports.laba-rugi",
                component: LabaRugi,
            },
            {
                path: "/reports/perubahan-modal",
                name: "reports.perubahan-modal",
                component: PerubahanModal,
            },
            {
                path: "/reports/arus-kas",
                name: "reports.arus-kas",
                component: ArusKas,
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

    console.log("Router guard - navigating to:", to.name, "from:", from.name);
    console.log("Auth state:", {
        isAuthenticated: authStore.isAuthenticated,
        hasToken: !!authStore.token,
    });

    // Allow public routes
    if (to.meta.public) {
        console.log("Public route, allowing access");
        next();
        return;
    }

    // Check if route requires guest (not authenticated)
    if (to.meta.requiresGuest) {
        if (authStore.isAuthenticated) {
            console.log("User is authenticated, redirecting to Dashboard");
            next({ name: "Dashboard" });
            return;
        }
        console.log("Guest route, allowing access");
        next();
        return;
    }

    // Check if route requires authentication
    if (to.meta.requiresAuth) {
        if (!authStore.isAuthenticated) {
            console.log("Not authenticated, redirecting to Login");
            next({ name: "login", query: { redirect: to.fullPath } });
            return;
        }

        // Verify token is still valid (only if not coming from login page to avoid extra API call)
        if (from.name !== "login") {
            console.log("Verifying token validity");
            try {
                const isValid = await authStore.checkAuth();
                if (!isValid) {
                    console.log("Token invalid, redirecting to Login");
                    next({ name: "login", query: { redirect: to.fullPath } });
                    return;
                }
            } catch (error) {
                console.error("Error checking auth:", error);
                next({ name: "login", query: { redirect: to.fullPath } });
                return;
            }
        }
    }

    console.log("Navigation allowed");
    next();
});

export default router;
