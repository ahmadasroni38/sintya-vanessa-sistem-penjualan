# Sales Data Generation Guide

## Overview
Successfully generated **78 random sales transactions** for the date range **January 1-9, 2026** following the [`SalesController.php`](app/Http/Controllers/SalesController.php:133) store process.

## What Was Created

### 1. Laravel Artisan Command
**File:** [`app/Console/Commands/GenerateSalesData.php`](app/Console/Commands/GenerateSalesData.php)

This command generates random sales transactions with:
- Random dates within specified range
- Random customers, locations, and products
- Random quantities (1-10 items per line)
- Random discounts (0-20%)
- Random tax (0% or 11%)
- Random payment methods (cash, transfer, credit)
- Random statuses (draft or posted - 80% posted)
- Proper transaction numbering (SO-2026-XXXXX)

### 2. Standalone PHP Script (Optional)
**File:** [`generate_sales_data.php`](generate_sales_data.php)

Alternative script that generates SQL INSERT statements directly without Laravel framework.

## Usage

### Using Laravel Artisan Command (Recommended)

Run the command with custom parameters:

```bash
# Generate 78 transactions for Jan 1-9, 2026 (default)
php artisan sales:generate 78 --start-date=2026-01-01 --end-date=2026-01-09

# Generate custom number of transactions
php artisan sales:generate 100 --start-date=2026-01-01 --end-date=2026-01-31

# Generate for different date range
php artisan sales:generate 50 --start-date=2026-02-01 --end-date=2026-02-28
```

### Command Options

| Option | Description | Default |
|--------|-------------|----------|
| `count` | Number of transactions to generate | 78 |
| `--start-date` | Start date (Y-m-d format) | 2026-01-01 |
| `--end-date` | End date (Y-m-d format) | 2026-01-09 |

## Data Generated

### Sales Table
- **Transaction Numbers:** SO-2026-00002 to SO-2026-00079
- **Date Range:** January 1-9, 2026
- **Total Records:** 78 sales
- **Status Distribution:**
  - ~80% posted
  - ~20% draft
- **Payment Methods:** cash, transfer, credit

### Sale Details Table
- **Total Records:** ~200-300 sale details (1-5 items per sale)
- **Each detail includes:**
  - Product ID
  - Quantity (1-10)
  - Unit price (from product table)
  - Discount percent (0-20%)
  - Discount amount
  - Tax percent (0% or 11%)
  - Tax amount
  - Total price

## Verification

Check the generated data:

```sql
-- Count sales in date range
SELECT COUNT(*) as total_sales 
FROM sales 
WHERE transaction_date BETWEEN '2026-01-01' AND '2026-01-09';

-- View sample sales
SELECT 
    transaction_number,
    transaction_date,
    customer_id,
    location_id,
    total_amount,
    status,
    payment_method
FROM sales
WHERE transaction_date BETWEEN '2026-01-01' AND '2026-01-09'
ORDER BY transaction_date
LIMIT 10;

-- Count sale details
SELECT COUNT(*) as total_details FROM sale_details;

-- View sample sale details
SELECT 
    sd.sale_id,
    s.transaction_number,
    sd.product_id,
    p.product_name,
    sd.quantity,
    sd.unit_price,
    sd.total_price
FROM sale_details sd
JOIN sales s ON sd.sale_id = s.id
JOIN products p ON sd.product_id = p.id
LIMIT 10;
```

## How It Works

The generation process follows the [`SalesController::store()`](app/Http/Controllers/SalesController.php:133) method:

1. **Create Sale Header**
   - Generate transaction number (SO-2026-XXXXX)
   - Set initial totals to 0
   - Set status to 'draft'
   - Assign random customer, location, payment method

2. **Create Sale Details**
   - For each item (1-5 per sale):
     - Select random product
     - Generate random quantity (1-10)
     - Apply random discount (0-20%)
     - Apply random tax (0% or 11%)
     - Calculate line totals

3. **Calculate Totals**
   - Sum all line subtotals
   - Sum all tax amounts
   - Sum all discount amounts
   - Calculate total amount
   - Generate random paid amount (sometimes with extra for change)

4. **Update Sale**
   - Update with calculated totals
   - Randomly post the sale (80% probability)
   - Set posted_by and posted_at if posted

## Important Notes

### Database Requirements
Before running the command, ensure you have:
- ✅ At least 1 customer in [`customers`](db_ta_sintiya_2025.sql:151) table
- ✅ At least 1 active location in [`locations`](db_ta_sintiya_2025.sql:240) table
- ✅ At least 1 active product with selling_price > 0 in [`products`](db_ta_sintiya_2025.sql:378) table

### Payment Methods
The command uses payment methods that match the database enum:
- `cash`
- `transfer`
- `credit`

### Transaction Numbers
The command automatically continues from the last transaction number in the database to avoid conflicts.

## Regenerating Data

To regenerate the data:

1. **Delete existing sales:**
   ```sql
   DELETE FROM sale_details WHERE sale_id IN (
       SELECT id FROM sales WHERE transaction_date BETWEEN '2026-01-01' AND '2026-01-09'
   );
   DELETE FROM sales WHERE transaction_date BETWEEN '2026-01-01' AND '2026-01-09';
   ```

2. **Run the command again:**
   ```bash
   php artisan sales:generate 78 --start-date=2026-01-01 --end-date=2026-01-09
   ```

## Support

For issues or questions:
- Check database connection in [`.env`](.env:11)
- Verify required tables exist and have data
- Review command output for error messages

## Summary

✅ **Generated:** 78 sales transactions  
✅ **Date Range:** January 1-9, 2026  
✅ **Transaction Numbers:** SO-2026-00002 to SO-2026-00079  
✅ **Status:** Ready to use in the system  
✅ **Method:** Follows [`SalesController::store()`](app/Http/Controllers/SalesController.php:133) process  
