# Noor-Store

Laravel cosmetics e-commerce store with a recommendation engine built for a master's project on **UI/UX improvement**. Stack: Laravel 9, PostgreSQL, Blade + Bootstrap 5 storefront.

## Algorithms

| Goal | Method | Where it shows |
|------|--------|----------------|
| **Cross-selling** | Multi-level **Apriori** (product + product-type rules) hybrid-filled with item-CF neighbours | Product “Frequently bought together”, cart “Complete your look” |
| **Upselling** | **TF-IDF** content similarity + multi-criteria utility (price uplift, Bayesian rating) | Product upgrade card, cart line “Swap” |
| **Personalisation (3rd)** | **Item-based collaborative filtering** (cosine + significance shrinkage) | Home “Picked for you”, “Customers also liked”, `/recommendations` |

Offline training writes into `association_rules` and `product_recommendations`. Online serving is a single indexed read (+ jsonb containment for cart-level rules). Events (impression / click / add_to_cart / purchase) feed the admin CTR dashboard.

## Setup

1. PostgreSQL database `nour_ecommerce` (user/password as in `.env.example`).
2. Copy `.env.example` → `.env`, set `DB_*`, run `php artisan key:generate`.
3. Optional dataset refresh: `python database/data/prepare_dataset.py`
4. Migrate & seed:

```bash
php artisan migrate:fresh --seed
php artisan recommendations:train
php artisan recommendations:evaluate
php artisan serve --port=8001
```

Open `http://127.0.0.1:8001` (locale redirects to `/en`). Use another port if `8000` is already taken.

### Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@noor-store.test` | `password` |
| Demo shopper | `demo@noor-store.test` | `password` |

Admin recommendation dashboard: `/admin/recommendations`  
Association rules explorer: `/admin/association-rules`

### Artisan

- `php artisan recommendations:train {--min-support=} {--min-confidence=}`
- `php artisan recommendations:evaluate {--k=5}`

Training is also scheduled weekly (Sundays 03:00) via the console kernel.

## Evaluation snapshot (K=5, temporal hold-out)

Hybrid Apriori+CF cross-sell HitRate@5 ≈ **0.59** (beats multilevel Apriori alone and popularity / random baselines). Item-CF is strongest for next-basket personalisation among the compared methods.

## Project layout (recommendations)

- `app/Services/Recommendation/` — Apriori, TfIdf, UpsellRanker, ItemBasedCF, CrossSellBuilder, Trainer, Evaluator, RecommendationService, EventTracker
- `app/Console/Commands/TrainRecommendations.php`, `EvaluateRecommendations.php`
- `database/migrations/2026_03_06_000100_create_recommendation_tables.php`
- Storefront widgets in `resources/views/website/{details,cart,index}.blade.php`
- Admin UI in `resources/views/dashboard/recommendations/`
