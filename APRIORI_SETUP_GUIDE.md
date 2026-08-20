# Apriori Algorithm Integration - Setup Complete ✅

## What Has Been Done

Your Laravel e-commerce project now has the **Apriori Association Rules Algorithm** fully integrated. This will help identify which products are frequently bought together.

### Files Created:

1. **Migration:** `database/migrations/2024_04_01_000000_create_carts_tables.php`
   - Creates `carts` table to store user shopping sessions
   - Creates `cart_items` table to store individual items in each cart

2. **Models:**
   - `app/Models/Cart.php` - Cart model with relationships
   - `app/Models/CartItem.php` - CartItem model with relationships

3. **Controller:** `app/Http/Controllers/Admin/AprioriController.php`
   - `index()` - Display Apriori analysis dashboard
   - `generateRules()` - Generate association rules based on cart data
   - `statistics()` - Get cart statistics for display

4. **Blade View:** `resources/views/dashboard/apriori/index.blade.php`
   - Interactive dashboard to configure and run Apriori algorithm
   - Display association rules in a table format
   - Real-time statistics

5. **Seeder:** `database/seeders/CartSeeder.php`
   - Generates sample cart data for testing (50 carts already created)

### Files Modified:

1. **OrderController.php** - Updated `store()` method to:
   - Save cart data to `carts` and `cart_items` tables when order is placed
   - Preserve existing order functionality
   - Track all products purchased together

2. **routes/web.php** - Added 3 new admin routes:
   - `GET /admin/apriori` → Display dashboard
   - `GET /admin/apriori/rules` → Generate rules endpoint
   - `GET /admin/apriori/statistics` → Get statistics endpoint

3. **dashboard/header.blade.php** - Added navigation link

---

## How to Use

### Step 1: Access the Dashboard
1. Login as admin
2. Go to Admin Panel
3. Click "Association Rules (Apriori)" in sidebar

### Step 2: Generate Rules
1. Adjust **Minimum Support (%)** - Default: 20%
   - Items must appear together in at least this % of carts
   - Lower value = more rules, Higher value = more significant rules

2. Adjust **Minimum Confidence (%)** - Default: 50%
   - Probability that customer buys B if they buy A
   - Higher value = stronger relationships

3. Click "Generate Rules" button

### Step 3: View Results
- **If buys:** Products that customers purchased
- **Then buys:** Products they also purchased
- **Confidence:** Strength of the relationship (%)
- **Support:** How often items appear together (%)
- **Lift:** How much more likely items are bought together vs independently

---

## Understanding the Results

### Example Rule:
```
If buys:      Lipstick ($25.99)
Then buys:    Mascara ($15.50), Foundation ($45.00)
Confidence:   75%
Support:      15%
```

**Meaning:** 
- 75% of customers who buy Lipstick also buy Mascara and Foundation
- This product combination appears in 15% of all carts
- This is valuable for product recommendations!

---

## Database Structure

### Carts Table:
```sql
- id (Primary Key)
- user_id (Foreign Key)
- status (pending/completed/abandoned)
- total (Decimal)
- timestamps
```

### CartItems Table:
```sql
- id (Primary Key)
- cart_id (Foreign Key)
- shop_product_id (Foreign Key)
- quantity (Integer)
- price (Decimal)
- timestamps
```

---

## Testing with Sample Data

Sample cart data has been seeded! To generate more test data:

```bash
php artisan db:seed --class=CartSeeder
```

This creates:
- 10 users
- 5 carts per user = 50 total carts
- 2-5 products per cart
- Random product combinations

---

## Package Requirements

The following package was added:
- `php-ai/php-ml` - Machine Learning library with Apriori algorithm

Location: `vendor/php-ai/php-ml`

---

## How Data Flows

1. **Customer Checkout:**
   - Session cart items are submitted
   - OrderItem records created (existing functionality)
   - **NEW:** Cart + CartItem records created for analysis

2. **Apriori Analysis:**
   - Extracts all CartItem records grouped by cart_id
   - Creates transaction matrix: [[1,2,3], [2,5], [1,2,5,6], ...]
   - Apriori algorithm finds frequent itemsets
   - Generates association rules (If A then B)

3. **Display:**
   - Rules formatted with product names and prices
   - Shown in admin dashboard
   - Can be used for product recommendations

---

## Advanced Customization

### Change Support/Confidence in Seeder:
Edit `database/seeders/CartSeeder.php`:
```php
// Increase cart count
for ($i = 0; $i < 50; $i++) { // Change 50 to desired number

// Increase items per cart
$randomProducts = $shopProducts->random(rand(5, 15)); // Change range
```

### Modify Algorithm Parameters:
Edit `app/Http/Controllers/Admin/AprioriController.php`:
```php
// Change default thresholds
$support = $request->input('support', 0.1); // 10% default
$confidence = $request->input('confidence', 0.3); // 30% default
```

### Add More Statistics:
Add to `statistics()` method in AprioriController

---

## Troubleshooting

### No Rules Generated?
- Increase cart data: Run seeder multiple times
- Lower support/confidence thresholds
- Check that orders are actually being created

### Routes Not Found?
- Run: `php artisan route:clear`
- Run: `php artisan cache:clear`

### Database Errors?
- Run: `php artisan migrate`
- Run: `php artisan db:seed --class=CartSeeder`

---

## Next Steps

1. **Monitor Real Sales:** Wait for real customer orders to accumulate
2. **Create Recommendations:** Use rules to show "Frequently Bought Together" sections
3. **Optimize Inventory:** Stock products based on association patterns
4. **Marketing:** Create bundle deals based on association rules

---

## Need Help?

All code is fully commented. Check:
- Controller logic: `app/Http/Controllers/Admin/AprioriController.php`
- Database structure: `database/migrations/2024_04_01_000000_create_carts_tables.php`
- Blade view: `resources/views/dashboard/apriori/index.blade.php`

---

**Installation Date:** April 1, 2026
**Status:** ✅ Ready to Use
