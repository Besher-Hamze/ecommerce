<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## Association Rules (Apriori) for Cosmetic E-commerce (Laravel Only)

This project now includes a **Laravel-only** implementation of Association Rule Mining using the **Apriori algorithm** to improve product recommendations.

### Implemented files

- [app/Services/AprioriService.php](app/Services/AprioriService.php)
- [app/Http/Controllers/AssociationRuleController.php](app/Http/Controllers/AssociationRuleController.php)
- [app/Models/AssociationRule.php](app/Models/AssociationRule.php)
- [database/migrations/2026_03_06_000001_create_association_rules_table.php](database/migrations/2026_03_06_000001_create_association_rules_table.php)
- [resources/views/dashboard/association-rules/index.blade.php](resources/views/dashboard/association-rules/index.blade.php)
- [resources/views/website/recommendations.blade.php](resources/views/website/recommendations.blade.php)
- [routes/web.php](routes/web.php)

### Dataset understanding (inside website data)

The mining pipeline uses website behavioral data directly from database relations:

1. **Purchase transactions** from `orders` + `order_items` + `products`.
2. **User intent baskets** from `wishlists` grouped per user.

Each transaction is converted to a basket of product names.

### Methodology

For each candidate rule $X \Rightarrow Y$:

$$
Support(X) = \frac{count(X)}{N}
$$

$$
Confidence(X \Rightarrow Y) = \frac{Support(X \cup Y)}{Support(X)}
$$

$$
Lift(X \Rightarrow Y) = \frac{Confidence(X \Rightarrow Y)}{Support(Y)}
$$

Where $N$ is the number of transactions.

### Added-value strategy beyond basic Apriori

The implementation improves practical recommendation quality by:

- Filtering to rules with `lift > 1` (positive correlation).
- Applying minimum thresholds on `support` and `confidence`.
- Ranking rules by impact score:

$$
Impact = Support \times Confidence \times Lift
$$

- Combining **purchase behavior + wishlist intent** for richer cosmetic recommendations.

### How to run

1. Run migrations:

	`php artisan migrate`

2. Open admin analysis page and execute Apriori:

	`/admin/association-rules?run=1`

3. Check product recommendations:

	`/recommendations?product_id={id}`

### Business insights generated

- Detect products frequently bought together (bundle opportunities).
- Build “Frequently Bought Together” widgets with high-lift rules.
- Improve cross-sell strategy using confidence-weighted recommendations.
- Prioritize category-specific cosmetic relations (e.g., cleanser/toner, lipstick/lip-liner).
