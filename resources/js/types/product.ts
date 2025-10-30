/**
 * Product-related TypeScript interfaces and types
 */

export interface Product {
    id: number;
    product_code: string;
    product_name: string;
    description?: string;
    product_type: ProductType;
    category_id?: number | null;
    unit_id: number;
    purchase_price: number;
    selling_price?: number;
    minimum_stock: number;
    maximum_stock: number;
    is_active: boolean;
    created_at?: string;
    updated_at?: string;
}

export interface ProductFormData {
    product_code: string;
    product_name: string;
    description: string;
    product_type: ProductType;
    category_id: number | null;
    unit_id: number;
    purchase_price: number;
    selling_price: number;
    minimum_stock: number;
    maximum_stock: number;
    is_active: boolean;
}

export interface ProductFormErrors {
    product_code?: string[];
    product_name?: string[];
    description?: string[];
    product_type?: string[];
    unit_id?: string[];
    purchase_price?: string[];
    selling_price?: string[];
    minimum_stock?: string[];
    maximum_stock?: string[];
    is_active?: string[];
}

export interface ProductFormTouched {
    product_code?: boolean;
    product_name?: boolean;
    description?: boolean;
    product_type?: boolean;
    unit_id?: boolean;
    purchase_price?: boolean;
    selling_price?: boolean;
    minimum_stock?: boolean;
    maximum_stock?: boolean;
    is_active?: boolean;
}

export type ProductType = 'finished_goods' | 'raw_material' | 'consumable';

export interface ProductOption {
    value: string | number;
    label: string;
    icon?: any;
    color?: string;
}

export interface UnitOption {
    id: number;
    name: string;
    display_name?: string;
}

export interface ProductTypeOption {
    id?: number;
    value?: string;
    name: string;
    label?: string;
}

export interface ProductFormModalProps {
    isOpen: boolean;
    editingProduct: Product | null;
    unitOptions: UnitOption[];
}

export interface ProductFormModalEmits {
    close: () => void;
    saved: (data: ProductFormSaveData) => void;
    'unit-added': (unit: UnitOption) => void;
}

export interface ProductFormSaveData {
    formData: ProductFormData;
    isEditing: boolean;
    productId?: number;
}

export interface ProductValidationRule {
    required?: boolean;
    maxLength?: number;
    type?: 'string' | 'number' | 'boolean';
    min?: number;
    max?: number;
    pattern?: RegExp;
    in?: (string | number)[];
    nullable?: boolean;
    message?: string;
    requiredMessage?: string;
    maxLengthMessage?: string;
    typeMessage?: string;
    minMessage?: string;
    maxMessage?: string;
    patternMessage?: string;
    inMessage?: string;
}

export interface ProductValidationRules {
    [key: string]: ProductValidationRule;
}

export interface ProductFormSectionProps {
    form: ProductFormData;
    errors: ProductFormErrors;
    disabled?: boolean;
}

export interface ProductFormBasicInfoProps extends ProductFormSectionProps {
    editingProduct: Product | null;
    generatingCode: boolean;
}

export interface ProductFormDetailsProps extends ProductFormSectionProps {
    unitOptions: UnitOption[];
}

export interface ProductFormActionsProps {
    loading: boolean;
    disabled: boolean;
    editingProduct: Product | null;
    loadingText?: string;
    submitText?: string;
}

export interface ProductFormSectionEmits {
    'validate-field': (field: keyof ProductFormData) => void;
}

export interface ProductFormBasicInfoEmits extends ProductFormSectionEmits {
    'generate-code': () => void;
}

export interface ProductFormDetailsEmits extends ProductFormSectionEmits {
    'product-type-added': (productType: ProductTypeOption) => void;
    'unit-added': (unit: UnitOption) => void;
}

export interface ProductFormActionsEmits {
    cancel: () => void;
}
