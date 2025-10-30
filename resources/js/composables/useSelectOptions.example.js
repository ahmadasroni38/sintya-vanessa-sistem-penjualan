/**
 * ============================================================================
 * EXAMPLE: useSelectOptions Composable Usage
 * ============================================================================
 *
 * Quick reference untuk menggunakan useSelectOptions composable
 * dalam berbagai skenario yang umum.
 */

import { onMounted } from "vue";
import { useSelectOptions } from "./useSelectOptions";

// ============================================================================
// EXAMPLE 1: Basic Usage - Load Options
// ============================================================================
export function example1_BasicUsage() {
    const { unitOptions, loadUnits } = useSelectOptions();

    onMounted(async () => {
        // Load units dari backend
        await loadUnits();

        // unitOptions sekarang terisi dengan data:
        // [
        //   { value: 1, label: "Pieces (pcs)", name: "Pieces", code: "PCS", symbol: "pcs" },
        //   { value: 2, label: "Kilogram (kg)", name: "Kilogram", code: "KG", symbol: "kg" }
        // ]
    });

    return { unitOptions };
}

// ============================================================================
// EXAMPLE 2: Load Multiple Options in Parallel
// ============================================================================
export function example2_ParallelLoading() {
    const {
        unitOptions,
        categoryOptions,
        locationOptions,
        loadUnits,
        loadCategories,
        loadLocations,
    } = useSelectOptions();

    onMounted(async () => {
        // Load semua options secara bersamaan (parallel)
        await Promise.all([
            loadUnits(),
            loadCategories(),
            loadLocations(),
        ]);

        // Semua options sekarang terisi
        console.log("Units:", unitOptions.value.length);
        console.log("Categories:", categoryOptions.value.length);
        console.log("Locations:", locationOptions.value.length);
    });

    return { unitOptions, categoryOptions, locationOptions };
}

// ============================================================================
// EXAMPLE 3: Add New Item
// ============================================================================
export function example3_AddNewItem() {
    const { unitOptions, addUnit } = useSelectOptions();

    const handleAddUnit = async () => {
        try {
            // Add new unit via API
            const newUnit = await addUnit({
                name: "Dozen",
                symbol: "dz",
                description: "12 pieces",
            });

            console.log("Unit added successfully:", newUnit);
            // unitOptions otomatis ter-update dengan item baru
        } catch (error) {
            console.error("Failed to add unit:", error);
        }
    };

    return { unitOptions, handleAddUnit };
}

// ============================================================================
// EXAMPLE 4: Force Refresh
// ============================================================================
export function example4_ForceRefresh() {
    const { unitOptions, loadUnits } = useSelectOptions();

    const handleRefresh = async () => {
        // Force refresh dari backend (bypass cache)
        await loadUnits(true);
        console.log("Units refreshed from backend");
    };

    const handleNormalLoad = async () => {
        // Normal load (gunakan cache jika masih valid)
        await loadUnits();
        console.log("Units loaded (from cache if available)");
    };

    return { unitOptions, handleRefresh, handleNormalLoad };
}

// ============================================================================
// EXAMPLE 5: Integration dengan EnhancedFormSelect
// ============================================================================
export function example5_EnhancedFormSelectIntegration() {
    const { unitOptions, loadUnits } = useSelectOptions();

    onMounted(async () => {
        await loadUnits();
    });

    // Handler untuk event item-added dari EnhancedFormSelect
    const handleUnitAdded = async () => {
        // Composable sudah handle add di internal
        // Cukup force refresh untuk ensure consistency
        await loadUnits(true);
    };

    // Template usage:
    // <EnhancedFormSelect
    //     v-model="form.unit_id"
    //     label="Unit"
    //     :options="unitOptions"
    //     :allow-add="true"
    //     add-endpoint="/units"
    //     add-field="name"
    //     @item-added="handleUnitAdded"
    // />

    return { unitOptions, handleUnitAdded };
}

// ============================================================================
// EXAMPLE 6: Using Loading States
// ============================================================================
export function example6_LoadingStates() {
    const { unitOptions, loadUnits, loadingUnits } = useSelectOptions();

    onMounted(async () => {
        await loadUnits();
    });

    // Template usage:
    // <button :disabled="loadingUnits" @click="loadUnits(true)">
    //     <span v-if="loadingUnits">Loading...</span>
    //     <span v-else>Refresh Units</span>
    // </button>
    //
    // <select :disabled="loadingUnits">
    //     <option v-if="loadingUnits">Loading units...</option>
    //     <option v-for="unit in unitOptions" :key="unit.value" :value="unit.value">
    //         {{ unit.label }}
    //     </option>
    // </select>

    return { unitOptions, loadUnits, loadingUnits };
}

// ============================================================================
// EXAMPLE 7: Cache Management
// ============================================================================
export function example7_CacheManagement() {
    const {
        unitOptions,
        loadUnits,
        clearCache,
        isCacheValid
    } = useSelectOptions();

    const handleClearUnitCache = () => {
        // Clear specific cache
        clearCache("units");
        console.log("Unit cache cleared");
    };

    const handleClearAllCache = () => {
        // Clear all caches
        clearCache("all");
        console.log("All caches cleared");
    };

    const checkCacheStatus = () => {
        // Check cache validity
        console.log("Cache status:", isCacheValid.value);
        // Output: { units: true, categories: false, locations: true }
    };

    return {
        unitOptions,
        loadUnits,
        handleClearUnitCache,
        handleClearAllCache,
        checkCacheStatus,
    };
}

// ============================================================================
// EXAMPLE 8: Complete Product Form
// ============================================================================
export function example8_CompleteProductForm() {
    const {
        unitOptions,
        categoryOptions,
        productTypeOptions,
        statusOptions,
        loadUnits,
        loadCategories,
    } = useSelectOptions();

    const form = {
        product_name: "",
        unit_id: null,
        category_id: null,
        product_type: "finished_goods",
        is_active: true,
    };

    onMounted(async () => {
        // Load semua options yang dibutuhkan
        await Promise.all([
            loadUnits(),
            loadCategories(),
        ]);
    });

    // Template usage:
    // <form>
    //   <input v-model="form.product_name" placeholder="Product Name" />
    //
    //   <select v-model="form.unit_id">
    //     <option v-for="unit in unitOptions" :key="unit.value" :value="unit.value">
    //       {{ unit.label }}
    //     </option>
    //   </select>
    //
    //   <select v-model="form.category_id">
    //     <option :value="null">-- Select Category --</option>
    //     <option v-for="cat in categoryOptions" :key="cat.value" :value="cat.value">
    //       {{ cat.label }}
    //     </option>
    //   </select>
    //
    //   <select v-model="form.product_type">
    //     <option v-for="type in productTypeOptions" :key="type.value" :value="type.value">
    //       {{ type.label }}
    //     </option>
    //   </select>
    //
    //   <select v-model="form.is_active">
    //     <option v-for="status in statusOptions" :key="status.value" :value="status.value">
    //       {{ status.label }}
    //     </option>
    //   </select>
    // </form>

    return {
        form,
        unitOptions,
        categoryOptions,
        productTypeOptions,
        statusOptions,
    };
}

// ============================================================================
// EXAMPLE 9: Error Handling
// ============================================================================
export function example9_ErrorHandling() {
    const { unitOptions, loadUnits } = useSelectOptions();

    const handleLoadWithErrorHandling = async () => {
        try {
            await loadUnits();
            console.log("Units loaded successfully");
        } catch (error) {
            // Error sudah di-handle oleh composable (notification ditampilkan)
            // Tapi bisa handle custom logic di sini jika diperlukan
            console.error("Custom error handling:", error);

            // Misalnya: retry atau fallback
            if (confirm("Failed to load units. Retry?")) {
                await handleLoadWithErrorHandling();
            }
        }
    };

    onMounted(async () => {
        await handleLoadWithErrorHandling();
    });

    return { unitOptions, handleLoadWithErrorHandling };
}

// ============================================================================
// EXAMPLE 10: Singleton Pattern Demonstration
// ============================================================================
export function example10_SingletonPattern() {
    // Component A
    const componentA = () => {
        const { unitOptions, loadUnits } = useSelectOptions();

        onMounted(async () => {
            await loadUnits();
            console.log("Component A loaded units:", unitOptions.value.length);
        });

        return { unitOptions };
    };

    // Component B (mounted setelah Component A)
    const componentB = () => {
        const { unitOptions } = useSelectOptions();

        onMounted(() => {
            // unitOptions sudah terisi dari Component A!
            // Tidak perlu load lagi
            console.log("Component B has units:", unitOptions.value.length);
        });

        return { unitOptions };
    };

    return { componentA, componentB };
}

// ============================================================================
// EXAMPLE 11: Using loadAllOptions Helper
// ============================================================================
export function example11_LoadAllOptionsHelper() {
    const {
        unitOptions,
        categoryOptions,
        locationOptions,
        loadAllOptions,
    } = useSelectOptions();

    onMounted(async () => {
        // Load semua options sekaligus dengan satu method
        await loadAllOptions();

        // Semua options sekarang terisi
        console.log("All options loaded!");
        console.log("Units:", unitOptions.value.length);
        console.log("Categories:", categoryOptions.value.length);
        console.log("Locations:", locationOptions.value.length);
    });

    return { unitOptions, categoryOptions, locationOptions };
}

// ============================================================================
// EXAMPLE 12: Refresh All Options
// ============================================================================
export function example12_RefreshAllOptions() {
    const {
        unitOptions,
        categoryOptions,
        locationOptions,
        refreshAllOptions,
    } = useSelectOptions();

    const handleRefreshAll = async () => {
        // Clear semua cache dan reload dari backend
        await refreshAllOptions();
        console.log("All options refreshed from backend");
    };

    return {
        unitOptions,
        categoryOptions,
        locationOptions,
        handleRefreshAll,
    };
}

// ============================================================================
// USAGE NOTES:
// ============================================================================
//
// 1. Import composable:
//    import { useSelectOptions } from "@/composables/useSelectOptions";
//
// 2. Destructure apa yang diperlukan:
//    const { unitOptions, loadUnits } = useSelectOptions();
//
// 3. Load data di onMounted:
//    onMounted(async () => { await loadUnits(); });
//
// 4. Gunakan options di template:
//    <option v-for="unit in unitOptions" :key="unit.value" :value="unit.value">
//
// 5. Force refresh jika diperlukan:
//    await loadUnits(true);
//
// ============================================================================
