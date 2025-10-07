<template>
    <div class="space-y-8">
        <!-- Basic Example -->
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Basic DataTable</h2>
            <DataTable
                title="Simple Products"
                description="A basic example of DataTable with minimal configuration."
                :data="basicData"
                :columns="basicColumns"
                :loading="false"
                :show-actions="false"
                :show-add-button="false"
                :show-filters="false"
                :show-export="false"
                search-placeholder="Search products..."
            />
        </div>

        <!-- Advanced Example -->
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Advanced DataTable</h2>
            <DataTable
                title="Advanced Products"
                description="A comprehensive example with all features enabled."
                :data="advancedData"
                :columns="advancedColumns"
                :loading="loading"
                :selectable="true"
                :show-actions="true"
                :show-add-button="true"
                add-button-text="Add Product"
                :show-filters="true"
                :show-export="true"
                :show-bulk-actions="true"
                search-placeholder="Search products..."
                empty-title="No products found"
                empty-description="Start by adding your first product to the inventory."
                @add="handleAddProduct"
                @edit="handleEditProduct"
                @delete="handleDeleteProduct"
                @bulk-action="handleBulkAction"
                @export="handleExportProducts"
            >
                <!-- Custom Image Column -->
                <template #column-image="{ item }">
                    <img
                        class="w-12 h-12 rounded-lg object-cover"
                        :src="item.image || 'https://via.placeholder.com/48'"
                        :alt="item.name"
                    />
                </template>

                <!-- Custom Price Column -->
                <template #column-price="{ item }">
                    <span class="text-lg font-semibold text-green-600 dark:text-green-400">
                        ${{ item.price.toFixed(2) }}
                    </span>
                </template>

                <!-- Custom Stock Column -->
                <template #column-stock="{ item }">
                    <span
                        :class="[
                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                            item.stock > 10 ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' :
                            item.stock > 0 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' :
                            'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
                        ]"
                    >
                        {{ item.stock }} units
                    </span>
                </template>

                <!-- Custom Actions -->
                <template #actions="{ item }">
                    <div class="flex items-center justify-end gap-2">
                        <button
                            @click="handleQuickEdit(item)"
                            class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                            title="Quick Edit"
                        >
                            <PencilSquareIcon class="w-4 h-4" />
                        </button>
                        <button
                            @click="handleDuplicate(item)"
                            class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200 dark:hover:text-green-400 dark:hover:bg-green-900/20"
                            title="Duplicate"
                        >
                            <DocumentDuplicateIcon class="w-4 h-4" />
                        </button>
                        <button
                            @click="handleArchive(item)"
                            class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors duration-200 dark:hover:text-yellow-400 dark:hover:bg-yellow-900/20"
                            title="Archive"
                        >
                            <ArchiveBoxIcon class="w-4 h-4" />
                        </button>
                    </div>
                </template>

                <!-- Custom Filters -->
                <template #filters>
                    <div class="p-3 space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                            <select v-model="selectedCategory" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">All Categories</option>
                                <option value="electronics">Electronics</option>
                                <option value="clothing">Clothing</option>
                                <option value="books">Books</option>
                                <option value="home">Home & Garden</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price Range</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input
                                    v-model="priceRange.min"
                                    type="number"
                                    placeholder="Min"
                                    class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                                <input
                                    v-model="priceRange.max"
                                    type="number"
                                    placeholder="Max"
                                    class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stock Status</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input v-model="stockFilters.inStock" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">In Stock</span>
                                </label>
                                <label class="flex items-center">
                                    <input v-model="stockFilters.lowStock" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Low Stock</span>
                                </label>
                                <label class="flex items-center">
                                    <input v-model="stockFilters.outOfStock" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Out of Stock</span>
                                </label>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-gray-200 dark:border-gray-600 flex gap-2">
                            <button
                                @click="applyFilters"
                                class="flex-1 px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                            >
                                Apply
                            </button>
                            <button
                                @click="clearFilters"
                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500"
                            >
                                Clear
                            </button>
                        </div>
                    </div>
                </template>
            </DataTable>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import DataTable from '../UI/DataTable.vue'
import {
    PencilSquareIcon,
    DocumentDuplicateIcon,
    ArchiveBoxIcon
} from '@heroicons/vue/24/outline'

// Reactive data
const loading = ref(false)
const selectedCategory = ref('')
const priceRange = ref({ min: '', max: '' })
const stockFilters = ref({
    inStock: false,
    lowStock: false,
    outOfStock: false
})

// Basic data and columns
const basicData = ref([
    { id: 1, name: 'Laptop Pro', category: 'Electronics', price: 1299.99 },
    { id: 2, name: 'Wireless Headphones', category: 'Electronics', price: 199.99 },
    { id: 3, name: 'Coffee Mug', category: 'Home', price: 15.99 },
    { id: 4, name: 'Programming Book', category: 'Books', price: 49.99 },
    { id: 5, name: 'Desk Chair', category: 'Furniture', price: 299.99 }
])

const basicColumns = [
    { key: 'name', label: 'Product Name', sortable: true },
    { key: 'category', label: 'Category', sortable: true },
    { key: 'price', label: 'Price', type: 'currency', sortable: true }
]

// Advanced data and columns
const advancedData = ref([
    {
        id: 1,
        name: 'MacBook Pro 16"',
        category: 'electronics',
        price: 2499.99,
        stock: 15,
        image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=64&h=64&fit=crop&crop=center',
        createdAt: '2024-01-15T10:30:00Z'
    },
    {
        id: 2,
        name: 'Sony WH-1000XM4',
        category: 'electronics',
        price: 349.99,
        stock: 8,
        image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=64&h=64&fit=crop&crop=center',
        createdAt: '2024-01-10T14:20:00Z'
    },
    {
        id: 3,
        name: 'Premium Coffee Beans',
        category: 'food',
        price: 24.99,
        stock: 0,
        image: 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=64&h=64&fit=crop&crop=center',
        createdAt: '2024-01-12T09:15:00Z'
    },
    {
        id: 4,
        name: 'JavaScript: The Good Parts',
        category: 'books',
        price: 32.99,
        stock: 25,
        image: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=64&h=64&fit=crop&crop=center',
        createdAt: '2024-01-08T16:45:00Z'
    },
    {
        id: 5,
        name: 'Ergonomic Office Chair',
        category: 'furniture',
        price: 399.99,
        stock: 3,
        image: 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=64&h=64&fit=crop&crop=center',
        createdAt: '2024-01-05T11:30:00Z'
    }
])

const advancedColumns = [
    { key: 'image', label: 'Image', sortable: false },
    { key: 'name', label: 'Product Name', sortable: true },
    { key: 'category', label: 'Category', sortable: true },
    { key: 'price', label: 'Price', sortable: true },
    { key: 'stock', label: 'Stock', sortable: true },
    { key: 'createdAt', label: 'Created', type: 'date', sortable: true }
]

// Methods
const handleAddProduct = () => {
    console.log('Add product clicked')
    // Simulate adding a new product
    const newProduct = {
        id: advancedData.value.length + 1,
        name: `New Product ${advancedData.value.length + 1}`,
        category: 'electronics',
        price: Math.floor(Math.random() * 1000) + 100,
        stock: Math.floor(Math.random() * 50),
        image: 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=64&h=64&fit=crop&crop=center',
        createdAt: new Date().toISOString()
    }
    advancedData.value.unshift(newProduct)
}

const handleEditProduct = (product) => {
    console.log('Edit product:', product)
    // Implement edit logic
}

const handleDeleteProduct = (product) => {
    console.log('Delete product:', product)
    if (confirm(`Are you sure you want to delete ${product.name}?`)) {
        const index = advancedData.value.findIndex(p => p.id === product.id)
        if (index > -1) {
            advancedData.value.splice(index, 1)
        }
    }
}

const handleQuickEdit = (product) => {
    console.log('Quick edit:', product)
    // Toggle stock status for demo
    const index = advancedData.value.findIndex(p => p.id === product.id)
    if (index > -1) {
        advancedData.value[index].stock = advancedData.value[index].stock > 0 ? 0 : 10
    }
}

const handleDuplicate = (product) => {
    console.log('Duplicate product:', product)
    const duplicated = {
        ...product,
        id: Math.max(...advancedData.value.map(p => p.id)) + 1,
        name: `${product.name} (Copy)`,
        createdAt: new Date().toISOString()
    }
    advancedData.value.unshift(duplicated)
}

const handleArchive = (product) => {
    console.log('Archive product:', product)
    // Simulate archiving by removing from list
    const index = advancedData.value.findIndex(p => p.id === product.id)
    if (index > -1) {
        advancedData.value.splice(index, 1)
    }
}

const handleBulkAction = (selectedItems) => {
    console.log('Bulk action for items:', selectedItems)
    alert(`Bulk action applied to ${selectedItems.length} items`)
}

const handleExportProducts = (data) => {
    console.log('Export products:', data)
    // Simple CSV export
    const csv = [
        'Name,Category,Price,Stock',
        ...data.map(p => `"${p.name}","${p.category}","${p.price}","${p.stock}"`)
    ].join('\n')

    const blob = new Blob([csv], { type: 'text/csv' })
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = 'products.csv'
    link.click()
}

const applyFilters = () => {
    console.log('Apply filters:', {
        category: selectedCategory.value,
        priceRange: priceRange.value,
        stockFilters: stockFilters.value
    })
    // Implement filter logic here
}

const clearFilters = () => {
    selectedCategory.value = ''
    priceRange.value = { min: '', max: '' }
    stockFilters.value = {
        inStock: false,
        lowStock: false,
        outOfStock: false
    }
}
</script>