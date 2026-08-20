<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Shop_product;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    public function run()
    {
        $users = User::limit(10)->get();
        $shopProducts = Shop_product::limit(20)->get();

        if ($shopProducts->isEmpty()) {
            echo "No shop products found. Please create products first.\n";
            return;
        }

        foreach ($users as $user) {
            for ($i = 0; $i < 5; $i++) {
                $cart = Cart::create([
                    'user_id' => $user->id,
                    'status' => 'completed',
                    'total' => 0,
                ]);

                $total = 0;
                $randomProducts = $shopProducts->random(rand(2, 5));

                foreach ($randomProducts as $product) {
                    $quantity = rand(1, 3);
                    CartItem::create([
                        'cart_id' => $cart->id,
                        'shop_product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $product->price,
                    ]);
                    $total += $product->price * $quantity;
                }

                $cart->update(['total' => $total]);
            }
        }

        echo "CartSeeder completed successfully!\n";
    }
}
