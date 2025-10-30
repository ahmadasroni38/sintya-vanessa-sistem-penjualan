import { describe, it, expect, beforeEach, vi, afterEach } from "vitest";
import { mount } from "@vue/test-utils";
import { createApp } from "vue";
import { createPinia } from "pinia";
import Mutasi from "@/views/Dashboard/Warehouse/Mutasi.vue";
import { useStockMutation } from "@/composables/useStockMutation";

// Mock the composable
vi.mock("@/composables/useStockMutation", () => ({
    useStockMutation: vi.fn(),
}));

// Mock components
vi.mock("@/components/UI/DataTable.vue", () => ({
    default: {
        name: "DataTable",
        template: '<div data-testid="data-table"><slot /></div>',
    },
}));

vi.mock("@/components/Overlays/Modal.vue", () => ({
    default: {
        name: "Modal",
        template:
            '<div v-if="isOpen" data-testid="modal"><slot name="header" /><slot /></div>',
        props: ["isOpen", "title", "size"],
    },
}));

vi.mock("@/components/Overlays/ConfirmationModal.vue", () => ({
    default: {
        name: "ConfirmationModal",
        template: "<div></div>",
    },
}));

vi.mock("@/components/Warehouse/StockMutationStats.vue", () => ({
    default: {
        name: "StockMutationStats",
        template: '<div data-testid="stats"></div>',
    },
}));

vi.mock("@/components/Warehouse/StockMutationFilters.vue", () => ({
    default: {
        name: "StockMutationFilters",
        template: '<div data-testid="filters"></div>',
    },
}));

vi.mock("@/components/Warehouse/StockMutationFormModal.vue", () => ({
    default: {
        name: "StockMutationFormModal",
        template: "<div></div>",
    },
}));

vi.mock("@/components/Warehouse/StockMutationActions.vue", () => ({
    default: {
        name: "StockMutationActions",
        template:
            '<div data-testid="actions"><button @click="$emit(\'view\', item)" data-testid="view-btn">View</button></div>',
        props: ["item"],
        emits: ["view"],
    },
}));

vi.mock("@/components/Warehouse/StockMutationDetails.vue", () => ({
    default: {
        name: "StockMutationDetails",
        template: '<div data-testid="details">Mutation Details</div>',
    },
}));

describe("Mutasi.vue - viewDetails functionality", () => {
    let wrapper;
    let mockComposable;
    let mockNotificationStore;

    beforeEach(() => {
        // Mock notification store
        mockNotificationStore = {
            error: vi.fn(),
            success: vi.fn(),
            info: vi.fn(),
        };

        vi.mock("@/stores/notification", () => ({
            useNotificationStore: () => mockNotificationStore,
        }));

        // Mock composable
        mockComposable = {
            stockMutations: [],
            loading: false,
            pagination: {},
            statistics: {},
            options: {},
            fetchStockMutations: vi.fn().mockResolvedValue([]),
            fetchStockMutation: vi.fn(),
            createStockMutation: vi.fn(),
            updateStockMutation: vi.fn(),
            deleteStockMutation: vi.fn(),
            submitStockMutation: vi.fn(),
            approveStockMutation: vi.fn(),
            completeStockMutation: vi.fn(),
            cancelStockMutation: vi.fn(),
            fetchStatistics: vi.fn(),
            fetchOptions: vi.fn(),
            fetchLocations: vi.fn(),
            getStatusLabel: vi.fn(),
            getStatusClass: vi.fn(),
            formatNumber: vi.fn(),
            formatDate: vi.fn(),
            formatDateTime: vi.fn(),
        };

        useStockMutation.mockReturnValue(mockComposable);

        const app = createApp({});
        app.use(createPinia());

        wrapper = mount(Mutasi, {
            global: {
                plugins: [app],
                stubs: {
                    "router-link": true,
                    "router-view": true,
                },
            },
        });
    });

    afterEach(() => {
        vi.clearAllMocks();
    });

    describe("viewDetails function", () => {
        it("should open modal and fetch mutation details successfully", async () => {
            const mockMutation = createMockMutation();
            const mockDetails = { ...mockMutation, details: [] };

            mockComposable.fetchStockMutation.mockResolvedValue(mockDetails);

            // Trigger viewDetails
            await wrapper.vm.viewDetails(mockMutation);

            // Check modal is opened
            expect(wrapper.vm.showDetailsModal).toBe(true);
            expect(wrapper.vm.loadingDetails).toBe(false);

            // Check API was called
            expect(mockComposable.fetchStockMutation).toHaveBeenCalledWith(
                mockMutation.id
            );

            // Check data is set
            expect(wrapper.vm.selectedMutation).toEqual(mockDetails);
        });

        it("should handle network errors with retry mechanism", async () => {
            const mockMutation = createMockMutation();
            const networkError = new Error("Network Error");
            networkError.response = { status: 500 };

            mockComposable.fetchStockMutation
                .mockRejectedValueOnce(networkError) // First call fails
                .mockRejectedValueOnce(networkError); // Retry also fails

            // Mock timers for retry delays
            vi.useFakeTimers();

            // Trigger viewDetails
            const viewDetailsPromise = wrapper.vm.viewDetails(mockMutation);

            // Fast-forward through retry delay
            await vi.runAllTimersAsync();
            await viewDetailsPromise;

            // Check modal was closed on final failure
            expect(wrapper.vm.showDetailsModal).toBe(false);
            expect(wrapper.vm.loadingDetails).toBe(false);

            // Check error notification was shown
            expect(mockNotificationStore.error).toHaveBeenCalledWith(
                "Failed to load mutation details. Please check your connection and try again."
            );

            // Check API was called twice (initial + 1 retry)
            expect(mockComposable.fetchStockMutation).toHaveBeenCalledTimes(2);
            expect(mockComposable.fetchStockMutation).toHaveBeenCalledWith(
                mockMutation.id
            );

            vi.useRealTimers();
        });

        it("should handle 404 errors gracefully", async () => {
            const mockMutation = createMockMutation();
            const notFoundError = new Error("Not Found");
            notFoundError.response = {
                status: 404,
                data: { message: "Mutation not found" },
            };

            mockComposable.fetchStockMutation.mockRejectedValue(notFoundError);

            await wrapper.vm.viewDetails(mockMutation);

            expect(wrapper.vm.showDetailsModal).toBe(false);
            expect(mockNotificationStore.error).toHaveBeenCalledWith(
                "Mutation not found"
            );
        });

        it("should succeed after retry on network failure", async () => {
            const mockMutation = createMockMutation();
            const mockDetails = { ...mockMutation, details: [] };

            // Fail first attempt, succeed on retry
            mockComposable.fetchStockMutation
                .mockRejectedValueOnce(new Error("Network Error"))
                .mockResolvedValueOnce(mockDetails);

            vi.useFakeTimers();

            await wrapper.vm.viewDetails(mockMutation);
            await vi.runAllTimersAsync();

            expect(wrapper.vm.showDetailsModal).toBe(true);
            expect(wrapper.vm.loadingDetails).toBe(false);
            expect(wrapper.vm.selectedMutation).toEqual(mockDetails);
            expect(mockComposable.fetchStockMutation).toHaveBeenCalledTimes(2);

            vi.useRealTimers();
        });

        it("should show loading state during data fetch", async () => {
            const mockMutation = createMockMutation();
            const mockDetails = { ...mockMutation, details: [] };

            // Create a promise that we can control
            let resolvePromise;
            const fetchPromise = new Promise((resolve) => {
                resolvePromise = resolve;
            });

            mockComposable.fetchStockMutation.mockReturnValue(fetchPromise);

            // Start viewDetails
            const viewDetailsPromise = wrapper.vm.viewDetails(mockMutation);

            // Check loading state is set
            expect(wrapper.vm.showDetailsModal).toBe(true);
            expect(wrapper.vm.loadingDetails).toBe(true);
            expect(wrapper.vm.selectedMutation).toBe(null);

            // Resolve the fetch
            resolvePromise(mockDetails);
            await viewDetailsPromise;

            // Check loading state is cleared
            expect(wrapper.vm.loadingDetails).toBe(false);
            expect(wrapper.vm.selectedMutation).toEqual(mockDetails);
        });

        it("should reset modal state when closed", () => {
            wrapper.vm.showDetailsModal = true;
            wrapper.vm.selectedMutation = { id: 1 };
            wrapper.vm.loadingDetails = true;

            wrapper.vm.closeDetailsModal();

            expect(wrapper.vm.showDetailsModal).toBe(false);
            expect(wrapper.vm.selectedMutation).toBe(null);
            expect(wrapper.vm.loadingDetails).toBe(false);
        });
    });

    describe("Modal rendering", () => {
        it("should show loading indicator when loadingDetails is true", async () => {
            wrapper.vm.showDetailsModal = true;
            wrapper.vm.loadingDetails = true;
            wrapper.vm.selectedMutation = null;

            await wrapper.vm.$nextTick();

            const modal = wrapper.find('[data-testid="modal"]');
            expect(modal.exists()).toBe(true);

            const loadingIndicator = modal.find(".animate-spin");
            expect(loadingIndicator.exists()).toBe(true);

            const loadingText = modal.text();
            expect(loadingText).toContain("Loading mutation details...");
        });

        it("should show mutation details when data is loaded", async () => {
            const mockDetails = createMockMutation();
            wrapper.vm.showDetailsModal = true;
            wrapper.vm.loadingDetails = false;
            wrapper.vm.selectedMutation = mockDetails;

            await wrapper.vm.$nextTick();

            const modal = wrapper.find('[data-testid="modal"]');
            expect(modal.exists()).toBe(true);

            const details = modal.find('[data-testid="details"]');
            expect(details.exists()).toBe(true);
        });

        it("should show empty state when no data available", async () => {
            wrapper.vm.showDetailsModal = true;
            wrapper.vm.loadingDetails = false;
            wrapper.vm.selectedMutation = null;

            await wrapper.vm.$nextTick();

            const modal = wrapper.find('[data-testid="modal"]');
            expect(modal.exists()).toBe(true);

            const emptyState = modal.text();
            expect(emptyState).toContain("No mutation data available");
        });
    });
});
