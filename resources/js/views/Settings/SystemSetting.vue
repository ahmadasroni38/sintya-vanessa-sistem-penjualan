<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div
            class="bg-gradient-to-r from-blue-600 via-purple-600 to-blue-800 overflow-hidden shadow-lg rounded-lg border border-gray-200 dark:border-gray-700"
        >
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center"
                                >
                                    <svg
                                        class="w-6 h-6 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                        ></path>
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-white">
                                    Pengaturan Sistem
                                </h1>
                                <p class="mt-1 text-sm text-blue-100">
                                    Kelola informasi dasar dan pengaturan
                                    aplikasi
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Form -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
        >
            <div class="p-6">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Logo Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-1">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Logo Sistem
                            </label>
                            <div class="space-y-4">
                                <!-- Current Logo Preview -->
                                <div
                                    v-if="
                                        form.logo_sistem ||
                                        currentSettings.logo_sistem
                                    "
                                    class="flex justify-center"
                                >
                                    <div class="relative">
                                        <img
                                            :src="
                                                logoPreview ||
                                                getLogoUrl(
                                                    currentSettings.logo_sistem
                                                )
                                            "
                                            alt="Logo Sistem"
                                            class="w-32 h-32 object-contain border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700"
                                        />
                                        <button
                                            type="button"
                                            @click="removeLogo"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors"
                                        >
                                            <svg
                                                class="w-4 h-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"
                                                ></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- File Upload -->
                                <div class="flex justify-center">
                                    <label class="cursor-pointer">
                                        <div
                                            class="flex flex-col items-center justify-center w-32 h-32 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-400 dark:hover:border-blue-500 transition-colors bg-gray-50 dark:bg-gray-700"
                                        >
                                            <svg
                                                class="w-8 h-8 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                                ></path>
                                            </svg>
                                            <span
                                                class="mt-2 text-sm text-gray-500 dark:text-gray-400"
                                                >Upload Logo</span
                                            >
                                        </div>
                                        <input
                                            type="file"
                                            ref="logoInput"
                                            @change="handleLogoUpload"
                                            accept="image/*"
                                            class="hidden"
                                        />
                                    </label>
                                </div>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 text-center"
                                >
                                    Format: JPEG, PNG, JPG, GIF. Maksimal 2MB
                                </p>
                            </div>
                        </div>

                        <!-- Settings Fields -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- System Name -->
                            <div>
                                <label
                                    for="nama_sistem"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                >
                                    Nama Sistem
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="nama_sistem"
                                    v-model="form.nama_sistem"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Masukkan nama sistem"
                                />
                                <p
                                    v-if="errors.nama_sistem"
                                    class="mt-1 text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ errors.nama_sistem[0] }}
                                </p>
                            </div>

                            <!-- System Description -->
                            <div>
                                <label
                                    for="deskripsi_sistem"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                >
                                    Deskripsi Sistem
                                    <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="deskripsi_sistem"
                                    v-model="form.deskripsi_sistem"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Masukkan deskripsi sistem"
                                ></textarea>
                                <p
                                    v-if="errors.deskripsi_sistem"
                                    class="mt-1 text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ errors.deskripsi_sistem[0] }}
                                </p>
                            </div>

                            <!-- Company Name -->
                            <div>
                                <label
                                    for="nama_perusahaan"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                >
                                    Nama Perusahaan
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="nama_perusahaan"
                                    v-model="form.nama_perusahaan"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Masukkan nama perusahaan"
                                />
                                <p
                                    v-if="errors.nama_perusahaan"
                                    class="mt-1 text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ errors.nama_perusahaan[0] }}
                                </p>
                            </div>

                            <!-- Company Address -->
                            <div>
                                <label
                                    for="alamat_lengkap"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                >
                                    Alamat Lengkap
                                    <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="alamat_lengkap"
                                    v-model="form.alamat_lengkap"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Masukkan alamat lengkap perusahaan"
                                ></textarea>
                                <p
                                    v-if="errors.alamat_lengkap"
                                    class="mt-1 text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ errors.alamat_lengkap[0] }}
                                </p>
                            </div>

                            <!-- Company Email -->
                            <div>
                                <label
                                    for="email_perusahaan"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                >
                                    Email Perusahaan
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="email_perusahaan"
                                    v-model="form.email_perusahaan"
                                    type="email"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Masukkan email perusahaan"
                                />
                                <p
                                    v-if="errors.email_perusahaan"
                                    class="mt-1 text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ errors.email_perusahaan[0] }}
                                </p>
                            </div>

                            <!-- Company Phone -->
                            <div>
                                <label
                                    for="nomor_telepon"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                >
                                    Nomor Telepon
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="nomor_telepon"
                                    v-model="form.nomor_telepon"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Masukkan nomor telepon perusahaan"
                                />
                                <p
                                    v-if="errors.nomor_telepon"
                                    class="mt-1 text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ errors.nomor_telepon[0] }}
                                </p>
                            </div>

                            <!-- Footer Text -->
                            <div>
                                <label
                                    for="footer_text"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                >
                                    Teks Footer
                                </label>
                                <textarea
                                    id="footer_text"
                                    v-model="form.footer_text"
                                    rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Masukkan teks footer (opsional)"
                                ></textarea>
                                <p
                                    class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    Teks yang akan ditampilkan di footer
                                    aplikasi
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div
                        class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700"
                    >
                        <button
                            type="button"
                            @click="resetForm"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                        >
                            Reset
                        </button>
                        <button
                            type="submit"
                            :disabled="loading"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            <span v-if="loading" class="flex items-center">
                                <svg
                                    class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                                Menyimpan...
                            </span>
                            <span v-else>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import { apiGet, apiPost } from "@/utils/api";
import { useNotificationStore } from "@/stores/notification";

const router = useRouter();
const notification = useNotificationStore();

// Loading states
const loading = ref(false);
const fetchLoading = ref(true);

// Form data
const form = reactive({
    nama_sistem: "",
    deskripsi_sistem: "",
    nama_perusahaan: "",
    alamat_lengkap: "",
    email_perusahaan: "",
    nomor_telepon: "",
    footer_text: "",
    logo_sistem: null,
});

// Current settings for comparison
const currentSettings = ref({});

// Form errors
const errors = ref({});

// Logo preview
const logoPreview = ref(null);
const logoInput = ref(null);

// Computed properties
const hasChanges = computed(() => {
    return (
        JSON.stringify(form) !==
        JSON.stringify({
            nama_sistem: currentSettings.value.nama_sistem || "",
            deskripsi_sistem: currentSettings.value.deskripsi_sistem || "",
            nama_perusahaan: currentSettings.value.nama_perusahaan || "",
            alamat_lengkap: currentSettings.value.alamat_lengkap || "",
            email_perusahaan: currentSettings.value.email_perusahaan || "",
            nomor_telepon: currentSettings.value.nomor_telepon || "",
            footer_text: currentSettings.value.footer_text || "",
            logo_sistem: null,
        })
    );
});

// Methods
const fetchSettings = async () => {
    try {
        fetchLoading.value = true;
        const response = await apiGet("/settings");

        if (response.success) {
            currentSettings.value = response.data;
            // Populate form with current data
            Object.keys(form).forEach((key) => {
                if (key !== "logo_sistem") {
                    form[key] = response.data[key] || "";
                }
            });
        }
    } catch (error) {
        console.error("Error fetching settings:", error);
        notification.error("Gagal memuat pengaturan sistem");
    } finally {
        fetchLoading.value = false;
    }
};

const handleLogoUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Validate file type
        const allowedTypes = [
            "image/jpeg",
            "image/png",
            "image/jpg",
            "image/gif",
        ];
        if (!allowedTypes.includes(file.type)) {
            notification.error(
                "Format file tidak didukung. Gunakan JPEG, PNG, JPG, atau GIF."
            );
            return;
        }

        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            notification.error("Ukuran file maksimal 2MB.");
            return;
        }

        form.logo_sistem = file;

        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            logoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removeLogo = () => {
    form.logo_sistem = null;
    logoPreview.value = null;
    if (logoInput.value) {
        logoInput.value.value = "";
    }
};

const getLogoUrl = (logoPath) => {
    if (!logoPath) return null;
    return `/storage/logo/${logoPath}`;
};

const resetForm = () => {
    // Reset to current settings
    Object.keys(form).forEach((key) => {
        if (key !== "logo_sistem") {
            form[key] = currentSettings.value[key] || "";
        } else {
            form[key] = null;
        }
    });
    logoPreview.value = null;
    if (logoInput.value) {
        logoInput.value.value = "";
    }
    errors.value = {};
};

const submitForm = async () => {
    try {
        loading.value = true;
        errors.value = {};

        // Debug: Log form data
        console.log("Form data before submit:", form);

        // Create FormData for file upload
        const formData = new FormData();

        // Add method spoofing for Laravel PUT
        formData.append("_method", "PUT");

        // Add text fields - include empty strings but not null/undefined
        Object.keys(form).forEach((key) => {
            if (
                key !== "logo_sistem" &&
                form[key] !== null &&
                form[key] !== undefined
            ) {
                formData.append(key, form[key]);
                console.log(`Adding ${key}:`, form[key]);
            }
        });

        // Add logo file if exists
        if (form.logo_sistem) {
            formData.append("logo_sistem", form.logo_sistem);
            console.log("Adding logo file:", form.logo_sistem);
        }

        // Debug: Log FormData contents
        console.log("FormData contents:");
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }

        // Use POST with method spoofing for file uploads
        const response = await apiPost("/settings", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        if (response.success) {
            notification.success("Pengaturan sistem berhasil diperbarui");
            // Update current settings
            currentSettings.value = response.data;
            // Clear logo preview and form file
            logoPreview.value = null;
            form.logo_sistem = null;
            if (logoInput.value) {
                logoInput.value.value = "";
            }
        }
    } catch (error) {
        console.error("Error updating settings:", error);

        if (error.response?.status === 422 && error.response?.data?.errors) {
            errors.value = error.response.data.errors;
            notification.error("Mohon perbaiki kesalahan pada form");
        } else {
            notification.error("Gagal memperbarui pengaturan sistem");
        }
    } finally {
        loading.value = false;
    }
};

// Lifecycle
onMounted(() => {
    fetchSettings();
});
</script>
