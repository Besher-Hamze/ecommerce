<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StoreSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            'name' => 'Noor-Store',
            'owner' => 'Noor Horan',
            'address' => 'Damascus, Syria',
            'email' => 'hello@noor-store.test',
            'wats_phone' => '+963 944 000 000',
            'const_phone' => '+963 11 000 0000',
            'work_time' => 'Sat - Thu, 9:00 - 21:00',
            'about' => 'Authentic makeup from the brands you love, with smart recommendations that build your routine.',
            'meta_title' => 'Noor-Store | Makeup & Cosmetics',
            'meta_description' => 'Shop lipsticks, mascaras, foundations and more with personalised recommendations.',
            'meta_keyword' => 'makeup, cosmetics, lipstick, mascara, foundation',
            'free_shipping_threshold' => '50',
            'flat_shipping' => '3',
        ];
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        DB::table('sliders')->insert([
            ['text_en' => 'New season colours', 'text_ar' => 'ألوان الموسم الجديد', 'image' => '1775230467.jpg', 'url' => null, 'created_at' => now(), 'updated_at' => now()],
            ['text_en' => 'Build your routine', 'text_ar' => 'ابني روتينك', 'image' => '1775230484.jpg', 'url' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        User::updateOrCreate(['email' => 'admin@noor-store.test'], [
            'name' => 'Store Admin', 'first_name' => 'Store', 'last_name' => 'Admin',
            'password' => Hash::make('password'), 'role' => 1, 'city' => 'Damascus', 'phone' => '+963 944 111 111',
        ]);
    }
}
