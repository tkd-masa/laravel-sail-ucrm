<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Purchase;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ItemSeeder::class,
            UserSeeder::class,
        ]);

        Customer::factory(1000)->create();
        // Purchase::factory(100)->create();

        $items = Item::all();
        Purchase::factory(30000)->create()->each(function (Purchase $purchase) use ($items) {
            // attachをつけると中間テーブルに情報を追加できる
            $purchase->items()->attach(
                $items->random(rand(1, 3))->pluck('id')->toArray(),
                ['quantity' => rand(1, 5)]
            );
        });
    }
}
