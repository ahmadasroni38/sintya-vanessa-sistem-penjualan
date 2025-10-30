// Comprehensive Warehouse Dummy Data Generator
export const warehouseDataGenerator = {
    // Generate Products
    generateProducts(count = 50) {
        const categories = [
            "Electronics",
            "Furniture",
            "Office Supplies",
            "Raw Materials",
            "Tools",
            "Consumables",
            "Equipment",
        ];
        const units = [
            "pcs",
            "box",
            "kg",
            "meter",
            "liter",
            "set",
            "pack",
            "roll",
        ];
        const productNames = [
            "Laptop Computer",
            "Office Chair",
            "Desk Lamp",
            "Printer Paper",
            "Mouse Pad",
            "Keyboard",
            "Monitor",
            "Desk Organizer",
            "Filing Cabinet",
            "Whiteboard",
            "Marker Set",
            "Binder Clips",
            "Stapler",
            "Paper Clips",
            "Envelope",
            "Calculator",
            "Pen Set",
            "Notebook",
            "Folder",
            "Scissors",
            "Tape Dispenser",
            "Highlighter",
            "Ruler",
            "Eraser",
            "Pencil Case",
            "USB Cable",
            "Power Adapter",
            "Webcam",
            "Microphone",
            "Speakers",
            "External Hard Drive",
            "Flash Drive",
            "HDMI Cable",
            "Network Cable",
            "Surge Protector",
            "Desk",
            "Table",
            "Shelf",
            "Cabinet",
            "Drawer",
            "Waste Bin",
            "Clock",
            "Calendar",
            "Phone Stand",
            "Cable Management",
        ];

        return Array.from({ length: count }, (_, i) => {
            const name =
                productNames[i % productNames.length] +
                ` ${Math.floor(i / productNames.length) + 1}`;
            const category =
                categories[Math.floor(Math.random() * categories.length)];
            const unit = units[Math.floor(Math.random() * units.length)];

            return {
                id: i + 1,
                code: `PRD${String(i + 1).padStart(5, "0")}`,
                name: name,
                description: `High-quality ${name.toLowerCase()} for office and warehouse use. Features include modern design and durable construction.`,
                category: category,
                unit: unit,
                price: Math.floor(Math.random() * 5000000) + 10000, // 10K - 5M IDR
                min_stock: Math.floor(Math.random() * 50) + 5,
                max_stock: Math.floor(Math.random() * 500) + 100,
                status: Math.random() > 0.1 ? "active" : "inactive",
                created_at: new Date(
                    Date.now() - Math.random() * 365 * 24 * 60 * 60 * 1000
                ).toISOString(),
                updated_at: new Date(
                    Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000
                ).toISOString(),
            };
        });
    },

    // Generate Locations
    generateLocations(count = 10) {
        const types = ["warehouse", "store", "production", "transit", "office"];
        const locationNames = [
            "Main Warehouse",
            "Secondary Storage",
            "Retail Store A",
            "Production Floor",
            "Transit Hub",
            "Office Supply Room",
            "Backup Warehouse",
            "Distribution Center",
            "Regional Store",
            "Factory Outlet",
            "Service Center",
            "Showroom",
            "Receiving Area",
            "Shipping Dock",
            "Quality Control",
            "Packaging Area",
        ];

        return Array.from({ length: count }, (_, i) => {
            const name = locationNames[i % locationNames.length];
            const type = types[Math.floor(Math.random() * types.length)];

            return {
                id: i + 1,
                code: `LOC${String(i + 1).padStart(3, "0")}`,
                name: name,
                address: `Jl. Warehouse No. ${
                    i + 1
                }, Industrial Area, Jakarta ${10000 + i}`,
                type: type,
                capacity: Math.floor(Math.random() * 10000) + 1000,
                status: Math.random() > 0.1 ? "active" : "inactive",
                created_at: new Date(
                    Date.now() - Math.random() * 365 * 24 * 60 * 60 * 1000
                ).toISOString(),
                updated_at: new Date(
                    Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000
                ).toISOString(),
            };
        });
    },

    // Generate Stock In transactions
    generateStockIn(products, locations, count = 30) {
        const suppliers = [
            "PT. Supplier Utama",
            "CV. Distributorindo",
            "PT. Global Supply",
            "PT. Local Vendor",
            "CV. Import Master",
            "PT. Wholesale Center",
            "PT. Direct Import",
            "CV. Trading Company",
            "PT. Manufacturing Co",
            "PT. Retail Supplier",
        ];

        const statuses = ["draft", "approved", "rejected"];
        const references = ["PO", "GRN", "INV", "REC", "PUR"];

        return Array.from({ length: count }, (_, i) => {
            const date = new Date(
                Date.now() - Math.random() * 90 * 24 * 60 * 60 * 1000
            );
            const location =
                locations[Math.floor(Math.random() * locations.length)];
            const supplier =
                suppliers[Math.floor(Math.random() * suppliers.length)];
            const status =
                statuses[Math.floor(Math.random() * statuses.length)];
            const referenceType =
                references[Math.floor(Math.random() * references.length)];

            const detailCount = Math.floor(Math.random() * 5) + 1;
            const details = Array.from({ length: detailCount }, (_, j) => {
                const product =
                    products[Math.floor(Math.random() * products.length)];
                const quantity = Math.floor(Math.random() * 100) + 1;
                const unitPrice = product.price * (0.9 + Math.random() * 0.2); // Price with some variation

                return {
                    id: i * detailCount + j + 1,
                    product_id: product.id,
                    product_name: product.name,
                    product_code: product.code,
                    quantity: quantity,
                    unit_price: unitPrice,
                    total_price: quantity * unitPrice,
                };
            });

            return {
                id: i + 1,
                code: `IN${String(i + 1).padStart(6, "0")}`,
                date: date.toISOString().split("T")[0],
                location_id: location.id,
                location_name: location.name,
                supplier: supplier,
                reference: `${referenceType}${String(
                    Math.floor(Math.random() * 999999) + 1
                ).padStart(6, "0")}`,
                status: status,
                notes: `Stock in transaction ${i + 1} - ${
                    status === "approved"
                        ? "Approved and processed"
                        : status === "rejected"
                        ? "Rejected due to quality issues"
                        : "Pending approval"
                }`,
                total_items: detailCount,
                total_value: details.reduce((sum, d) => sum + d.total_price, 0),
                created_by: `User ${Math.floor(Math.random() * 5) + 1}`,
                created_at: date.toISOString(),
                updated_at: new Date(
                    date.getTime() + Math.random() * 7 * 24 * 60 * 60 * 1000
                ).toISOString(),
                details: details,
            };
        });
    },

    // Generate Stock Mutations
    generateStockMutations(products, locations, count = 25) {
        const statuses = ["draft", "approved", "completed", "rejected"];
        const reasons = [
            "Stock transfer request",
            "Inventory balancing",
            "Seasonal movement",
            "Store replenishment",
            "Warehouse consolidation",
            "Emergency transfer",
            "Regular distribution",
            "Promotional stock",
            "New store setup",
            "Overstock relief",
        ];

        return Array.from({ length: count }, (_, i) => {
            const date = new Date(
                Date.now() - Math.random() * 90 * 24 * 60 * 60 * 1000
            );
            const fromLocation =
                locations[Math.floor(Math.random() * locations.length)];
            let toLocation =
                locations[Math.floor(Math.random() * locations.length)];

            // Ensure different locations
            while (toLocation.id === fromLocation.id) {
                toLocation =
                    locations[Math.floor(Math.random() * locations.length)];
            }

            const status =
                statuses[Math.floor(Math.random() * statuses.length)];
            const reason = reasons[Math.floor(Math.random() * reasons.length)];

            const detailCount = Math.floor(Math.random() * 5) + 1;
            const details = Array.from({ length: detailCount }, (_, j) => {
                const product =
                    products[Math.floor(Math.random() * products.length)];
                const quantity = Math.floor(Math.random() * 50) + 1;

                return {
                    id: i * detailCount + j + 1,
                    product_id: product.id,
                    product_name: product.name,
                    product_code: product.code,
                    quantity: quantity,
                };
            });

            return {
                id: i + 1,
                code: `MT${String(i + 1).padStart(6, "0")}`,
                date: date.toISOString().split("T")[0],
                from_location_id: fromLocation.id,
                from_location_name: fromLocation.name,
                to_location_id: toLocation.id,
                to_location_name: toLocation.name,
                status: status,
                reason: reason,
                notes: `Stock mutation ${i + 1} - ${reason}`,
                total_items: detailCount,
                created_by: `User ${Math.floor(Math.random() * 5) + 1}`,
                created_at: date.toISOString(),
                updated_at: new Date(
                    date.getTime() + Math.random() * 7 * 24 * 60 * 60 * 1000
                ).toISOString(),
                details: details,
            };
        });
    },

    // Generate Stock Adjustments
    generateStockAdjustments(products, locations, count = 20) {
        const statuses = ["draft", "approved", "rejected"];
        const types = ["increase", "decrease"];
        const reasons = [
            "Damaged goods",
            "Lost items",
            "Found items",
            "Counting error",
            "System correction",
            "Quality control",
            "Expired items",
            "Theft",
            "Return to supplier",
            "Customer return",
            "Sample usage",
            "Testing",
        ];

        return Array.from({ length: count }, (_, i) => {
            const date = new Date(
                Date.now() - Math.random() * 90 * 24 * 60 * 60 * 1000
            );
            const location =
                locations[Math.floor(Math.random() * locations.length)];
            const type = types[Math.floor(Math.random() * types.length)];
            const status =
                statuses[Math.floor(Math.random() * statuses.length)];
            const reason = reasons[Math.floor(Math.random() * reasons.length)];

            const detailCount = Math.floor(Math.random() * 5) + 1;
            const details = Array.from({ length: detailCount }, (_, j) => {
                const product =
                    products[Math.floor(Math.random() * products.length)];
                const systemQuantity = Math.floor(Math.random() * 100) + 10;
                let actualQuantity;

                if (type === "increase") {
                    actualQuantity =
                        systemQuantity + Math.floor(Math.random() * 20) + 1;
                } else {
                    actualQuantity = Math.max(
                        0,
                        systemQuantity - Math.floor(Math.random() * 20) - 1
                    );
                }

                return {
                    id: i * detailCount + j + 1,
                    product_id: product.id,
                    product_name: product.name,
                    product_code: product.code,
                    system_quantity: systemQuantity,
                    actual_quantity: actualQuantity,
                    adjustment_quantity: actualQuantity - systemQuantity,
                    reason: `${reason} - item ${j + 1}`,
                };
            });

            return {
                id: i + 1,
                code: `ADJ${String(i + 1).padStart(6, "0")}`,
                date: date.toISOString().split("T")[0],
                location_id: location.id,
                location_name: location.name,
                type: type,
                status: status,
                reason: reason,
                notes: `Stock adjustment ${i + 1} - ${reason}`,
                total_items: detailCount,
                created_by: `User ${Math.floor(Math.random() * 5) + 1}`,
                created_at: date.toISOString(),
                updated_at: new Date(
                    date.getTime() + Math.random() * 7 * 24 * 60 * 60 * 1000
                ).toISOString(),
                details: details,
            };
        });
    },

    // Generate Stock Opnames
    generateStockOpnames(products, locations, count = 15) {
        const statuses = [
            "draft",
            "in_progress",
            "completed",
            "approved",
            "rejected",
        ];
        const periods = [
            "2024-01",
            "2024-02",
            "2024-03",
            "2024-04",
            "2024-05",
            "2024-06",
            "2024-07",
            "2024-08",
            "2024-09",
            "2024-10",
            "2024-11",
            "2024-12",
        ];

        return Array.from({ length: count }, (_, i) => {
            const date = new Date(
                Date.now() - Math.random() * 90 * 24 * 60 * 60 * 1000
            );
            const location =
                locations[Math.floor(Math.random() * locations.length)];
            const period = periods[Math.floor(Math.random() * periods.length)];
            const status =
                statuses[Math.floor(Math.random() * statuses.length)];

            const detailCount = Math.floor(Math.random() * 20) + 5;
            const details = Array.from({ length: detailCount }, (_, j) => {
                const product =
                    products[Math.floor(Math.random() * products.length)];
                const systemQuantity = Math.floor(Math.random() * 100) + 10;
                const variance = Math.floor(Math.random() * 21) - 10; // -10 to +10
                const countedQuantity = Math.max(0, systemQuantity + variance);

                return {
                    id: i * detailCount + j + 1,
                    product_id: product.id,
                    product_name: product.name,
                    product_code: product.code,
                    system_quantity: systemQuantity,
                    counted_quantity: countedQuantity,
                    variance: countedQuantity - systemQuantity,
                    notes:
                        variance !== 0
                            ? `Counting variance detected: ${
                                  variance > 0 ? "+" : ""
                              }${variance}`
                            : "Count matches system",
                };
            });

            const varianceItems = details.filter(
                (d) => d.variance !== 0
            ).length;

            return {
                id: i + 1,
                code: `OP${String(i + 1).padStart(6, "0")}`,
                period: period,
                date: date.toISOString().split("T")[0],
                location_id: location.id,
                location_name: location.name,
                status: status,
                notes: `Stock opname ${i + 1} for ${period} - ${
                    status === "approved"
                        ? "Approved and finalized"
                        : status === "completed"
                        ? "Completed, pending approval"
                        : status
                }`,
                total_items: detailCount,
                variance_items: varianceItems,
                created_by: `User ${Math.floor(Math.random() * 5) + 1}`,
                created_at: date.toISOString(),
                updated_at: new Date(
                    date.getTime() + Math.random() * 7 * 24 * 60 * 60 * 1000
                ).toISOString(),
                details: details,
            };
        });
    },

    // Generate Stock Cards
    generateStockCards(products, locations, count = 100) {
        const transactionTypes = [
            "stock_in",
            "stock_out",
            "mutation_in",
            "mutation_out",
            "adjustment_in",
            "adjustment_out",
            "opname_in",
            "opname_out",
        ];

        const references = ["IN", "OUT", "MT", "ADJ", "OP"];
        const notes = [
            "Regular transaction",
            "Urgent request",
            "Scheduled movement",
            "Quality check",
            "Inventory adjustment",
            "Counting result",
            "Transfer complete",
            "Return processed",
            "Damage reported",
            "System update",
        ];

        return Array.from({ length: count }, (_, i) => {
            const date = new Date(
                Date.now() - Math.random() * 365 * 24 * 60 * 60 * 1000
            );
            const product =
                products[Math.floor(Math.random() * products.length)];
            const location =
                locations[Math.floor(Math.random() * locations.length)];
            const transactionType =
                transactionTypes[
                    Math.floor(Math.random() * transactionTypes.length)
                ];
            const referenceType =
                references[Math.floor(Math.random() * references.length)];
            const note = notes[Math.floor(Math.random() * notes.length)];

            let quantityIn = 0;
            let quantityOut = 0;

            if (transactionType.includes("_in")) {
                quantityIn = Math.floor(Math.random() * 100) + 1;
            } else {
                quantityOut = Math.floor(Math.random() * 100) + 1;
            }

            const balance = Math.floor(Math.random() * 1000) + 100;

            return {
                id: i + 1,
                date: date.toISOString().split("T")[0],
                product_id: product.id,
                product_name: product.name,
                product_code: product.code,
                location_id: location.id,
                location_name: location.name,
                transaction_type: transactionType,
                reference: `${referenceType}${String(
                    Math.floor(Math.random() * 999999) + 1
                ).padStart(6, "0")}`,
                quantity_in: quantityIn,
                quantity_out: quantityOut,
                balance: balance,
                notes: `${note} - ${transactionType.replace("_", " ")}`,
                created_by: `User ${Math.floor(Math.random() * 5) + 1}`,
                created_at: date.toISOString(),
            };
        });
    },

    // Generate complete warehouse dataset
    generateCompleteDataset() {
        const products = this.generateProducts(50);
        const locations = this.generateLocations(10);
        const stockIn = this.generateStockIn(products, locations, 30);
        const mutations = this.generateStockMutations(products, locations, 25);
        const adjustments = this.generateStockAdjustments(
            products,
            locations,
            20
        );
        const stockOpnames = this.generateStockOpnames(products, locations, 15);
        const stockCards = this.generateStockCards(products, locations, 100);

        return {
            products,
            locations,
            stockIn,
            mutations,
            adjustments,
            stockOpnames,
            stockCards,
        };
    },

    // Generate stock levels for all products and locations
    generateStockLevels(products, locations) {
        const stockLevels = {};

        locations.forEach((location) => {
            stockLevels[location.id] = {};
            products.forEach((product) => {
                stockLevels[location.id][product.id] =
                    Math.floor(Math.random() * 1000) + 100;
            });
        });

        return stockLevels;
    },
};

export default warehouseDataGenerator;
