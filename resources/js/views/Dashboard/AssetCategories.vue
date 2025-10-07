<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Asset Categories
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Manage asset categories and their hierarchical structure
                </p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <TagIcon
                            class="w-6 h-6 text-blue-600 dark:text-blue-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Categories
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ totalCategories }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div
                        class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg"
                    >
                        <CheckCircleIcon
                            class="w-6 h-6 text-green-600 dark:text-green-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Active Categories
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ activeCategories }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div
                        class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg"
                    >
                        <FolderIcon
                            class="w-6 h-6 text-purple-600 dark:text-purple-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Root Categories
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ rootCategories }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
            >
                <div class="flex items-center">
                    <div
                        class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg"
                    >
                        <PlusIcon
                            class="w-6 h-6 text-orange-600 dark:text-orange-400"
                        />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            New This Month
                        </p>
                        <p
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            {{ newCategories }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <DataTable
            title="Asset Categories"
            description="A list of all asset categories in your system including their hierarchy and status."
            :data="categories"
            :columns="columns"
            :loading="loading"
            :selectable="true"
            :show-actions="true"
            :show-add-button="true"
            add-button-text="Create Category"
            :show-filters="false"
            :show-bulk-actions="true"
            :show-refresh="true"
            :refresh-loading="refreshLoading"
            search-placeholder="Search categories..."
            empty-title="No categories found"
            empty-description="Get started by creating your first asset category."
            @add="handleAddCategory"
            @edit="handleEditCategory"
            @delete="handleDeleteCategory"
            @bulk-action="handleBulkAction"
            @selection-change="handleSelectionChange"
            @refresh="handleRefreshCategories"
        >
            <!-- Custom Name Column with Hierarchy -->
            <template #column-name="{ item }">
                <div class="flex items-center">
                    <div
                        class="w-4 h-4 rounded-full mr-3 flex-shrink-0"
                        :style="{ backgroundColor: item.color }"
                    ></div>
                    <div
                        :style="{
                            paddingLeft: `${item.hierarchy_level * 20}px`,
                        }"
                    >
                        <p
                            class="text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{ item.name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ item.code }}
                        </p>
                    </div>
                </div>
            </template>

            <!-- Custom Parent Column -->
            <template #column-parent="{ item }">
                <div v-if="item.parent" class="flex items-center">
                    <div
                        class="w-3 h-3 rounded-full mr-2"
                        :style="{
                            backgroundColor: item.parent.color || '#6366F1',
                        }"
                    ></div>
                    <span class="text-sm text-gray-900 dark:text-white">
                        {{ item.parent.name }}
                    </span>
                </div>
                <span v-else class="text-sm text-gray-500 dark:text-gray-400">
                    Root Category
                </span>
            </template>

            <!-- Custom Status Column -->
            <template #column-status="{ item }">
                <div class="flex items-center">
                    <div
                        :class="[
                            'w-2 h-2 rounded-full mr-2',
                            item.is_active ? 'bg-green-500' : 'bg-red-500',
                        ]"
                    ></div>
                    <span
                        class="text-sm text-gray-900 dark:text-white capitalize"
                        >{{ item.is_active ? "Active" : "Inactive" }}</span
                    >
                </div>
            </template>

            <!-- Custom Children Count Column -->
            <template #column-children="{ item }">
                <span
                    :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        item.children_count > 0
                            ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400'
                            : 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
                    ]"
                >
                    {{ item.children_count }} child{{
                        item.children_count !== 1 ? "ren" : ""
                    }}
                </span>
            </template>

            <!-- Custom Actions -->
            <template #actions="{ item }">
                <div class="flex items-center justify-end gap-2">
                    <button
                        @click="handleEditCategory(item)"
                        class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200 dark:hover:text-green-400 dark:hover:bg-green-900/20"
                        title="Edit Category"
                    >
                        <PencilIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleToggleStatus(item)"
                        :class="[
                            'p-1.5 rounded-lg transition-colors duration-200',
                            item.is_active
                                ? 'text-gray-400 hover:text-orange-600 hover:bg-orange-50 dark:hover:text-orange-400 dark:hover:bg-orange-900/20'
                                : 'text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:text-blue-400 dark:hover:bg-blue-900/20',
                        ]"
                        :title="
                            item.is_active
                                ? 'Deactivate Category'
                                : 'Activate Category'
                        "
                    >
                        <PlayIcon v-if="!item.is_active" class="w-4 h-4" />
                        <PauseIcon v-else class="w-4 h-4" />
                    </button>
                    <button
                        @click="handleDeleteCategory(item)"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                        title="Delete Category"
                    >
                        <TrashIcon class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Create/Edit Category Modal -->
        <Modal
            :is-open="showCreateModal || showEditModal"
            @close="closeModals"
            size="lg"
        >
            <template #title>
                {{ showCreateModal ? "Create Category" : "Edit Category" }}
            </template>

            <form @submit.prevent="saveCategory" class="space-y-6">
                <!-- Basic Information -->
                <div class="space-y-5">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Basic Information
                    </h3>
                    <FormInput
                        v-model="categoryForm.name"
                        label="Name"
                        placeholder="Enter category name"
                        :error="getFieldError('name')"
                        required
                        @blur="handleFieldBlur('name')"
                    />
                    <FormInput
                        v-model="categoryForm.code"
                        label="Code"
                        placeholder="Enter category code (optional - auto-generated if empty)"
                        :error="getFieldError('code')"
                        @blur="handleFieldBlur('code')"
                    />
                    <FormTextarea
                        v-model="categoryForm.description"
                        label="Description"
                        placeholder="Enter category description"
                        :error="getFieldError('description')"
                        rows="3"
                        @blur="handleFieldBlur('description')"
                    />
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput
                            v-model="categoryForm.color"
                            label="Color"
                            type="color"
                            :error="getFieldError('color')"
                            @blur="handleFieldBlur('color')"
                        />
                        <!-- Parent Category Select -->
                        <div class="relative">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Parent Category
                            </label>
                            <select
                                v-model="categoryForm.parent_id"
                                data-hs-select='{
                                  "hasSearch": true,
                                  "searchPlaceholder": "Search parent categories...",
                                  "searchClasses": "block w-full sm:text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 before:absolute before:inset-0 before:z-1 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 py-1.5 sm:py-2 px-3",
                                  "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0 dark:bg-neutral-900",
                                  "placeholder": "Select parent category (optional)",
                                  "toggleTag": "<button type=\"button\" aria-expanded=\"false\"><span class=\"me-2\" data-icon></span><span class=\"text-gray-800 dark:text-neutral-200 \" data-title></span></button>",
                                  "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:focus:outline-hidden dark:focus:ring-1 dark:focus:ring-neutral-600",
                                  "dropdownClasses": "mt-2 max-h-72 pb-1 px-1 space-y-0.5 z-20 w-full bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-neutral-900 dark:border-neutral-700",
                                  "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-hidden focus:bg-gray-100 dark:bg-neutral-900 dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800",
                                  "optionTemplate": "<div><div class=\"flex items-center\"><div class=\"me-2\" data-icon></div><div class=\"text-gray-800 dark:text-neutral-200 \" data-title></div></div></div>",
                                  "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500 dark:text-neutral-500 \" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                                }'
                                class="hidden"
                                @blur="handleFieldBlur('parent_id')"
                            >
                                <option value="">
                                    Select parent category (optional)
                                </option>
                                <option
                                    v-for="parent in parentOptions"
                                    :key="parent.value"
                                    :value="parent.value"
                                    :data-hs-select-option="`{&quot;icon&quot;: &quot;<div class=\&quot;w-3 h-3 rounded-full\&quot; style=\&quot;background-color: ${getParentCategoryColor(
                                        parent.value
                                    )}\&quot;></div>&quot;}`"
                                >
                                    {{ parent.label }}
                                </option>
                            </select>
                            <p
                                v-if="getFieldError('parent_id')"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ getFieldError("parent_id") }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-8 flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3 space-y-3 space-y-reverse sm:space-y-0"
                >
                    <Button
                        @click="closeModals"
                        variant="secondary"
                        :disabled="saving"
                        class="w-full sm:w-auto"
                    >
                        Cancel
                    </Button>
                    <Button
                        type="submit"
                        :loading="saving"
                        :disabled="saving"
                        class="w-full sm:w-auto"
                    >
                        {{
                            showCreateModal
                                ? "Create Category"
                                : "Update Category"
                        }}
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- Confirmation Modal -->
        <ConfirmationModal
            :is-open="confirmationModal.isOpen"
            :title="confirmationModal.config.title"
            :message="confirmationModal.config.message"
            :description="confirmationModal.config.description"
            :confirm-text="confirmationModal.config.confirmText"
            :cancel-text="confirmationModal.config.cancelText"
            :loading="confirmationModal.loading"
            @confirm="confirmationModal.handleConfirm"
            @cancel="confirmationModal.handleCancel"
            @close="confirmationModal.handleClose"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import DataTable from "../../components/UI/DataTable.vue";
import Modal from "../../components/Overlays/Modal.vue";
import ConfirmationModal from "../../components/Overlays/ConfirmationModal.vue";
import FormInput from "../../components/Forms/FormInput.vue";
import FormTextarea from "../../components/Forms/FormTextarea.vue";
import Button from "../../components/Base/Button.vue";
import { useNotificationStore } from "@/stores/notification";
import { useConfirmationModalStore } from "@/stores/confirmationModal";
import { apiGet, apiPost, apiPut, apiDelete } from "@/utils/api";
import {
    TagIcon,
    CheckCircleIcon,
    FolderIcon,
    PlusIcon,
    PencilIcon,
    TrashIcon,
    PauseIcon,
    PlayIcon,
} from "@heroicons/vue/24/outline";

const notification = useNotificationStore();
const confirmationModal = useConfirmationModalStore();

// Reactive data
const loading = ref(false);
const refreshLoading = ref(false);
const categories = ref([]);
const selectedCategories = ref([]);
const parentOptions = ref([]);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedCategory = ref(null);
const saving = ref(false);

// Form data
const categoryForm = ref({
    name: "",
    code: "",
    description: "",
    color: "#6366F1",
    parent_id: null,
});

// Form validation
const formErrors = ref({});
const formTouched = ref({});

// Validation functions
const validateField = (field, value) => {
    const errors = [];

    switch (field) {
        case "name":
            if (!value || value.trim() === "") {
                errors.push("Name is required");
            } else if (value.length < 2) {
                errors.push("Name must be at least 2 characters");
            } else if (value.length > 255) {
                errors.push("Name must be less than 255 characters");
            }
            break;
        case "code":
            if (value && value.length > 20) {
                errors.push("Code must be less than 20 characters");
            }
            break;
        case "description":
            if (value && value.length > 1000) {
                errors.push("Description must be less than 1000 characters");
            }
            break;
        case "color":
            if (value && !/^#[0-9A-Fa-f]{6}$/.test(value)) {
                errors.push("Please enter a valid hex color");
            }
            break;
    }

    return errors;
};

const validateForm = () => {
    const errors = {};

    Object.keys(categoryForm.value).forEach((field) => {
        const fieldErrors = validateField(field, categoryForm.value[field]);
        if (fieldErrors.length > 0) {
            errors[field] = fieldErrors;
        }
    });

    formErrors.value = errors;
    return Object.keys(errors).length === 0;
};

const handleFieldBlur = (field) => {
    formTouched.value[field] = true;
    const errors = validateField(field, categoryForm.value[field]);
    formErrors.value[field] = errors;
};

const getFieldError = (field) => {
    return formTouched.value[field] && formErrors.value[field]
        ? formErrors.value[field][0]
        : "";
};

const getParentCategoryColor = (categoryId) => {
    const category = parentOptions.value.find(
        (cat) => cat.value === categoryId
    );
    if (category && category.color) {
        return category.color;
    }
    return "#6366F1"; // Default color
};

// Table columns configuration
const columns = [
    {
        key: "name",
        label: "Category",
        sortable: true,
    },
    {
        key: "parent",
        label: "Parent",
        sortable: false,
    },
    {
        key: "children",
        label: "Children",
        sortable: false,
    },
    {
        key: "status",
        label: "Status",
        sortable: false,
    },
    {
        key: "created_at",
        label: "Created",
        type: "date",
        sortable: true,
    },
];

// Computed properties
const totalCategories = computed(() => categories.value.length);
const activeCategories = computed(
    () => categories.value.filter((c) => c.is_active).length
);
const rootCategories = computed(
    () => categories.value.filter((c) => !c.parent_id).length
);
const newCategories = computed(() => {
    const oneMonthAgo = new Date();
    oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1);
    return categories.value.filter((c) => new Date(c.created_at) > oneMonthAgo)
        .length;
});

// Methods
const handleRefreshCategories = async () => {
    refreshLoading.value = true;
    try {
        await loadCategories();
        notification.success("Categories data refreshed successfully");
    } catch (error) {
        console.error("Refresh categories error:", error);
        notification.error("Failed to refresh categories data");
    } finally {
        refreshLoading.value = false;
    }
};

const saveCategory = async () => {
    if (!validateForm()) {
        notification.error("Please fix the errors in the form");
        return;
    }

    saving.value = true;
    try {
        const url = showCreateModal.value
            ? "asset-categories"
            : `asset-categories/${selectedCategory.value.id}`;

        const data = showCreateModal.value
            ? await apiPost(url, categoryForm.value)
            : await apiPut(url, categoryForm.value);

        if (data.success) {
            notification.success(
                `Category ${
                    showCreateModal.value ? "created" : "updated"
                } successfully`
            );
            closeModals();
            loadCategories();
            loadParentOptions();
        } else {
            if (data.errors) {
                formErrors.value = data.errors;
                notification.error("Please check the form for errors");
            } else {
                notification.error(data.message || "Failed to save category");
            }
        }
    } catch (error) {
        console.error("Save category error:", error);
        notification.error("Failed to save category. Please try again.");
    } finally {
        saving.value = false;
    }
};

const closeModals = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    selectedCategory.value = null;
    categoryForm.value = {
        name: "",
        code: "",
        description: "",
        color: "#6366F1",
        parent_id: null,
    };
    formErrors.value = {};
    formTouched.value = {};
};

const handleAddCategory = () => {
    showCreateModal.value = true;
    loadParentOptions();
};

const handleEditCategory = (category) => {
    selectedCategory.value = category;
    categoryForm.value = {
        name: category.name,
        code: category.code,
        description: category.description || "",
        color: category.color,
        parent_id: category.parent_id,
    };
    formErrors.value = {};
    formTouched.value = {};
    showEditModal.value = true;
    loadParentOptions(category);
};

const handleDeleteCategory = (category) => {
    confirmationModal.showModal({
        title: "Delete Category",
        message: `Are you sure you want to delete "${category.name}"?`,
        description:
            "This action cannot be undone. Make sure this category has no child categories or assets assigned to it.",
        confirmText: "Delete Category",
        cancelText: "Cancel",
        onConfirm: async () => {
            const data = await apiDelete(`asset-categories/${category.id}`);
            if (data.success) {
                notification.success("Category deleted successfully");
                loadCategories();
                loadParentOptions();
                return data;
            } else {
                throw new Error(data.message || "Failed to delete category");
            }
        },
        onSuccess: (result) => {
            console.log("Category deleted successfully:", result);
        },
        onError: (error) => {
            console.error("Failed to delete category:", error);
            notification.error(error.message || "Failed to delete category");
        },
    });
};

const handleBulkAction = (selectedItems) => {
    if (selectedItems.length === 0) {
        notification.warning("Please select categories to perform bulk action");
        return;
    }

    confirmationModal.showModal({
        title: "Bulk Delete Categories",
        message: `Are you sure you want to delete ${
            selectedItems.length
        } categor${selectedItems.length === 1 ? "y" : "ies"}?`,
        description:
            "This action cannot be undone. Make sure these categories have no child categories or assets assigned to them.",
        confirmText: "Delete Categories",
        cancelText: "Cancel",
        onConfirm: async () => {
            try {
                const deletePromises = selectedItems.map((item) =>
                    apiDelete(`asset-categories/${item.id}`)
                );
                await Promise.all(deletePromises);
                notification.success(
                    `${selectedItems.length} categor${
                        selectedItems.length === 1 ? "y" : "ies"
                    } deleted successfully`
                );
                loadCategories();
                loadParentOptions();
                return { success: true };
            } catch (error) {
                throw new Error("Failed to delete some categories");
            }
        },
        onSuccess: (result) => {
            console.log("Bulk delete completed:", result);
        },
        onError: (error) => {
            console.error("Bulk delete failed:", error);
            notification.error(error.message || "Failed to delete categories");
        },
    });
};

const handleSelectionChange = (selectedItems) => {
    selectedCategories.value = selectedItems;
    console.log("Selection changed:", selectedItems);
};

const handleToggleStatus = async (category) => {
    const newStatus = category.is_active ? "inactive" : "active";
    const action = newStatus === "active" ? "activate" : "deactivate";

    try {
        const data = await apiPost(
            `asset-categories/${category.id}/toggle-status`
        );

        if (data.success) {
            notification.success(`Category ${action}d successfully`);
            loadCategories();
        } else {
            notification.error(data.message || `Failed to ${action} category`);
        }
    } catch (error) {
        console.error(`Toggle status error:`, error);
        notification.error(`Failed to ${action} category`);
    }
};

const loadCategories = async () => {
    try {
        loading.value = true;
        const data = await apiGet("asset-categories");
        if (data.success) {
            categories.value = data.data;
        }
    } catch (error) {
        console.error("Error loading categories:", error);
        notification.error("Failed to load categories");
    } finally {
        loading.value = false;
    }
};

const loadParentOptions = async (excludeCategory = null) => {
    try {
        const url = excludeCategory
            ? `asset-categories-parent-options/${excludeCategory.id}`
            : "asset-categories-parent-options";

        const data = await apiGet(url);
        if (data.success) {
            parentOptions.value = [
                { value: null, label: "No Parent (Root Category)" },
                ...data.data.map((category) => ({
                    value: category.id,
                    label: category.full_path,
                })),
            ];
        }
    } catch (error) {
        console.error("Error loading parent options:", error);
        notification.error("Failed to load parent options");
    }
};

// Lifecycle
onMounted(() => {
    loadCategories();
    loadParentOptions();
});
</script>
