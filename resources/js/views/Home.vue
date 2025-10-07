<template>
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold mb-6">Welcome to Laravel + Vue 3</h2>
        <p class="mb-4">
            This is a full-stack application with Laravel backend and Vue 3
            frontend.
        </p>

        <!-- Base Components Demo -->
        <section class="mb-8">
            <h3 class="text-2xl font-semibold mb-4">Base Components</h3>
            <div class="space-y-4">
                <div>
                    <h4 class="text-lg font-medium mb-2">Buttons</h4>
                    <div class="flex gap-2 flex-wrap">
                        <BaseButton variant="primary">Primary</BaseButton>
                        <BaseButton variant="secondary">Secondary</BaseButton>
                        <BaseButton variant="success">Success</BaseButton>
                        <BaseButton variant="danger">Danger</BaseButton>
                        <BaseButton variant="warning">Warning</BaseButton>
                        <BaseButton variant="primary" outline
                            >Outline</BaseButton
                        >
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-medium mb-2">Inputs</h4>
                    <BaseInput
                        v-model="inputValue"
                        placeholder="Enter text..."
                    />
                    <p class="text-sm text-gray-600 mt-1">
                        Value: {{ inputValue }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Forms Demo -->
        <section class="mb-8">
            <h3 class="text-2xl font-semibold mb-4">Forms</h3>
            <div class="max-w-md space-y-4">
                <FormInput
                    v-model="formData.name"
                    label="Name"
                    placeholder="Enter your name"
                    required
                />
                <FormInput
                    v-model="formData.email"
                    label="Email"
                    type="email"
                    placeholder="Enter your email"
                    required
                />
                <FormSelect
                    v-model="formData.country"
                    label="Country"
                    placeholder="Select a country..."
                    :options="countryOptions"
                />
                <BaseButton variant="primary" @click="submitForm"
                    >Submit</BaseButton
                >
            </div>
        </section>

        <!-- Overlays Demo -->
        <section class="mb-8">
            <h3 class="text-2xl font-semibold mb-4">Overlays</h3>
            <BaseButton variant="primary" @click="showModal = true"
                >Open Modal</BaseButton
            >

            <Modal
                :is-open="showModal"
                title="Sample Modal"
                @close="showModal = false"
            >
                <p class="text-gray-700 dark:text-gray-300">
                    This is a modal dialog using Preline UI components.
                </p>

                <template #footer="{ close }">
                    <BaseButton variant="secondary" @click="close"
                        >Cancel</BaseButton
                    >
                    <BaseButton variant="primary" @click="close"
                        >Confirm</BaseButton
                    >
                </template>
            </Modal>
        </section>
    </div>
</template>

<script setup>
import { ref, reactive } from "vue";
import BaseButton from "../components/Base/Button.vue";
import BaseInput from "../components/Base/Input.vue";
import FormInput from "../components/Forms/FormInput.vue";
import FormSelect from "../components/Forms/FormSelect.vue";
import Modal from "../components/Overlays/Modal.vue";

const inputValue = ref("");
const showModal = ref(false);

const formData = reactive({
    name: "",
    email: "",
    country: "",
});

const countryOptions = [
    { value: "us", label: "United States" },
    { value: "ca", label: "Canada" },
    { value: "uk", label: "United Kingdom" },
    { value: "de", label: "Germany" },
    { value: "fr", label: "France" },
];

const submitForm = () => {
    console.log("Form submitted:", formData);
    alert("Form submitted! Check console for data.");
};
</script>

<style scoped>
/* Scoped styles */
</style>
