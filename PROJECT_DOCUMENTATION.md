# E-Commerce System — Master's Degree Project Documentation

**Project name:** NourHoran E-Commerce — Skincare & Cosmetics Platform  
**Research focus:** Improving User Experience in E-Commerce Systems  
**Storefront name:** Noor-Store  
**Database:** `nour_ecommerce` (MySQL, localhost:3306)  
**Framework:** Laravel (PHP), Blade templating, Laravel Breeze authentication  
**Session driver:** File (`storage/framework/sessions`)  
**Payment:** Stripe (test keys)  
**Localization:** `mcamara/laravel-localization` (Arabic + English, locale prefix on all web routes)

---

## Table of Contents

1. [Project Idea & Research Focus](#1-project-idea--research-focus)
2. [Platform Domain — Skincare & Cosmetics](#2-platform-domain--skincare--cosmetics)
3. [System Architecture](#3-system-architecture)
4. [Database — `nour_ecommerce`](#4-database--nour_ecommerce)
5. [Products](#5-products)
6. [Cart — Session-Based Storage](#6-cart--session-based-storage)
7. [Virtual (Dummy) Cart — Simulating User Behavior](#7-virtual-dummy-cart--simulating-user-behavior)
8. [Orders](#8-orders)
9. [Seeder — Expanding the Dataset to 2,000 Records](#9-seeder--expanding-the-dataset-to-2000-records)
10. [Dummy Users — Simulating Real Usage](#10-dummy-users--simulating-real-usage)
11. [Recommendation Engine (Apriori)](#11-recommendation-engine-apriori)
12. [Known UI/UX Issue — Apriori Admin Interface (Association Rules)](#12-known-uiux-issue--apriori-admin-interface-association-rules)
13. [Known Issue — Cart Count Not Updating on Listing Pages](#13-known-issue--cart-count-not-updating-on-listing-pages)
14. [Authentication & Roles](#14-authentication--roles)
15. [Key Routes Summary](#15-key-routes-summary)
16. [Technology Stack](#16-technology-stack)

### Known Issues (Documented)

- [Known Issues Summary](#known-issues-summary)
- [Issue 1 — Apriori Admin Interface Layout](#12-known-uiux-issue--apriori-admin-interface-association-rules)
- [Issue 2 — Cart Count on Listing Pages](#13-known-issue--cart-count-not-updating-on-listing-pages)

---

## 1. Project Idea & Research Focus

### Academic Context

This system is the practical component of a master's degree research project. The central research question is:

> **How can e-commerce platforms be designed and optimized to improve the overall user experience for online shoppers?**

User experience (UX) in e-commerce encompasses how intuitively and smoothly a customer can discover products, manage their shopping cart, complete a purchase, and receive personalized recommendations. Poor UX — such as slow pages, confusing navigation, or irrelevant suggestions — directly leads to cart abandonment and lost revenue.

### What This Project Addresses

The project tackles UX improvement from multiple angles:

| UX Challenge | System Solution |
|---|---|
| Slow or clunky cart interactions | Session-based cart with AJAX updates (no page reloads) |
| Irrelevant product suggestions | Apriori market-basket analysis for personalized recommendations |
| Language barrier | Full Arabic + English bilingual interface |
| Friction at checkout | Pre-filled billing details for logged-in users |
| Lack of social proof | Product review and rating system |
| Discovering new products | Wishlist, sliders, and featured product sections |

### Why Skincare & Cosmetics?

The skincare and cosmetics market is one of the fastest-growing segments of online retail. Customers in this category typically browse extensively before buying, compare multiple products, and rely heavily on recommendations and reviews — making it an ideal domain to study and improve UX patterns.

---

## 2. Platform Domain — Skincare & Cosmetics

### What the Platform Sells

The platform specializes in **skincare and cosmetic products**. The catalog is organized into categories and brands relevant to this domain, such as:

- **Categories:** Moisturizers, Serums, Cleansers, Sunscreens, Makeup, Foundations, Lip Care, Eye Care, Body Care, etc.
- **Brands:** Various skincare and cosmetic brands, each linked to their products through the `brands` table.

### Product Characteristics in This Domain

Skincare and cosmetics products have specific traits that shape how the system is built:

- Products often come in **variants** (different sizes, shades, or formulas), reflected in the `shop_products` table where the same base product can appear with different pricing or stock.
- Customers frequently buy **complementary products** together (e.g., a cleanser and a moisturizer), which is exactly what the **Apriori recommendation engine** is designed to detect and surface.
- **Images** are critical in this category — the `images` table supports multiple images per product, and `image_url` on `products` stores the primary image.
- **Reviews and ratings** (`review_ratings` table) are especially influential in cosmetics purchasing decisions.

### Bilingual Support

The platform supports both **Arabic and English**, reflecting the target user base. Product names and descriptions are stored in both languages (`name` / `eng_name`, `description` / `eng_description`) directly on the `products` table. All routes are prefixed with a locale code (e.g., `/en/products`, `/ar/products`).

---

## 3. System Architecture

```
┌───────────────────────────────────────────────────┐
│                    Browser / Client                │
│         (Arabic or English, AJAX-enhanced)         │
└───────────────────────┬───────────────────────────┘
                        │ HTTP
┌───────────────────────▼───────────────────────────┐
│              Laravel Application                   │
│  ┌──────────┐  ┌───────────┐  ┌────────────────┐  │
│  │  Routes  │  │Middleware │  │  Controllers   │  │
│  │ web.php  │─►│  (auth,   │─►│ Store, Order,  │  │
│  │ auth.php │  │  isAdmin, │  │ Product, User, │  │
│  └──────────┘  │  locale)  │  │ Apriori, etc.  │  │
│                └───────────┘  └───────┬────────┘  │
│                                       │            │
│  ┌────────────────────────────────────▼──────────┐ │
│  │              Eloquent Models                  │ │
│  │  Product · Shop_product · Category · Brand   │ │
│  │  Order · OrderItem · Cart · CartItem · User  │ │
│  │  Wishlist · Review · Coupon · AssociationRule│ │
│  └──────────────────┬─────────────────────────── ┘ │
│                     │                              │
│  ┌──────────────────▼───────────────────────────┐  │
│  │       Session Storage (File-based)           │  │
│  │  storage/framework/sessions/  (live cart)    │  │
│  └──────────────────────────────────────────────┘  │
└───────────────────────────────────────────────────┘
                        │
┌───────────────────────▼───────────────────────────┐
│          MySQL Database: nour_ecommerce            │
│  products · shop_products · orders · order_items  │
│  carts · cart_items · users · categories · brands │
│  wishlists · review_ratings · association_rules   │
└───────────────────────────────────────────────────┘
```

---

## 4. Database — `nour_ecommerce`

### Connection Settings (from `.env`)

| Parameter | Value |
|---|---|
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | `127.0.0.1` |
| `DB_PORT` | `3306` |
| `DB_DATABASE` | `nour_ecommerce` |
| `DB_USERNAME` | `root` |
| `DB_PASSWORD` | *(empty — local development)* |

### Core Tables

| Table | Description |
|---|---|
| `users` | Registered customers and admins (`role` column distinguishes them) |
| `categories` | Product categories — supports parent–child hierarchy via `parent_id` (e.g., "Skincare" → "Moisturizers") |
| `brands` | Cosmetic/skincare brand records |
| `shops` | Shop entities that list products |
| `products` | Core product catalog (~1,000 records; bilingual name, description, image) |
| `shop_products` | Pricing/stock table: links a `product` to a `shop` with price, category, brand, and stock count |
| `images` | Additional product images (one product can have many images) |
| `orders` | Customer orders — billing details, totals, and order state |
| `order_items` | Individual line items per order (product, quantity, unit price) |
| `carts` | Persistent cart records written after checkout, used for analytics |
| `cart_items` | Items inside a persistent cart (feeds the Apriori algorithm) |
| `review_ratings` | Customer reviews and star ratings on products |
| `wishlists` | Customer saved/wishlist products |
| `coupons` | Discount coupon codes |
| `sliders` | Homepage promotional banners |
| `settings` | Site-wide key–value configuration |
| `association_rules` | Cached Apriori output (itemsets, support, confidence, lift) |
| `product_states` | Status flags attached to products |
| `messages` | Customer contact/support messages |

> **Design note:** The `products` table stores canonical product data. The `shop_products` table extends it with shop-specific pricing and availability. Most storefront queries join both tables to display the complete product picture.

---

## 5. Products

### Data Structure

Products are split across two tables to cleanly separate universal product information from shop-specific pricing:

```
products
  id | name (AR) | eng_name (EN) | description (AR) | eng_description (EN) | image_url

shop_products
  id | product_id (FK→products) | shop_id (FK→shops) | category_id (FK) | brand_id (FK)
     | price | count | ...
```

A single `Product` can be listed by multiple shops at different prices. The storefront always joins `products` with `shop_products` to present the full product view.

### Product Model — Key Points

- **Model file:** `app/Models/Product.php`
- **Table:** `products`
- **Fillable fields:** `name`, `eng_name`, `description`, `eng_description`, `image_url`
- **Relationships:**
  - `shops()` — many-to-many through `shop_products`
  - `orders()` — many-to-many through `order_items` (with `quantity` pivot)
  - `category()` — resolved via `hasOneThrough` through `shop_products`

### Current Dataset Size

The `products` table currently holds approximately **1,000 records**. The plan to expand this to **2,000 records** using a seeder is detailed in [Section 9](#9-seeder--expanding-the-dataset-to-2000-records).

### Product Detail Page

The detail page route (`/detail/{id}`) uses `products.id` as the URL parameter. The controller joins `products` and `shop_products` on that id to display the full product page — including price, stock, brand, images, and customer reviews.

---

## 6. Cart — Session-Based Storage

### Why the Session?

The live shopping cart — the cart a customer is actively building while browsing — is stored entirely in the **PHP session**, not in the database. This is a deliberate UX decision:

- There is **no database write** every time a user adds, removes, or changes a quantity. This makes cart interactions fast and lightweight.
- The cart is **automatically tied to the user's session** without requiring authentication.
- If the user closes the browser, the session (and cart) expires naturally.
- Only when the user **completes checkout** does the cart data get written to the database.

### Session Cart Structure

The cart lives in the session under the key `'cart'`. It is a PHP associative array:

```
Key   → shop_products.id  (which specific shop listing was added)
Value → array of product details for display purposes
```

Example:

```php
session('cart') = [
    42 => [                        // key = shop_product id
        'id'        => 7,          // products.id (core product)
        'name'      => 'Vitamin C Serum',
        'price'     => 29.99,
        'quantity'  => 2,
        'image_url' => 'uploads/serum.jpg',
    ],
    85 => [
        'id'        => 12,
        'name'      => 'Hydrating Moisturizer',
        'price'     => 19.99,
        'quantity'  => 1,
        'image_url' => 'uploads/moisturizer.jpg',
    ],
]
```

### Cart Operations (`StoreController`)

| Operation | HTTP Method | What Happens |
|---|---|---|
| Add to cart | `POST` | Reads `session('cart')`, adds a new entry or increments quantity if already present |
| Update quantity | `POST` | Overwrites the quantity field for a specific session cart key |
| Remove item | `POST` | Unsets the entry at that key from the session cart array |
| View cart | `GET` | Passes `session('cart')` to the cart Blade view for display |

### AJAX Cart Enhancement

`public/my_js/cart.js` intercepts cart actions and sends them via AJAX, so items can be added or removed **without a full page reload**. This is a direct UX improvement: the page feels instantaneous, which reduces friction and cart abandonment.

### What Happens to the Cart After Checkout

Once an order is placed, `OrderController` does two things:

1. Creates a `Cart` database record with `status = 'completed'` and the final total.
2. Creates a `CartItem` record for each item, linking it to the completed cart.

These completed cart records are the **data source for the Apriori recommendation engine**. The session cart is then cleared.

---

## 7. Virtual (Dummy) Cart — Simulating User Behavior

### Concept

A **virtual cart** (also called a dummy cart or synthetic cart) is a cart record that is **generated programmatically** rather than created by a real user interacting with the website. It is written directly into the `carts` and `cart_items` tables with `status = 'completed'`, exactly as a real checkout would produce.

### Purpose

The virtual cart serves two important purposes in this project:

**1. Enabling the Apriori Algorithm to Run Meaningfully**

The Apriori algorithm needs a large number of past purchase transactions to discover meaningful association rules (e.g., "customers who buy Vitamin C Serum often also buy Sunscreen SPF 50"). With only a small number of real orders or completed carts, the patterns it finds will be statistically weak.

By generating hundreds or thousands of virtual carts — each containing a realistic combination of skincare or cosmetic products — we give the algorithm enough data to produce **reliable, high-confidence recommendations**.

**2. Testing the System Under Realistic Conditions**

Virtual carts allow the system to be tested as if it had been running in production for a long time:
- The recommendation engine can be evaluated against a rich dataset.
- The admin dashboard shows meaningful order and cart volume.
- Performance and query efficiency can be measured under realistic data loads.

### How Virtual Carts Are Structured

A virtual cart is identical in structure to a real completed cart:

```
carts table:
  user_id   → an existing user's id (real or dummy)
  status    → 'completed'
  total     → sum of (price × quantity) for all items

cart_items table:
  cart_id         → the id of the cart above
  shop_product_id → a valid shop_products.id
  quantity        → a realistic random number (e.g., 1–3)
  price           → the price from shop_products at time of creation
```

### The Existing `CartSeeder`

The system already includes a `CartSeeder` (`database/seeders/CartSeeder.php`) that creates virtual carts. Here is what it does:

```
For each of up to 10 users in the database:
  Create 5 completed Cart records
  For each cart, pick 2–5 random shop_products
  Create CartItem records for each picked product
  Calculate and store the cart total
```

This seeder is the foundation of the virtual cart concept. It can be extended to generate more users and more carts per user as the dataset grows (see [Section 9](#9-seeder--expanding-the-dataset-to-2000-records) and [Section 10](#10-dummy-users--simulating-real-usage)).

### Relationship to Real Carts

| Property | Real Cart | Virtual (Dummy) Cart |
|---|---|---|
| Created by | User clicking "Add to Cart" then checking out | `CartSeeder` running directly on the database |
| `status` | `'completed'` (after checkout) | `'completed'` (set by seeder) |
| `cart_items` records | Actual products the user chose | Randomly selected `shop_products` |
| Used for Apriori? | Yes | Yes — indistinguishable from real carts |
| Affects real orders? | Yes | No — carts and orders are separate tables |

---

## 8. Orders

### Order Placement Flow

```
Customer reviews session cart
          │
          ▼
Customer fills billing form
  (first_name, last_name, email, mobile, address, city, ...)
          │
          ▼
OrderController::store() executes:
  1. Validates all billing fields
  2. Reads session('cart') to calculate total_price and total_quantity
  3. Creates a record in the `orders` table
  4. Creates one `order_items` record per cart item
  5. Creates a `Cart` (status='completed') + `CartItem` records for analytics
  6. Clears the session cart
  7. Redirects to order confirmation page
```

### Orders Table — Key Columns

| Column | Description |
|---|---|
| `user_id` | FK → `users.id` |
| `first_name` | Billing first name |
| `last_name` | Billing last name |
| `email` | Billing email |
| `mobile` | Customer phone |
| `address` | Delivery address |
| `total_price` | Full order value |
| `total_quantity` | Total units ordered |
| `state` | Order status (pending, processing, shipped, delivered, etc.) |
| `company` | Optional billing company name |

### Order Items Table — Key Columns

| Column | Description |
|---|---|
| `order_id` | FK → `orders.id` |
| `product_id` | References `products.id` |
| `quantity` | Units of this product in the order |
| `unit_price` | Price per unit at the time of ordering |

### Models

- **`Order`** (`app/Models/Order.php`): belongs to `User`; many-to-many with `Product` through `order_items`.
- **`OrderItem`** (`app/Models/OrderItem.php`): pivot-style model bound to the `order_items` table.

### Admin Order Dashboard

The `OrderDashController` provides administrators with a view of all orders, filterable by status, with full line-item breakdowns per order.

---

## 9. Seeder — Expanding the Dataset to 2,000 Records

### Current State

The `nour_ecommerce` database currently holds approximately **1,000 product records** in the `products` table, each with a corresponding entry in `shop_products`. The `DatabaseSeeder.php` is currently empty (all content is commented out), and the existing `CartSeeder.php` generates synthetic cart history.

### Goal

Expand to approximately **2,000 product records** to:
- Provide a richer and more realistic product catalog for the skincare/cosmetics store.
- Give the Apriori engine a larger item pool to discover more diverse association rules.
- Create a more realistic dataset for academic evaluation of the system.

### The Duplicate Problem — Why It Must Be Handled Carefully

Simply running a seeder that calls `Product::create(...)` in a loop would try to insert new rows **regardless of whether they already exist**, resulting in:
- Duplicate product names in the catalog.
- Broken foreign key relationships if re-run multiple times.
- A database that no longer reflects reality.

The seeder must be written to be **idempotent** — safe to run multiple times with the same result.

### The Solution: `firstOrCreate`

Eloquent's `firstOrCreate` method checks whether a matching record already exists before inserting:

```php
// Only creates the record if no product with this name exists yet
$product = Product::firstOrCreate(
    ['name' => $productName],           // uniqueness check
    [
        'eng_name'        => $engName,
        'description'     => $description,
        'eng_description' => $engDescription,
        'image_url'       => $imageUrl,
    ]
);
```

Applied the same way to `shop_products`:

```php
Shop_product::firstOrCreate(
    ['product_id' => $product->id, 'shop_id' => $shopId],  // uniqueness check
    [
        'price'       => $price,
        'category_id' => $categoryId,
        'brand_id'    => $brandId,
        'count'       => $stock,
    ]
);
```

### Proposed `ProductSeeder`

```php
<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shop_product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $targetTotal  = 2000;
        $currentCount = Product::count();

        if ($currentCount >= $targetTotal) {
            $this->command->info("Already have {$currentCount} products. Nothing to add.");
            return;
        }

        $toCreate = $targetTotal - $currentCount;
        $this->command->info("Adding {$toCreate} products to reach {$targetTotal} total...");

        // Use valid existing IDs from your database
        $shopId     = 1;
        $categoryId = 1;  // e.g., "Moisturizers"
        $brandId    = 1;  // e.g., a skincare brand

        for ($i = 1; $i <= $toCreate; $i++) {
            $name = "Skincare Product {$i}";

            $product = Product::firstOrCreate(
                ['name' => $name],
                [
                    'eng_name'        => "Skincare Product {$i} EN",
                    'description'     => "منتج عناية بالبشرة رقم {$i}.",
                    'eng_description' => "Auto-generated skincare product number {$i}.",
                    'image_url'       => 'uploads/default.jpg',
                ]
            );

            Shop_product::firstOrCreate(
                ['product_id' => $product->id, 'shop_id' => $shopId],
                [
                    'price'       => rand(5, 200),
                    'category_id' => $categoryId,
                    'brand_id'    => $brandId,
                    'count'       => rand(10, 150),
                ]
            );
        }

        $this->command->info("Done. Total products: " . Product::count());
    }
}
```

### How to Register and Run the Seeder

**Step 1 — Register in `DatabaseSeeder.php`:**

```php
public function run(): void
{
    $this->call([
        ProductSeeder::class,
        // CartSeeder::class,  // run after ProductSeeder if needed
    ]);
}
```

**Step 2 — Run safely (no data is wiped):**

```bash
php artisan db:seed --class=ProductSeeder
```

> **Critical:** Never run `php artisan migrate:fresh --seed` on a database with real data. The `migrate:fresh` command drops all tables first, destroying all existing records. Use `db:seed --class=` to add data without touching existing records.

### Full Seeder Execution Order

```
1. ProductSeeder   → safely adds products + shop_products up to 2,000 total
2. UserSeeder      → creates dummy users for simulation (see Section 10)
3. CartSeeder      → generates virtual completed carts for Apriori training data
```

---

## 10. Dummy Users — Simulating Real Usage

### Why Dummy Users Are Needed

The system's analytical features — primarily the Apriori recommendation engine and the virtual cart concept — depend on having data that represents **many different users** making purchases. With only a handful of real registered users, the purchase patterns are too sparse to be meaningful.

Dummy users are **fake accounts generated by the seeder** that exist solely to:
1. Be assigned virtual carts and orders, making the data look like it came from a real user base.
2. Allow the system to be tested as if it had a realistic number of active customers.
3. Provide diverse data for the Apriori algorithm, since different dummy users can be assigned different product combinations.

### What a Dummy User Looks Like

A dummy user is a normal record in the `users` table. The only difference from a real user is that it was created by a seeder rather than by someone registering on the site:

```
users table:
  name       → "Test User 1" (or realistic fake name using Faker library)
  email      → "user1@test.com" (or faker->unique()->safeEmail())
  password   → bcrypt('password')  (a known test password)
  role       → 'user'
  first_name → (optional fake data)
  last_name  → (optional fake data)
  city       → (optional fake data)
  phone      → (optional fake data)
```

### Proposed `UserSeeder`

Laravel's `Faker` library (already included in Laravel via `fakerphp/faker`) can generate realistic-looking fake user data:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $targetTotal  = 100;  // generate 100 dummy users
        $currentCount = User::where('role', 'user')->count();

        if ($currentCount >= $targetTotal) {
            $this->command->info("Already have {$currentCount} users. Nothing to add.");
            return;
        }

        $toCreate = $targetTotal - $currentCount;
        $faker    = \Faker\Factory::create();

        for ($i = 1; $i <= $toCreate; $i++) {
            $email = "dummy_user_{$i}@test.com";

            User::firstOrCreate(
                ['email' => $email],   // email is unique — prevents duplicates
                [
                    'name'       => $faker->name(),
                    'first_name' => $faker->firstName(),
                    'last_name'  => $faker->lastName(),
                    'password'   => Hash::make('password'),
                    'city'       => $faker->city(),
                    'phone'      => $faker->phoneNumber(),
                    'role'       => 'user',
                ]
            );
        }

        $this->command->info("Done. Total regular users: " . User::where('role', 'user')->count());
    }
}
```

Run it:

```bash
php artisan db:seed --class=UserSeeder
```

### Connecting Dummy Users to Virtual Carts

Once dummy users exist, the `CartSeeder` can assign virtual carts to them. The existing `CartSeeder` already does this — it fetches up to 10 users and creates 5 completed carts per user. By expanding it to target more dummy users, the virtual cart dataset grows proportionally, feeding richer data into the Apriori engine.

### Important Note on Duplicate Prevention

Just like with products, the `UserSeeder` uses `firstOrCreate` with the `email` field as the uniqueness key. Since every user's email must be unique in the `users` table, this guarantees no duplicate accounts are ever created, even if the seeder is run multiple times.

---

## 11. Recommendation Engine (Apriori)

### Purpose in This Project

The Apriori algorithm is one of the main technical contributions of this project toward improving UX. Instead of showing random or generic suggestions, the system learns from actual (and simulated) purchase history to recommend products that genuinely complement each other in the skincare/cosmetics domain.

### How It Works

```
Completed carts / orders in the database
          │
          ▼
Each cart/order = one "transaction" (a set of product IDs bought together)
          │
          ▼
AprioriService groups transactions and mines frequent itemsets
(e.g., {Vitamin C Serum, Sunscreen} appears in 40% of transactions → high support)
          │
          ▼
Association rules are generated:
  {Vitamin C Serum} → {Sunscreen}  [confidence: 75%, lift: 2.1]
          │
          ▼
Rules are stored in `association_rules` table
          │
          ▼
Storefront shows "Customers also buy..." on product pages
```

### Key Metrics

| Metric | Meaning |
|---|---|
| **Support** | How often a set of items appears across all transactions |
| **Confidence** | Given item A was bought, how often is item B also bought |
| **Lift** | How much more likely the combination is compared to random chance (lift > 1 = meaningful) |

### Why the Virtual Cart Data Matters for Apriori

The algorithm needs a **minimum threshold of transactions** to produce statistically reliable rules. With only real user data (~1,000 products, limited users), the transactions may be too few or too uniform to find interesting patterns. The virtual carts generated by `CartSeeder` provide the volume needed to make the algorithm produce meaningful, high-quality recommendations.

---

---

## Known Issues Summary

This project explicitly targets **user experience improvement**. Two frontend issues have been identified that currently undermine that goal. Both are documented below for the master's degree project record. **No code changes have been made as part of this documentation update.**

| # | Area | Issue | UX Impact |
|---|---|---|---|
| 1 | Admin — Apriori / Association Rules | Minimum Support and Minimum Confidence fields render outside the main content container | Admin interface looks broken; analysis controls are hard to find and use |
| 2 | Storefront — Cart (Noor-Store) | Cart counter in the navbar stays at **0** when adding from listing/home pages; works correctly on the product detail page | Customers receive no feedback that items were added; inconsistent behavior erodes trust in the cart |

---

## 12. Known UI/UX Issue — Apriori Admin Interface (Association Rules)

### Issue Type

UI layout / CSS class incompatibility — Admin Panel (`/admin/apriori`)

### Description

In the admin dashboard, under the **Apriori analysis / Association Rules** section, the input fields for **Minimum Support** and **Minimum Confidence** are not properly contained within the main content layout. Instead of appearing inside a structured panel — aligned consistently with the rest of the admin dashboard — they render **outside the main content container**, floating in the page background.

As observed at `http://127.0.0.1:8000/en/admin/apriori`:

- The statistics cards at the top (**Unique Products**, **Avg Items/Cart**, **Total Items**) display correctly.
- The **Minimum Confidence (%)** label and input (default value `50`) appear detached from any card or panel boundary.
- The **Minimum Support (%)** input (default value `20`) is pushed toward the edge of the page and may overlap the dark sidebar area on RTL layouts.
- Helper text (e.g., *"Items must appear in at least this % of carts"*) can be partially cut off or hidden behind the sidebar.

This is a **UI/UX problem**: all input fields in the Association Rules interface should be fully enclosed within the main layout, aligned side by side, and visually consistent with every other admin page (Products, Orders, Categories).

### Requirements for Correct Behavior

1. **All input fields** (Minimum Support, Minimum Confidence, and the Generate Rules button) must be **properly contained** within the main content panel — not floating outside it.
2. **Minimum Support** and **Minimum Confidence** must be **aligned consistently** in the same row, using the same grid and spacing as other admin forms.
3. The page must **respect the sidebar margin** on RTL layouts so fields do not overlap the navigation menu.
4. The overall layout must **match the FlatLab Bootstrap 3 admin theme** used across the rest of the dashboard.

### Root Cause

The admin dashboard is built on the **FlatLab Bootstrap 3 RTL template** (`resources/views/dashboard/master.blade.php`). Every working admin page wraps its content in this hierarchy:

```html
<section id="main-content" dir="rtl">
    <section class="wrapper site-min-height">
        <section class="panel">
            <header class="panel-heading">Page Title</header>
            <div class="panel-body">
                <!-- all page content here -->
            </div>
        </section>
    </section>
</section>
```

The Apriori page (`resources/views/dashboard/apriori/index.blade.php`) was originally written using **Bootstrap 4/5 class names** — `card`, `card-body`, `card-header`, `container-fluid`, `mb-4`, `badge bg-success`, `table-dark` — which are **not defined** in the Bootstrap 3 stylesheet loaded by the master layout. Because Bootstrap 3 does not recognize these classes, the card/container structure collapses and the form fields fall out of the normal layout flow.

The related **Association Rules page** (`resources/views/dashboard/association-rules/index.blade.php`) correctly uses Bootstrap 3 panel markup (`panel`, `panel-body`, `col-md-*`, `form-control`) and renders without alignment problems — confirming the issue is specific to the Apriori view's markup.

### Affected Files

| File | Problem |
|---|---|
| `resources/views/dashboard/apriori/index.blade.php` | Uses Bootstrap 4/5 classes inside a Bootstrap 3 master; originally missing `#main-content` → `.wrapper` → `.panel` wrapper |
| `resources/views/dashboard/master.blade.php` | Loads Bootstrap 3 CSS (FlatLab theme) — does not define `card`, `card-body`, or other Bootstrap 4/5 utilities |

### Affected Input Fields

| Field | Expected Position | Observed Position |
|---|---|---|
| **Minimum Support (%)** | Inside the panel, side by side with Confidence, within the main content area | Renders outside the content container; may overlap the sidebar |
| **Minimum Confidence (%)** | Inside the panel, side by side with Support, within the main content area | Renders outside the content container, floating in the page background |

### Impact on User Experience

- **Visual inconsistency:** Every other admin page looks structured and polished. The Apriori page looks broken, which is especially problematic on a feature the project highlights (the recommendation engine).
- **Reduced usability:** An administrator may not immediately recognize the floating fields as the analysis controls.
- **Loss of trust:** Interface inconsistency undermines the impression of a complete, professional system — directly contradicting the project's UX research focus.
- **RTL disruption:** Without `dir="rtl"` on the content section, text and field alignment may be incorrect on Arabic-locale visits.

### Suggested Fix (Documentation Only — No Code Changed)

Restructure `resources/views/dashboard/apriori/index.blade.php` to follow the same pattern as `association-rules/index.blade.php`:

| Change | Incorrect (Bootstrap 4/5) | Correct (Bootstrap 3 / FlatLab) |
|---|---|---|
| Outer wrapper | `<div class="container-fluid">` | `<section id="main-content" dir="rtl">` → `.wrapper` → `.panel` |
| Container | `<div class="card">` + `<div class="card-body">` | `<div class="panel-body">` |
| Section header | `<div class="card-header">` | `<header class="panel-heading">` |
| Form helper text | `form-text text-muted` | `help-block` |
| Statistics boxes | `<div class="card">` | `<div class="panel panel-default">` |
| Grid columns | `col-12` | `col-md-6` / `col-md-12` |

The Minimum Support and Minimum Confidence fields should sit in a single `<div class="row">` with two `<div class="col-md-6">` columns, both inside `panel-body`:

```html
<section id="main-content" dir="rtl">
    <section class="wrapper site-min-height">
        <section class="panel">
            <header class="panel-heading">Apriori Association Rules Analysis</header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <label>Minimum Support (%)</label>
                        <input type="number" class="form-control" ...>
                        <small class="help-block">Items must appear in at least this % of carts</small>
                    </div>
                    <div class="col-md-6">
                        <label>Minimum Confidence (%)</label>
                        <input type="number" class="form-control" ...>
                        <small class="help-block">If A is bought, probability of buying B</small>
                    </div>
                </div>
            </div>
        </section>
    </section>
</section>
```

> **Note:** This section documents the issue and proposed solution for the master's degree project. No backend logic, routes, or controllers need to change — the fix is entirely in the Blade view markup and CSS class names.

---

## 13. Known Issue — Cart Count Not Updating on Listing Pages

### Issue Type

Frontend JavaScript / AJAX inconsistency — Customer-facing storefront (**Noor-Store**)

### Observed Behavior

There is an **inconsistency in cart behavior** depending on which page a customer uses to add a product:

| Page | "Add to Cart" Action | Cart Counter in Navbar (`#cart_counter`) | User Feedback |
|---|---|---|---|
| **Homepage / product listing** (Shop, category grids) | Customer clicks "Add to cart" on a product card | Counter stays at **0** — does not increment | No toast notification; no visual confirmation |
| **Product detail page** (`/en/detail/{id}`) | Customer clicks "Add to cart" with quantity | Counter **increments correctly** (+1) | Green success toast: *"Product added to cart successfully."* |

Screenshots from the **Noor-Store** storefront confirm this:

- On the homepage and shop listing pages, the shopping bag icon in the header shows a badge of **0** even after clicking "Add to cart" on mascara and skincare products.
- On the product detail page (e.g., L'Oreal Paris Voluminous Mascara at `/en/detail/930`), clicking "Add to cart" shows the success notification and the cart count updates as expected.

This inconsistency is confusing for customers and directly undermines the project's goal of improving e-commerce UX. The cart is one of the most important interaction points in any online store — customers expect immediate, consistent feedback regardless of where they add a product.

### Expected Behavior

Adding a product to the cart from **any page** — homepage, shop listing, category page, search results, or product detail — should:

1. Successfully send a request to the server and update the **session cart**.
2. Show a **success toast notification** (e.g., via Toastr).
3. **Immediately increment** the `#cart_counter` badge in the navbar by 1 (or by the selected quantity).
4. Behave **identically** regardless of which view triggered the action.

### Root Cause — Technical Analysis

Inspecting the codebase reveals that listing pages and the detail page use **two completely separate JavaScript implementations** for "add to cart", defined in `public/my_js/cart.js`. Both implementations contain counter-update logic (`++spanText`), so the counter code itself is not missing — the listing-page AJAX call **never reaches the success callback** because the request fails silently.

#### Implementation A — Listing / Homepage (broken)

**Trigger:** buttons with class `add-to-cart` in `resources/views/website/product-card.blade.php`

```javascript
$(".add-to-cart").on("click", function (event) {
    var id = $(this).data("id");

    $.ajax("/store/add-to-cart/" + id, {
        type: "get",
        success: function (res) {
            toastr.success(res.status);
            var spanText = document.getElementById('cart_counter').textContent;
            document.getElementById('cart_counter').textContent = ++spanText;
        },
        // no error handler — failures are completely silent
    });
});
```

**Blade template (`product-card.blade.php`):**

```html
<a class="btn ... add-to-cart"
   data-id="{{ $product->id }}"
   type="button">
    Add to cart
</a>
```

Note: the button has `data-id` only — **no `data-route` attribute** with a Laravel-generated URL.

#### Implementation B — Product Detail Page (working)

**Trigger:** button with `id="btn"` in `resources/views/website/details.blade.php`

```javascript
$(document).on("click", "#btn", function () {
    var id    = $("#id").val();
    var qty   = $("#qty").val();
    var route = $("#route").val();   // localized URL from Blade

    $.ajax({
        type: "post",
        url: route,
        data: { id: id, qty: qty },
        success: function (res) {
            toastr.success(res.status);
            var spanText = document.getElementById('cart_counter').textContent;
            document.getElementById('cart_counter').textContent = ++spanText;
        },
    });
});
```

**Blade template (`details.blade.php`):**

```html
<input name="id" id="id" type="hidden" value="{{ $product->id }}">
<input name="route" id="route" type="hidden" value="{{ route('cart.detail.store') }}">
<button type="button" id="btn">Add to cart</button>
```

The detail page passes the correct localized route via Laravel's `route()` helper and uses **POST**, matching the registered route in `routes/web.php`:

```php
Route::post('detail/add', [StoreController::class, 'addToCartFromDetail'])
    ->name('cart.detail.store');
```

#### Why Implementation A Fails — Three Contributing Factors

**Factor 1 — The listing-page route is commented out (primary cause)**

In `routes/web.php`, the route that `cart.js` calls from listing pages is **disabled**:

```php
// Route::get('store/add-to-cart/{id}', [StoreController::class, 'addToCart'])->name('cart.store'); // غير مستخدم
```

The JavaScript hardcodes `/store/add-to-cart/{id}`, but this route does not exist in the active route table. The server returns **404 Not Found**, the `success` callback never runs, and because no `error` handler is defined, the failure is completely invisible to the user.

**Factor 2 — Hardcoded URL missing the locale prefix**

The application uses `mcamara/laravel-localization`, which prefixes all web routes with a locale code (`/en/...`, `/ar/...`). Even if the route were uncommented, the hardcoded path `/store/add-to-cart/{id}` would still miss the `/en` prefix. The detail page avoids this by using `route('cart.detail.store')`, which always generates the correct localized URL.

**Factor 3 — HTTP method mismatch**

Implementation A uses `type: "get"`. The working detail route is registered as **POST**. Cart mutation endpoints should use POST (with CSRF protection), not GET.

**Factor 4 — Possible ID mismatch (secondary)**

`StoreController::addToCart($id)` looks up products by `shop_products.id`:

```php
->where('shop_products.id', '=', $id)
```

On listing pages, `product-card.blade.php` passes `$product->id`, which from a `Product::join('shop_products', ...)` query is likely **`products.id`**, not `shop_products.id`. On the detail page, `$product->id` is explicitly selected as `shop_products.id`. Even after fixing the route, the listing page may need to pass `shop_products.id` instead.

### Summary of Differences

| Aspect | Listing Page (broken) | Detail Page (working) |
|---|---|---|
| Blade file | `product-card.blade.php` | `details.blade.php` |
| JS trigger | `.add-to-cart` (class) | `#btn` (id) |
| HTTP method | `GET` | `POST` |
| URL source | Hardcoded `/store/add-to-cart/{id}` | `#route` hidden input via `route('cart.detail.store')` |
| Route registered? | **No** — commented out in `web.php` | **Yes** — `POST detail/add` |
| Locale prefix | Missing (`/en/` not included) | Included automatically by `route()` |
| CSRF token | Not sent | Sent via `$.ajaxSetup` |
| Error handler | None — failures are silent | None (but request succeeds) |
| Counter update | Never reached | Works correctly |
| Toast notification | Never shown | Shown on success |

### Affected Files

| File | Role |
|---|---|
| `public/my_js/cart.js` | Contains both cart implementations; listing version uses broken hardcoded URL |
| `resources/views/website/product-card.blade.php` | Listing "Add to cart" button — missing `data-route`, may pass wrong ID |
| `resources/views/website/details.blade.php` | Detail "Add to cart" button — working reference implementation |
| `resources/views/website/header.blade.php` | Defines `#cart_counter` badge: `{{ count((array) session('cart')) }}` |
| `routes/web.php` | `cart.store` route is commented out; only `cart.detail.store` is active |
| `app/Http/Controllers/StoreController.php` | `addToCart()` (listing) and `addToCartFromDetail()` (detail) — both write to session |

### Impact on User Experience

- **No feedback on listing pages:** Customers click "Add to cart" and see nothing happen — the counter stays at 0, no toast appears. They may think the button is broken.
- **Inconsistent behavior:** The same action produces different results on different pages, which is one of the most damaging UX patterns in e-commerce.
- **Cart abandonment risk:** If customers cannot trust that items were added, they are less likely to proceed to checkout.
- **Contradicts project goals:** This project focuses specifically on improving e-commerce UX; a broken cart interaction on the primary browsing pages is a critical gap.

### Suggested Fix (Documentation Only — No Code Changed)

The goal is to **unify cart behavior** so listing pages work the same way as the detail page:

**Step 1 — Re-enable or replace the listing route in `routes/web.php`:**

```php
Route::post('store/add-to-cart/{id}', [StoreController::class, 'addToCart'])
    ->name('cart.store');
```

**Step 2 — Add `data-route` to the product card button in `product-card.blade.php`:**

```html
<a class="btn ... add-to-cart"
   data-id="{{ $product->shop_product_id }}"
   data-route="{{ route('cart.store', $product->shop_product_id) }}">
    Add to cart
</a>
```

(Ensure the listing query selects and exposes `shop_products.id`.)

**Step 3 — Update Implementation A in `cart.js` to match Implementation B:**

```javascript
$(".add-to-cart").on("click", function (event) {
    event.preventDefault();
    var route = $(this).data("route");
    var id    = $(this).data("id");

    $.ajaxSetup({
        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") }
    });

    $.ajax({
        url:  route,
        type: "post",
        data: { id: id, qty: 1 },
        success: function (res) {
            toastr.success(res.status);
            var spanText = parseInt(document.getElementById('cart_counter').textContent, 10);
            document.getElementById('cart_counter').textContent = spanText + 1;
        },
        error: function (xhr) {
            toastr.error("Could not add to cart. Please try again.");
            console.error(xhr.status, xhr.responseText);
        }
    });
});
```

**Step 4 (optional, recommended) — Extract a shared function** used by both `.add-to-cart` and `#btn` so cart updates are handled in one place and cannot drift apart again.

> **Note:** This section is documentation only. No changes have been made to the codebase.

---

## 14. Authentication & Roles

Authentication is handled by **Laravel Breeze** (email + password login). The `users` table has a `role` column:

| Role | Access |
|---|---|
| `user` | Browse products, manage cart, place orders, write reviews, manage wishlist |
| `admin` | All customer features + product/order management, run Apriori, view dashboard |

Admin routes are protected by the `isAdmin` middleware and grouped under the `/admin` URL prefix.

Session regeneration is performed on both login and logout to prevent session fixation attacks. The CSRF token is also refreshed on logout.

---

## 15. Key Routes Summary

| Route | Controller | Description |
|---|---|---|
| `GET /` | `HomeController@index` | Homepage — featured skincare products |
| `GET /products` | `ProductController@index` | Full product catalog |
| `GET /detail/{id}` | `ProductController@showDetail` | Single product detail with reviews |
| `POST /cart/add` | `StoreController@addToCart` | Add item to session cart |
| `POST /cart/update` | `StoreController@updateCart` | Update quantity in session cart |
| `POST /cart/remove` | `StoreController@removeFromCart` | Remove item from session cart |
| `GET /cart` | `StoreController@viewCart` | View current session cart |
| `GET /checkout` | `OrderController@index` | Checkout form *(auth required)* |
| `POST /checkout` | `OrderController@store` | Place an order *(auth required)* |
| `GET /orders` | `UserController@orders` | Customer's order history |
| `GET /admin` | `AdminController@index` | Admin dashboard |
| `GET /admin/products` | `ProductController@adminIndex` | Manage product catalog |
| `GET /admin/orders` | `OrderDashController@index` | Manage orders |
| `GET /admin/apriori` | `AprioriController@index` | Run / view Apriori results |

> All routes carry a locale prefix (e.g., `/en/products`, `/ar/products`) due to `mcamara/laravel-localization`.

---

## 16. Technology Stack

| Layer | Technology |
|---|---|
| Backend framework | Laravel (PHP) |
| Frontend templating | Blade — partial views for header, footer, product cards, etc. |
| Authentication | Laravel Breeze (web) + Laravel Passport (API tokens) |
| Database | MySQL — `nour_ecommerce` |
| Session storage | File-based (`storage/framework/sessions`) — holds live cart data |
| Payment | Stripe (test mode integration) |
| Localization | `mcamara/laravel-localization` — full Arabic + English support |
| Recommendations | Custom Apriori implementation (`app/Services/AprioriService.php`) |
| JS enhancements | Vanilla JS (`public/my_js/cart.js`) — AJAX cart interactions |
| Fake data generation | Faker (`fakerphp/faker`) — used in seeders for dummy users |
| Dev environment | Laravel Artisan CLI, Composer, local MySQL |

---

## Summary of the Simulation & Data Strategy

The table below shows how real data and simulated (dummy) data work together in this project:

| Data Type | Source | Purpose |
|---|---|---|
| Real products (~1,000) | Manually added via admin | Core catalog |
| Seeded products (~1,000 new) | `ProductSeeder` | Expand catalog to 2,000 for richer analysis |
| Real users | Self-registered on the site | Actual customer base |
| Dummy users (~100) | `UserSeeder` | Simulate a larger user base for testing |
| Real completed carts | Live checkouts | Actual purchase data |
| Virtual (dummy) carts | `CartSeeder` | Simulate purchase history for Apriori training |
| Association rules | `AprioriService` computation | Power the "Customers also buy" UX feature |

---

*This document describes the Noor-Store e-commerce system as it currently stands, including known UX issues documented for the master's degree project. No code changes were made while producing this documentation.*
