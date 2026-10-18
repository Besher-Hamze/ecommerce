<?php

namespace Database\Seeders;

use App\Services\ProductStats;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Behavioural data simulator ("virtual customers").
 *
 * Instead of random baskets (which give Apriori nothing to discover), every
 * synthetic customer has:
 *   - a segment (preferred product types, e.g. "eye_artist"),
 *   - a price tier (budget / mid / premium, relative to each product type),
 *   - one or two favourite brands.
 * Every product has a latent quality that drives both its popularity and the
 * stars it receives. Orders are either a makeup *routine* (an anchor product
 * plus complementary products, usually of the same brand - the co-purchase
 * patterns cross-selling should learn) or independent picks driven by the
 * customer profile. Product views, wishlists and reviews are generated from the
 * same profiles, so every algorithm is trained on consistent behaviour.
 */
class ShopperSimulationSeeder extends Seeder
{
    private const CUSTOMERS = 500;
    private const ROUTINES = 170;
    private const ABANDONED_CARTS = 160;

    private const SEGMENTS = [
        'lip_lover' => [0.22, ['lipstick' => 5, 'lip_liner' => 3, 'blush' => 1, 'nail_polish' => 1]],
        'eye_artist' => [0.22, ['mascara' => 4, 'eyeliner' => 4, 'eyeshadow' => 3, 'eyebrow' => 2]],
        'base_perfectionist' => [0.20, ['foundation' => 5, 'bronzer' => 3, 'blush' => 2]],
        'nail_fan' => [0.10, ['nail_polish' => 6, 'lipstick' => 1]],
        'full_glam' => [0.16, ['lipstick' => 1, 'lip_liner' => 1, 'mascara' => 1, 'eyeliner' => 1, 'eyeshadow' => 1,
            'eyebrow' => 1, 'foundation' => 1, 'blush' => 1, 'bronzer' => 1, 'nail_polish' => 1]],
        'natural_minimalist' => [0.10, ['mascara' => 2, 'foundation' => 2, 'eyebrow' => 2, 'lipstick' => 1, 'blush' => 1]],
    ];

    private const TIERS = [
        'budget' => [0.4, ['low' => 3.0, 'mid' => 2.0, 'high' => 0.6, 'premium' => 0.3]],
        'mid' => [0.4, ['low' => 1.2, 'mid' => 3.0, 'high' => 2.0, 'premium' => 0.8]],
        'premium' => [0.2, ['low' => 0.3, 'mid' => 1.0, 'high' => 2.5, 'premium' => 3.0]],
    ];

    /** What people buy together with an anchor product type (makeup routines). */
    private const COMPLEMENTS = [
        'lipstick' => ['lip_liner' => 0.6, 'blush' => 0.2, 'nail_polish' => 0.2],
        'lip_liner' => ['lipstick' => 1.0],
        'mascara' => ['eyeliner' => 0.55, 'eyeshadow' => 0.25, 'eyebrow' => 0.2],
        'eyeliner' => ['mascara' => 0.65, 'eyeshadow' => 0.35],
        'eyeshadow' => ['eyeliner' => 0.5, 'mascara' => 0.5],
        'eyebrow' => ['mascara' => 0.5, 'eyeliner' => 0.5],
        'foundation' => ['blush' => 0.45, 'bronzer' => 0.45, 'mascara' => 0.1],
        'blush' => ['bronzer' => 0.6, 'foundation' => 0.25, 'lipstick' => 0.15],
        'bronzer' => ['blush' => 0.65, 'foundation' => 0.35],
        'nail_polish' => ['nail_polish' => 0.75, 'lipstick' => 0.25],
    ];

    private const NATURAL_TAGS = ['natural', 'organic', 'vegan', 'gluten free', 'certclean', 'ewg verified', 'usda organic'];

    private const CITIES = ['Damascus', 'Aleppo', 'Homs', 'Latakia', 'Hama', 'Tartus', 'Daraa', 'As-Suwayda'];

    private const REVIEWS = [
        5 => ['Absolutely love this {type}! The colour payoff is amazing.', 'My new holy grail {type}, it lasts all day.',
            'Worth every penny, I will definitely repurchase.', 'Perfect shade and super comfortable to wear.',
            'Best {type} I have tried so far. Highly recommended!'],
        4 => ['Really nice {type}, comfortable and long lasting.', 'Great quality for the price.',
            'Very pretty finish, packaging could be better.', 'I like it a lot, it goes perfectly with my routine.'],
        3 => ['It is okay, does the job but nothing special.', 'Decent {type}, needs a couple of layers.',
            'Average quality, the shade is slightly different from the picture.'],
        2 => ['Not long lasting, I expected more.', 'The texture is a bit dry for me.'],
        1 => ['Disappointed, I would not buy it again.', 'Did not work for me at all.'],
    ];

    private array $products = [];
    private array $byType = [];
    private Carbon $now;

    public function run()
    {
        mt_srand(2026);
        $this->now = Carbon::now()->startOfHour();
        $this->loadProducts();
        $routines = $this->buildRoutines();

        $customers = $this->createCustomers();
        $orders = $orderItems = $carts = $cartItems = $views = $wishlists = $reviews = [];
        $orderId = $cartId = 0;

        foreach ($customers as $customer) {
            $profile = $this->profileWeights($customer);
            $orderCount = $customer['email'] === 'demo@noor-store.test' ? 4 : min(15, 1 + $this->poisson(4.6));
            $dates = $this->orderDates($customer['created_at'], $orderCount);
            $purchased = [];

            foreach ($dates as $date) {
                $basket = $this->basket($customer, $profile, $routines);
                $orderId++;
                $state = $date->gt($this->now->copy()->subDays(5)) ? 0 : (mt_rand(1, 100) <= 95 ? 1 : 2);
                $subtotal = 0;
                $quantity = 0;
                $cartId++;

                foreach ($basket as $index) {
                    $p = $this->products[$index];
                    $qty = $this->pick([1 => 80, 2 => 17, 3 => 3]);
                    $subtotal += $p['price'] * $qty;
                    $quantity += $qty;
                    $orderItems[] = [
                        'order_id' => $orderId, 'product_id' => $p['id'], 'shop_product_id' => $p['sp'],
                        'quantity' => $qty, 'unit_price' => $p['price'], 'created_at' => $date, 'updated_at' => $date,
                    ];
                    $cartItems[] = [
                        'cart_id' => $cartId, 'shop_product_id' => $p['sp'], 'quantity' => $qty,
                        'price' => $p['price'], 'created_at' => $date, 'updated_at' => $date,
                    ];
                    $purchased[$index] = $date;
                    foreach (range(1, mt_rand(1, 2)) as $_) {
                        $views[] = $this->view($customer['id'], $p['id'], $date->copy()->subMinutes(mt_rand(5, 2880)));
                    }
                }

                $shipping = $subtotal >= 50 ? 0 : 3;
                $orders[] = [
                    'id' => $orderId, 'user_id' => $customer['id'],
                    'first_name' => $customer['first_name'], 'last_name' => $customer['last_name'],
                    'email' => $customer['email'], 'mobile' => $customer['phone'], 'address' => $customer['address'],
                    'city' => $customer['city'], 'country' => 'Syria',
                    'total_quantity' => $quantity, 'subtotal' => round($subtotal, 2), 'shipping' => $shipping,
                    'total_price' => round($subtotal + $shipping, 2), 'state' => $state,
                    'created_at' => $date, 'updated_at' => $date,
                ];
                $carts[] = [
                    'id' => $cartId, 'user_id' => $customer['id'], 'order_id' => $orderId,
                    'status' => $state === 2 ? 'abandoned' : 'completed', 'total' => round($subtotal, 2),
                    'created_at' => $date, 'updated_at' => $date,
                ];
            }

            // Browsing sessions that did not end in a purchase.
            foreach (range(1, mt_rand(10, 35)) as $_) {
                $index = $this->pickCumulative($profile);
                $views[] = $this->view($customer['id'], $this->products[$index]['id'], $this->randomDate($customer['created_at']));
            }

            if (mt_rand(1, 100) <= 35) {
                $wish = [];
                foreach (range(1, mt_rand(1, 5)) as $_) {
                    $wish[$this->pickCumulative($profile)] = true;
                }
                foreach (array_keys($wish) as $index) {
                    $date = $this->randomDate($customer['created_at']);
                    $wishlists[] = ['user_id' => $customer['id'], 'product_id' => $this->products[$index]['sp'], 'created_at' => $date, 'updated_at' => $date];
                }
            }

            foreach ($purchased as $index => $date) {
                if (mt_rand(1, 100) <= 32) {
                    $reviews[] = $this->review($customer['id'], $this->products[$index], $date->copy()->addDays(mt_rand(3, 20)));
                }
            }
        }

        foreach (range(1, self::ABANDONED_CARTS) as $_) {
            $customer = $customers[array_rand($customers)];
            $profile = $this->profileWeights($customer);
            $cartId++;
            $date = $this->randomDate($customer['created_at']);
            $total = 0;
            $items = [];
            foreach (range(1, mt_rand(1, 3)) as $__) {
                $items[$this->pickCumulative($profile)] = true;
            }
            foreach (array_keys($items) as $index) {
                $p = $this->products[$index];
                $total += $p['price'];
                $cartItems[] = ['cart_id' => $cartId, 'shop_product_id' => $p['sp'], 'quantity' => 1, 'price' => $p['price'], 'created_at' => $date, 'updated_at' => $date];
            }
            $carts[] = ['id' => $cartId, 'user_id' => $customer['id'], 'order_id' => null, 'status' => 'abandoned', 'total' => round($total, 2), 'created_at' => $date, 'updated_at' => $date];
        }

        DB::transaction(function () use ($orders, $orderItems, $carts, $cartItems, $views, $wishlists, $reviews) {
            $this->bulk('orders', $orders);
            $this->bulk('order_items', $orderItems);
            $this->bulk('carts', $carts);
            $this->bulk('cart_items', $cartItems);
            $this->bulk('product_views', $views);
            $this->bulk('wishlists', $wishlists);
            $this->bulk('reviews', $reviews);
            foreach (['orders', 'carts'] as $table) {
                DB::statement("SELECT setval(pg_get_serial_sequence('{$table}', 'id'), (SELECT MAX(id) FROM {$table}))");
            }
        });

        ProductStats::refresh();

        $this->command?->info(sprintf(
            'Simulated %d customers, %d orders (%d items), %d carts, %d views, %d reviews, %d wishlist entries.',
            count($customers), count($orders), count($orderItems), count($carts), count($views), count($reviews), count($wishlists)
        ));
    }

    private function loadProducts(): void
    {
        $rows = DB::table('products')
            ->join('shop_products', 'shop_products.product_id', '=', 'products.id')
            ->select('products.id', 'products.source_id', 'products.tags', 'shop_products.id as sp',
                'shop_products.price', 'shop_products.product_type', 'shop_products.brand_id')
            ->orderBy('products.id')
            ->get();

        $sourceRatings = collect(json_decode(file_get_contents(database_path('data/products.json')), true))
            ->pluck('source_rating', 'source_id');

        $pricesByType = [];
        foreach ($rows as $row) {
            $pricesByType[$row->product_type][] = (float) $row->price;
        }
        $quartiles = [];
        foreach ($pricesByType as $type => $prices) {
            sort($prices);
            $n = count($prices);
            $quartiles[$type] = [$prices[(int) ($n * 0.25)], $prices[(int) ($n * 0.5)], $prices[(int) ($n * 0.75)]];
        }

        foreach ($rows as $i => $row) {
            [$q1, $q2, $q3] = $quartiles[$row->product_type];
            $price = (float) $row->price;
            $bucket = $price <= $q1 ? 'low' : ($price <= $q2 ? 'mid' : ($price <= $q3 ? 'high' : 'premium'));
            $quality = $sourceRatings[$row->source_id] ?? $this->gauss(3.95, 0.45);
            $quality = max(2.6, min(4.95, $quality + ($bucket === 'premium' ? 0.12 : 0)));
            $tags = json_decode($row->tags ?? '[]', true) ?: [];

            $this->products[$i] = [
                'id' => $row->id, 'sp' => $row->sp, 'price' => $price, 'type' => $row->product_type,
                'brand' => $row->brand_id, 'bucket' => $bucket, 'quality' => $quality,
                'natural' => count(array_intersect($tags, self::NATURAL_TAGS)) > 0,
                'pop' => exp(1.3 * ($quality - 4.0)) * exp($this->gauss(0, 0.85)),
            ];
            $this->byType[$row->product_type][] = $i;
        }
    }

    /** Makeup routines: an anchor product plus complementary products, mostly from the same brand. */
    private function buildRoutines(): array
    {
        $pool = [];
        foreach ($this->products as $i => $p) {
            $pool[$i] = $p['pop'];
        }
        $anchors = [];
        while (count($anchors) < self::ROUTINES && $pool) {
            $index = $this->pickWeighted($pool);
            $anchors[] = $index;
            unset($pool[$index]);
        }

        $routines = [];
        foreach ($anchors as $rank => $anchor) {
            $a = $this->products[$anchor];
            $items = [$anchor];
            $size = mt_rand(1, 100) <= 62 ? 2 : 3;
            $complements = self::COMPLEMENTS[$a['type']];
            while (count($items) < $size && $complements) {
                $type = $this->pickWeighted($complements);
                if ($type !== $a['type']) {
                    unset($complements[$type]);
                }
                $candidates = [];
                foreach ($this->byType[$type] as $c) {
                    if (in_array($c, $items, true)) {
                        continue;
                    }
                    $p = $this->products[$c];
                    $candidates[$c] = $p['pop'] * ($p['brand'] === $a['brand'] ? 8 : 1) * ($p['bucket'] === $a['bucket'] ? 2 : 1);
                }
                if ($candidates) {
                    $items[] = $this->pickWeighted($candidates);
                }
            }
            if (count($items) > 1) {
                $routines[] = ['items' => $items, 'weight' => 1 / pow($rank + 1, 0.6), 'anchor' => $a];
            }
        }

        return $routines;
    }

    private function createCustomers(): array
    {
        $fakers = [Faker::create('en_US'), Faker::create('ar_SA')];
        $fakers[0]->seed(11);
        $fakers[1]->seed(12);
        $password = Hash::make('password');
        $rows = [];
        $customers = [];

        for ($i = 1; $i <= self::CUSTOMERS; $i++) {
            $faker = $fakers[$i % 2];
            $segment = $i === 1 ? 'eye_artist' : $this->pick(array_map(fn ($s) => $s[0], self::SEGMENTS));
            $tier = $i === 1 ? 'mid' : $this->pick(array_map(fn ($t) => $t[0], self::TIERS));
            $first = $i === 1 ? 'Lina' : $faker->firstNameFemale();
            $last = $i === 1 ? 'Haddad' : $faker->lastName();
            $email = $i === 1 ? 'demo@noor-store.test' : sprintf('customer%03d@noor-store.test', $i);
            $created = $this->now->copy()->subDays(mt_rand(30, 420));
            $rows[] = [
                'id' => $i + 1, 'name' => "$first $last", 'first_name' => $first, 'last_name' => $last,
                'email' => $email, 'password' => $password, 'role' => 0,
                'city' => self::CITIES[array_rand(self::CITIES)], 'phone' => '+963 9' . mt_rand(30, 99) . ' ' . mt_rand(100, 999) . ' ' . mt_rand(100, 999),
                'segment' => "$segment:$tier", 'email_verified_at' => $created, 'created_at' => $created, 'updated_at' => $created,
            ];

            $brands = [];
            $typeWeights = self::SEGMENTS[$segment][1];
            foreach (range(1, mt_rand(1, 2)) as $_) {
                $type = $this->pickWeighted($typeWeights);
                $brands[] = $this->products[$this->byType[$type][array_rand($this->byType[$type])]]['brand'];
            }
            $customers[] = [
                'id' => $i + 1, 'email' => $email, 'first_name' => $first, 'last_name' => $last,
                'phone' => end($rows)['phone'], 'city' => end($rows)['city'], 'address' => $fakers[0]->streetAddress(),
                'segment' => $segment, 'tier' => $tier, 'brands' => array_unique($brands), 'created_at' => $created,
            ];
        }

        DB::table('users')->where('id', '>', 1)->delete();
        DB::statement("SELECT setval(pg_get_serial_sequence('users', 'id'), 1)");
        $this->bulk('users', $rows);
        DB::statement("SELECT setval(pg_get_serial_sequence('users', 'id'), (SELECT MAX(id) FROM users))");

        return $customers;
    }

    /** Cumulative distribution over all products for one customer profile. */
    private function profileWeights(array $customer): array
    {
        $types = self::SEGMENTS[$customer['segment']][1];
        $tierWeights = self::TIERS[$customer['tier']][1];
        $cumulative = [];
        $sum = 0;
        foreach ($this->products as $p) {
            $w = ($types[$p['type']] ?? 0.08) * $p['pop'] * $tierWeights[$p['bucket']];
            if (in_array($p['brand'], $customer['brands'], true)) {
                $w *= 3;
            }
            if ($customer['segment'] === 'natural_minimalist' && $p['natural']) {
                $w *= 4;
            }
            $sum += $w;
            $cumulative[] = $sum;
        }

        return $cumulative;
    }

    private function basket(array $customer, array $profile, array $routines): array
    {
        $items = [];
        if (mt_rand(1, 100) <= 60) {
            $types = self::SEGMENTS[$customer['segment']][1];
            $tierWeights = self::TIERS[$customer['tier']][1];
            $weights = [];
            foreach ($routines as $r => $routine) {
                $a = $routine['anchor'];
                $weights[$r] = $routine['weight'] * ($types[$a['type']] ?? 0.05) * $tierWeights[$a['bucket']]
                    * (in_array($a['brand'], $customer['brands'], true) ? 2.5 : 1);
            }
            $routine = $routines[$this->pickWeighted($weights)];
            foreach ($routine['items'] as $position => $index) {
                if ($position === 0 || mt_rand(1, 100) <= 88) {
                    $items[$index] = true;
                }
            }
            if (mt_rand(1, 100) <= 30) {
                $items[$this->pickCumulative($profile)] = true;
            }
        } else {
            $count = $this->pick([1 => 45, 2 => 35, 3 => 20]);
            while (count($items) < $count) {
                $items[$this->pickCumulative($profile)] = true;
            }
        }

        return array_keys($items);
    }

    private function review(int $userId, array $product, Carbon $date): array
    {
        $star = (int) max(1, min(5, round($this->gauss($product['quality'], 0.75))));
        $label = str_replace('_', ' ', $product['type']);
        $templates = self::REVIEWS[$star];

        return [
            'product_id' => $product['id'], 'user_id' => $userId, 'star' => $star,
            'review' => str_replace('{type}', $label, $templates[array_rand($templates)]),
            'created_at' => min($date, $this->now), 'updated_at' => min($date, $this->now),
        ];
    }

    private function view(int $userId, int $productId, Carbon $date): array
    {
        return ['user_id' => $userId, 'session_id' => 'sim-' . $userId, 'product_id' => $productId, 'created_at' => $date];
    }

    private function orderDates(Carbon $since, int $count): array
    {
        $dates = [];
        for ($i = 0; $i < $count; $i++) {
            $dates[] = $this->randomDate($since);
        }
        usort($dates, fn ($a, $b) => $a <=> $b);

        return $dates;
    }

    /** Random moment between $since and now, skewed towards recent activity. */
    private function randomDate(Carbon $since): Carbon
    {
        $span = max(1, $since->diffInMinutes($this->now));
        $offset = (int) ($span * pow(mt_rand() / mt_getrandmax(), 0.75));

        return $since->copy()->addMinutes($offset);
    }

    private function bulk(string $table, array $rows): void
    {
        foreach (array_chunk($rows, 1000) as $chunk) {
            DB::table($table)->insert($chunk);
        }
    }

    private function pick(array $weights)
    {
        return $this->pickWeighted($weights);
    }

    private function pickWeighted(array $weights)
    {
        $r = mt_rand() / mt_getrandmax() * array_sum($weights);
        foreach ($weights as $key => $w) {
            $r -= $w;
            if ($r <= 0) {
                return $key;
            }
        }

        return array_key_last($weights);
    }

    private function pickCumulative(array $cumulative): int
    {
        $target = mt_rand() / mt_getrandmax() * end($cumulative);
        $lo = 0;
        $hi = count($cumulative) - 1;
        while ($lo < $hi) {
            $mid = intdiv($lo + $hi, 2);
            if ($cumulative[$mid] < $target) {
                $lo = $mid + 1;
            } else {
                $hi = $mid;
            }
        }

        return $lo;
    }

    private function gauss(float $mean, float $sd): float
    {
        $u = max(1e-9, mt_rand() / mt_getrandmax());
        $v = mt_rand() / mt_getrandmax();

        return $mean + $sd * sqrt(-2 * log($u)) * cos(2 * M_PI * $v);
    }

    private function poisson(float $lambda): int
    {
        $l = exp(-$lambda);
        $k = 0;
        $p = 1.0;
        do {
            $k++;
            $p *= mt_rand() / mt_getrandmax();
        } while ($p > $l);

        return $k - 1;
    }
}
