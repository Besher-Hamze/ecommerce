<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Imports the cleaned Makeup API catalogue (database/data/products.json,
 * produced by database/data/prepare_dataset.py) into categories, brands,
 * products and shop_products.
 */
class CatalogSeeder extends Seeder
{
    /** parent => [ar, en, icon, children[type => [ar, en]]] */
    public const TAXONOMY = [
        'lips' => ['الشفاه', 'Lips', 'fa-kiss-wink-heart', [
            'lipstick' => ['أحمر الشفاه', 'Lipstick'],
            'lip_liner' => ['محدد الشفاه', 'Lip Liner'],
        ]],
        'eyes' => ['العيون', 'Eyes', 'fa-eye', [
            'mascara' => ['الماسكارا', 'Mascara'],
            'eyeliner' => ['محدد العيون', 'Eyeliner'],
            'eyeshadow' => ['ظلال العيون', 'Eyeshadow'],
            'eyebrow' => ['الحواجب', 'Eyebrow'],
        ]],
        'face' => ['الوجه', 'Face', 'fa-smile', [
            'foundation' => ['كريم الأساس', 'Foundation'],
            'blush' => ['أحمر الخدود', 'Blush'],
            'bronzer' => ['البرونزر', 'Bronzer'],
        ]],
        'nails' => ['الأظافر', 'Nails', 'fa-hand-sparkles', [
            'nail_polish' => ['طلاء الأظافر', 'Nail Polish'],
        ]],
    ];

    public function run()
    {
        $path = database_path('data/products.json');
        if (!is_file($path)) {
            throw new RuntimeException('Missing database/data/products.json - run: python database/data/prepare_dataset.py');
        }
        $items = json_decode(file_get_contents($path), true);
        $now = Carbon::now();
        mt_srand(2026);

        DB::transaction(function () use ($items, $now) {
            $typeCategoryIds = $this->seedCategories($now);
            $brandIds = $this->seedBrands($items, $now);
            $shopId = DB::table('shops')->insertGetId([
                'name' => 'نور ستور', 'eng_name' => 'Noor Store',
                'description' => 'متجر مستحضرات التجميل', 'eng_description' => 'Cosmetics & makeup store',
                'city' => 'Damascus', 'phone' => '+963 11 000 0000', 'status' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ]);

            $productRows = [];
            foreach ($items as $i => $item) {
                $created = $now->copy()->subDays(mt_rand(0, 540))->subMinutes(mt_rand(0, 1440));
                $productRows[] = [
                    'source_id' => $item['source_id'],
                    'name' => $item['name'],
                    'eng_name' => $item['name'],
                    'description' => $item['description'],
                    'eng_description' => $item['description'],
                    'meta_title' => $item['brand'] . ' ' . $item['name'],
                    'eng_meta_title' => $item['brand'] . ' ' . $item['name'],
                    'product_code' => 'NS-' . $item['source_id'],
                    'image_url' => $item['image'],
                    'form' => $item['form'],
                    'tags' => json_encode($item['tags']),
                    'created_at' => $created,
                    'updated_at' => $created,
                ];
            }
            foreach (array_chunk($productRows, 300) as $chunk) {
                DB::table('products')->insert($chunk);
            }

            $productIds = DB::table('products')->pluck('id', 'source_id');
            $shopRows = [];
            foreach ($items as $item) {
                $productId = $productIds[$item['source_id']];
                $shopRows[] = [
                    'product_id' => $productId,
                    'shop_id' => $shopId,
                    'category_id' => $typeCategoryIds[$item['product_type']] ?? null,
                    'brand_id' => $brandIds[$item['brand']],
                    'price' => $item['price'],
                    'count' => mt_rand(8, 140),
                    'shipping' => 1,
                    'status' => 1,
                    'product_type' => $item['product_type'],
                    'product_colors' => json_encode(array_map(
                        fn ($c) => ['hex_value' => $c['hex'], 'colour_name' => $c['name']],
                        $item['colors']
                    )),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            foreach (array_chunk($shopRows, 300) as $chunk) {
                DB::table('shop_products')->insert($chunk);
            }

            DB::statement('UPDATE categories c SET count_prod = s.n FROM (
                SELECT category_id, COUNT(*) n FROM shop_products GROUP BY category_id) s WHERE s.category_id = c.id');
            DB::statement('UPDATE categories p SET count_prod = s.n FROM (
                SELECT parent_id, SUM(count_prod) n FROM categories WHERE parent_id IS NOT NULL GROUP BY parent_id) s WHERE s.parent_id = p.id');
        });

        $this->command?->info(sprintf('Catalogue imported: %d products, %d brands.', count($items), DB::table('brands')->count()));
    }

    /** @return array<string,int> product_type => category id */
    private function seedCategories(Carbon $now): array
    {
        $map = [];
        $order = 0;
        foreach (self::TAXONOMY as $slug => [$ar, $en, $icon, $children]) {
            $parentId = DB::table('categories')->insertGetId([
                'name' => $ar, 'eng_name' => $en, 'slug' => $slug, 'icon' => $icon,
                'status' => 1, 'sort_order' => $order++, 'created_at' => $now, 'updated_at' => $now,
            ]);
            $childOrder = 0;
            foreach ($children as $type => [$childAr, $childEn]) {
                $map[$type] = DB::table('categories')->insertGetId([
                    'parent_id' => $parentId, 'name' => $childAr, 'eng_name' => $childEn,
                    'slug' => Str::slug($type), 'status' => 1, 'sort_order' => $childOrder++,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        return $map;
    }

    /** @return array<string,int> brand name => id */
    private function seedBrands(array $items, Carbon $now): array
    {
        $names = array_values(array_unique(array_column($items, 'brand')));
        sort($names);
        $rows = array_map(fn ($name) => [
            'name' => $name, 'slug' => Str::slug($name), 'status' => 0,
            'created_at' => $now, 'updated_at' => $now,
        ], $names);
        DB::table('brands')->insert($rows);

        return DB::table('brands')->pluck('id', 'name')->all();
    }
}
