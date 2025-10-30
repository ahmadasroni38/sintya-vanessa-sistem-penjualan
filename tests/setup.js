import { beforeAll } from "vitest";

// Global test utilities
global.createMockMutation = (overrides = {}) => ({
    id: 1,
    transaction_number: "SM-001",
    transaction_date: "2024-01-01",
    from_location_id: 1,
    to_location_id: 2,
    status: "draft",
    ...overrides,
});
