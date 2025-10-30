<template>
    <div class="space-y-2">
        <label
            v-if="label"
            :for="selectId"
            class="block text-sm font-medium text-gray-700 dark:text-gray-200"
        >
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <Listbox
            v-model="selectedValue"
            :disabled="disabled"
            @update:modelValue="handleValueChange"
        >
            <div class="relative">
                <ListboxButton
                    :id="selectId"
                    :class="buttonClasses"
                    @blur="$emit('blur', $event)"
                    @focus="$emit('focus', $event)"
                >
                    <span class="block truncate">
                        {{ selectedLabel || placeholderText }}
                    </span>
                    <span
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2"
                    >
                        <ChevronUpDownIcon
                            class="h-5 w-5 text-gray-400"
                            aria-hidden="true"
                        />
                    </span>
                </ListboxButton>

                <transition
                    leave-active-class="transition ease-in duration-100"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <ListboxOptions
                        class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm dark:bg-gray-800 dark:ring-gray-700"
                    >
                        <div
                            v-if="hasSearch"
                            class="sticky top-0 z-10 bg-white px-2 py-1 dark:bg-gray-800"
                        >
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search..."
                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                                @input="handleSearch"
                            />
                        </div>

                        <!-- Add New Item Option -->
                        <div
                            v-if="
                                allowAdd &&
                                searchQuery &&
                                !filteredOptions.some((opt) =>
                                    opt.label
                                        .toLowerCase()
                                        .includes(searchQuery.toLowerCase())
                                )
                            "
                            class="px-3 py-2 border-b border-gray-100 dark:border-gray-700"
                        >
                            <button
                                type="button"
                                @click="handleAddNewItem"
                                class="w-full text-left text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-2"
                            >
                                <PlusIcon class="h-4 w-4" />
                                Add "{{ searchQuery }}"
                            </button>
                        </div>

                        <ListboxOption
                            v-if="placeholder && !searchQuery"
                            :value="null"
                            class="relative cursor-default select-none py-2 pl-3 pr-9 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                        >
                            <span class="block truncate">{{
                                placeholder
                            }}</span>
                        </ListboxOption>
                        <ListboxOption
                            v-for="option in filteredOptions"
                            :key="option.value"
                            :value="option.value"
                            v-slot="{ active, selected }"
                            class="relative cursor-default select-none py-2 pl-3 pr-9"
                            :class="[
                                active
                                    ? 'bg-blue-100 text-blue-900 dark:bg-blue-900 dark:text-blue-100'
                                    : 'text-gray-900 dark:text-white',
                                selected ? 'bg-blue-50 dark:bg-blue-950' : '',
                            ]"
                        >
                            <div class="flex items-center">
                                <span
                                    v-if="option.icon"
                                    class="mr-2 flex-shrink-0"
                                >
                                    <component
                                        :is="option.icon"
                                        class="h-4 w-4"
                                    />
                                </span>
                                <span
                                    v-else-if="option.color"
                                    class="mr-2 h-3 w-3 rounded-full flex-shrink-0"
                                    :style="{ backgroundColor: option.color }"
                                ></span>
                                <span class="block truncate">{{
                                    option.label
                                }}</span>
                            </div>
                            <span
                                v-if="selected"
                                class="absolute inset-y-0 right-0 flex items-center pr-4"
                            >
                                <CheckIcon class="h-5 w-5" aria-hidden="true" />
                            </span>
                        </ListboxOption>
                        <div
                            v-if="filteredOptions.length === 0 && searchQuery"
                            class="py-2 px-3 text-sm text-gray-500 dark:text-gray-400"
                        >
                            No options found
                        </div>
                    </ListboxOptions>
                </transition>
            </div>
        </Listbox>
        <p v-if="error" class="text-sm text-red-600 dark:text-red-400">
            {{ error }}
        </p>
        <p
            v-if="hint && !error"
            class="text-sm text-gray-500 dark:text-gray-400"
        >
            {{ hint }}
        </p>
    </div>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import {
    Listbox,
    ListboxButton,
    ListboxOptions,
    ListboxOption,
} from "@headlessui/vue";
import {
    CheckIcon,
    ChevronUpDownIcon,
    PlusIcon,
} from "@heroicons/vue/24/outline";
import { useNotificationStore } from "../../stores/notification";
import api from "../../utils/api";

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: "",
    },
    label: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "",
    },
    options: {
        type: Array,
        default: () => [],
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
    size: {
        type: String,
        default: "md",
    },
    error: {
        type: String,
        default: "",
    },
    hint: {
        type: String,
        default: "",
    },
    id: {
        type: String,
        default: "",
    },
    searchable: {
        type: Boolean,
        default: true,
    },
    allowAdd: {
        type: Boolean,
        default: false,
    },
    addEndpoint: {
        type: String,
        default: "",
    },
    addField: {
        type: String,
        default: "name",
    },
});

const emit = defineEmits(["update:modelValue", "blur", "focus", "item-added"]);

const notificationStore = useNotificationStore();

const selectId = computed(
    () => props.id || `select-${Math.random().toString(36).substr(2, 9)}`
);

const placeholderText = computed(() => props.placeholder || "Choose");

const selectedValue = ref(props.modelValue);
const searchQuery = ref("");
const addingItem = ref(false);

const hasSearch = computed(() => props.searchable);

const filteredOptions = computed(() => {
    if (!searchQuery.value) {
        return props.options;
    }
    return props.options.filter((option) =>
        option.label.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const selectedLabel = computed(() => {
    if (!selectedValue.value && selectedValue.value !== 0) {
        return "";
    }
    const option = props.options.find(
        (opt) => opt.value === selectedValue.value
    );
    return option ? option.label : "";
});

const buttonClasses = computed(() => {
    const baseClasses =
        "block w-full rounded-md border px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 disabled:pointer-events-none disabled:opacity-50 bg-white dark:border-gray-600 dark:bg-gray-700 dark:text-white cursor-default text-left";

    const sizeClasses = {
        sm: "px-2.5 py-1.5 text-xs",
        md: "px-3 py-2 text-sm",
        lg: "px-4 py-3 text-base",
    };

    const variantClasses = props.error
        ? "border-red-500 focus:border-red-500 focus:ring-red-500 dark:border-red-500"
        : "border-gray-200 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:focus:border-blue-500";

    const disabledClasses = props.disabled
        ? "cursor-not-allowed opacity-50 bg-gray-50 dark:bg-gray-700"
        : "hover:bg-gray-50 dark:hover:bg-gray-700";

    return `${baseClasses} ${
        sizeClasses[props.size]
    } ${variantClasses} ${disabledClasses}`;
});

const handleValueChange = (value) => {
    selectedValue.value = value;
    emit("update:modelValue", value);
    searchQuery.value = ""; // Clear search when selection changes
};

const handleSearch = () => {
    // Search is handled by computed filteredOptions
};

const handleAddNewItem = async () => {
    if (!props.allowAdd || !props.addEndpoint || !searchQuery.value.trim()) {
        return;
    }

    addingItem.value = true;
    try {
        const response = await api.post(props.addEndpoint, {
            [props.addField]: searchQuery.value.trim(),
        });

        const newItem = response.data.data;

        // Emit the new item to parent
        emit("item-added", newItem);

        // Select the new item
        selectedValue.value = newItem.id || newItem.value;
        emit("update:modelValue", selectedValue.value);

        // Clear search
        searchQuery.value = "";

        notificationStore.success("New item added successfully");
    } catch (error) {
        console.error("Error adding new item:", error);
        notificationStore.error(
            error.response?.data?.message || "Failed to add new item"
        );
    } finally {
        addingItem.value = false;
    }
};

watch(
    () => props.modelValue,
    (newValue) => {
        selectedValue.value = newValue;
    }
);
</script>
