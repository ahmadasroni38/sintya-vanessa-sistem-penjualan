---
config:
  layout: elk
  look: neo
---
erDiagram
    USER ||--o{ USER_ROLE : has
    ROLE ||--o{ USER_ROLE : assigned

    USER ||--o{ JOURNAL_ENTRY : creates
    USER ||--o{ STOCK_MUTATION : manages
    USER ||--o{ STOCK_ADJUSTMENT : manages
    USER ||--o{ STOCK_OPNAME : manages
    USER ||--o{ STOCK_IN : manages
    USER ||--o{ SALE : processes

    CHART_OF_ACCOUNT ||--o{ CHART_OF_ACCOUNT : "has child"
    CHART_OF_ACCOUNT ||--o{ ACCOUNT_BALANCE_HISTORY : tracks
    CHART_OF_ACCOUNT ||--o{ ACCOUNT_AUDIT : audited
    CHART_OF_ACCOUNT ||--o{ JOURNAL_ENTRY_LINE : records

    JOURNAL_ENTRY ||--o{ JOURNAL_ENTRY_LINE : contains
    JOURNAL_ENTRY ||--o{ JOURNAL_APPROVAL : requires
    JOURNAL_ENTRY ||--o{ JOURNAL_ATTACHMENT : supports
    JOURNAL_ENTRY ||--o{ JOURNAL_REVISION : revised

    PRODUCT_CATEGORY ||--o{ PRODUCT_CATEGORY : "has subcategory"
    PRODUCT_CATEGORY ||--o{ PRODUCT : classifies
    UNIT ||--o{ PRODUCT : measures
    LOCATION ||--o{ PRODUCT : "default storage"

    PRODUCT ||--o{ STOCK_BALANCE : tracked
    PRODUCT ||--o{ STOCK_CARD : "movement recorded"
    PRODUCT ||--o{ STOCK_IN_LINE : received
    PRODUCT ||--o{ STOCK_MUTATION_LINE : transferred
    PRODUCT ||--o{ STOCK_ADJUSTMENT_LINE : adjusted
    PRODUCT ||--o{ STOCK_OPNAME_LINE : counted
    PRODUCT ||--o{ SALE_LINE : sold

    LOCATION ||--o{ LOCATION : "has sub-location"
    LOCATION ||--o{ STOCK_BALANCE : stores
    LOCATION ||--o{ STOCK_CARD : tracks
    LOCATION ||--o{ STOCK_IN : receives
    LOCATION ||--o{ STOCK_MUTATION : "origin/destination"
    LOCATION ||--o{ STOCK_ADJUSTMENT : "adjusted at"
    LOCATION ||--o{ STOCK_OPNAME : "counted at"
    LOCATION ||--o{ SALE : "sells from"
    LOCATION ||--o{ JOURNAL_ENTRY_LINE : "cost center"

    STOCK_IN ||--o{ STOCK_IN_LINE : details
    STOCK_MUTATION ||--o{ STOCK_MUTATION_LINE : details
    STOCK_ADJUSTMENT ||--o{ STOCK_ADJUSTMENT_LINE : details
    STOCK_OPNAME ||--o{ STOCK_OPNAME_LINE : details

    CUSTOMER ||--o{ SALE : purchases
    SALE ||--o{ SALE_LINE : details

    USER {
        string name
        string email
        string status
        string contact_info
    }

    ROLE {
        string name
        string display_name
        string permissions
        string active_status
    }

    USER_ROLE {
        string assignment_period
        string active_status
    }

    CHART_OF_ACCOUNT {
        string account_code
        string account_name
        string account_type
        string normal_balance
        int hierarchy_level
        decimal opening_balance
        decimal current_balance
        string active_status
    }

    ACCOUNT_BALANCE_HISTORY {
        decimal balance_amount
        decimal debit_total
        decimal credit_total
        string period
    }

    ACCOUNT_AUDIT {
        string event_type
        string changes
        string user_info
        string audit_trail
    }

    JOURNAL_ENTRY {
        string entry_number
        date entry_date
        string description
        string entry_type
        string status
        string debit_credit_totals
        string approval_info
    }

    JOURNAL_ENTRY_LINE {
        string transaction_type
        string amounts
        string tax_info
        string cost_allocation
    }

    JOURNAL_APPROVAL {
        string approval_status
        date approval_date
        string notes
    }

    JOURNAL_ATTACHMENT {
        string filename
        string file_info
    }

    JOURNAL_REVISION {
        int revision_number
        string changes
        string notes
    }

    PRODUCT {
        string product_code
        string product_name
        string product_type
        string pricing
        string stock_limits
        string active_status
    }

    PRODUCT_CATEGORY {
        string category_code
        string category_name
        string active_status
    }

    UNIT {
        string unit_code
        string unit_name
        string symbol
        string active_status
    }

    LOCATION {
        string location_code
        string location_name
        string address_info
        string geographic_data
        string active_status
    }

    STOCK_BALANCE {
        decimal current_quantity
        string stock_status
        date last_transaction
    }

    STOCK_CARD {
        date transaction_date
        string transaction_type
        string quantities
        decimal running_balance
        string pricing
    }

    STOCK_IN {
        string transaction_number
        date transaction_date
        string supplier_info
        decimal total_value
        string status
    }

    STOCK_IN_LINE {
        decimal quantity
        decimal unit_price
        decimal total_price
    }

    STOCK_MUTATION {
        string transaction_number
        date transaction_date
        string origin_destination
        string workflow_status
        string approval_chain
    }

    STOCK_MUTATION_LINE {
        decimal quantity
        decimal available_stock
    }

    STOCK_ADJUSTMENT {
        string adjustment_number
        date adjustment_date
        int total_items
        string status
        string approval_info
    }

    STOCK_ADJUSTMENT_LINE {
        decimal system_quantity
        decimal actual_quantity
        decimal variance
        string adjustment_type
        string reason
    }

    STOCK_OPNAME {
        string opname_number
        date opname_date
        int total_items
        string status
        string completion_info
    }

    STOCK_OPNAME_LINE {
        decimal system_quantity
        decimal physical_count
        decimal variance
    }

    SALE {
        string transaction_number
        date transaction_date
        string amounts
        string payment_info
        string status
    }

    SALE_LINE {
        decimal quantity
        string pricing
        string discounts
        string taxes
    }

    CUSTOMER {
        string customer_code
        string customer_name
        string contact_info
        string status
    }
