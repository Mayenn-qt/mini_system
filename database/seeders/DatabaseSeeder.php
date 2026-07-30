<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Setting;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Branches
        $main = Branch::create(['name' => 'Main Branch']);
        $bacon = Branch::create(['name' => 'Bacon Branch']);
        $gubat = Branch::create(['name' => 'Gubat Branch']);

        // 2. Seed Users
        // Owner
        $owner = User::create([
            'name' => 'Kenji Sato (Owner)',
            'email' => 'owner@ohaiyo.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'branch_id' => null,
            'status' => 'active',
        ]);

        // Staff for Main Branch
        $staffMain = User::create([
            'name' => 'Aiko Tanaka',
            'email' => 'staff.main@ohaiyo.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'branch_id' => $main->id,
            'status' => 'active',
        ]);

        // Staff for Bacon Branch
        $staffBacon = User::create([
            'name' => 'Yuto Watanabe',
            'email' => 'staff.bacon@ohaiyo.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'branch_id' => $bacon->id,
            'status' => 'active',
        ]);

        // Staff for Gubat Branch
        $staffGubat = User::create([
            'name' => 'Sakura Ito',
            'email' => 'staff.gubat@ohaiyo.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'branch_id' => $gubat->id,
            'status' => 'active',
        ]);

        // 3. Seed Categories
        $categories = [
            'Kitchenware & Dining',
            'Ceramics & Porcelain',
            'Japanese Tools & Hardware',
            'Traditional Toys & Hobbies',
            'Vintage Electronics',
            'Collectibles & Antiques'
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[] = Category::create(['name' => $cat]);
        }

        // 4. Seed Products
        $productsData = [
            [
                'name' => 'Minoyaki Ceramic Ramen Bowl',
                'sku' => 'MINO-RAM-01',
                'category_index' => 1,
                'buying_price' => 150.00,
                'selling_price' => 350.00,
            ],
            [
                'name' => 'Handcrafted Damascus Santoku Knife',
                'sku' => 'DKN-SAN-99',
                'category_index' => 0,
                'buying_price' => 1200.00,
                'selling_price' => 2800.00,
            ],
            [
                'name' => 'Cast Iron Tetsubin Teapot 800ml',
                'sku' => 'TET-POT-50',
                'category_index' => 0,
                'buying_price' => 800.00,
                'selling_price' => 1800.00,
            ],
            [
                'name' => 'Vintage Ryobi Electric Planer',
                'sku' => 'RYO-PLN-08',
                'category_index' => 2,
                'buying_price' => 1500.00,
                'selling_price' => 3200.00,
            ],
            [
                'name' => 'Retro Bandai Gundam Model Kit 1995',
                'sku' => 'BAN-GUN-95',
                'category_index' => 3,
                'buying_price' => 350.00,
                'selling_price' => 950.00,
            ],
            [
                'name' => 'Sony Walkman WM-EX900 (Restored)',
                'sku' => 'SON-WM-900',
                'category_index' => 4,
                'buying_price' => 2000.00,
                'selling_price' => 4500.00,
            ],
            [
                'name' => 'Showa Era Bronze Lucky Cat (Maneki-Neko)',
                'sku' => 'SHO-MNK-33',
                'category_index' => 5,
                'buying_price' => 1800.00,
                'selling_price' => 3900.00,
            ],
            [
                'name' => 'Urasenke Bamboo Matcha Whisk (Chasen)',
                'sku' => 'URA-CHA-02',
                'category_index' => 0,
                'buying_price' => 120.00,
                'selling_price' => 300.00,
            ],
            [
                'name' => 'Lacquerware Bento Box 3-Tier',
                'sku' => 'LAC-BEN-12',
                'category_index' => 0,
                'buying_price' => 250.00,
                'selling_price' => 600.00,
            ],
            [
                'name' => 'Arita Ware Sake Set (1 Decanter, 4 Cups)',
                'sku' => 'ARI-SAK-05',
                'category_index' => 1,
                'buying_price' => 400.00,
                'selling_price' => 950.00,
            ]
        ];

        $productModels = [];
        foreach ($productsData as $pData) {
            $catId = $categoryModels[$pData['category_index']]->id;
            $productModels[] = Product::create([
                'category_id' => $catId,
                'name' => $pData['name'],
                'sku' => $pData['sku'],
                'buying_price' => $pData['buying_price'],
                'selling_price' => $pData['selling_price'],
                'image_path' => null,
            ]);
        }

        // 5. Seed Inventories (Create Inventory records for all branches & products)
        $branches = [$main, $bacon, $gubat];
        foreach ($branches as $branch) {
            foreach ($productModels as $idx => $prod) {
                // Determine quantities (mix of stock levels)
                // Let's make some items low stock (< 5) to test alerts
                $quantity = 15;
                if ($idx == 0 && $branch->id == $bacon->id) $quantity = 2; // Low stock Minoyaki in Bacon
                if ($idx == 1 && $branch->id == $gubat->id) $quantity = 3; // Low stock Santoku in Gubat
                if ($idx == 4) $quantity = 4; // Low stock Gundam everywhere

                Inventory::create([
                    'branch_id' => $branch->id,
                    'product_id' => $prod->id,
                    'quantity' => $quantity,
                ]);

                // Initial stock movement record
                StockMovement::create([
                    'branch_id' => $branch->id,
                    'product_id' => $prod->id,
                    'user_id' => $owner->id,
                    'type' => 'in',
                    'quantity' => $quantity,
                    'notes' => 'Initial system seeding',
                ]);
            }
        }

        // 6. Seed Settings
        Setting::set('store_name', 'Ohaiyo Japan Surplus');
        Setting::set('store_address', 'Rizal Street, Sorsogon City, Sorsogon');
        Setting::set('store_phone', '+63 912 345 6789');
        Setting::set('store_email', 'info@ohaiyo-japan.com');
        Setting::set('sms_gateway_api_key', 'OHAIYO_MOCK_API_KEY_2026');
        Setting::set('sms_sender_id', 'OHAIYOJP');

        // 7. Seed Sales History (Simulate some sales for charts and reports)
        // Let's create some sales for yesterday and today
        $now = now();
        $yesterday = now()->subDay();

        // Yesterday Sale in Bacon Branch
        $sale1 = Sale::create([
            'reference_number' => 'SL-' . strtoupper(Str::random(8)),
            'branch_id' => $bacon->id,
            'user_id' => $staffBacon->id,
            'total_amount' => 1300.00,
            'amount_paid' => 1500.00,
            'change_amount' => 200.00,
            'created_at' => $yesterday,
            'updated_at' => $yesterday,
        ]);
        SaleItem::create([
            'sale_id' => $sale1->id,
            'product_id' => $productModels[0]->id, // Ramen bowl (350.00)
            'quantity' => 1,
            'buying_price' => $productModels[0]->buying_price,
            'selling_price' => $productModels[0]->selling_price,
            'subtotal' => 350.00,
        ]);
        SaleItem::create([
            'sale_id' => $sale1->id,
            'product_id' => $productModels[4]->id, // Gundam (950.00)
            'quantity' => 1,
            'buying_price' => $productModels[4]->buying_price,
            'selling_price' => $productModels[4]->selling_price,
            'subtotal' => 950.00,
        ]);

        // Today Sale in Main Branch
        $sale2 = Sale::create([
            'reference_number' => 'SL-' . strtoupper(Str::random(8)),
            'branch_id' => $main->id,
            'user_id' => $staffMain->id,
            'total_amount' => 3100.00,
            'amount_paid' => 3500.00,
            'change_amount' => 400.00,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        SaleItem::create([
            'sale_id' => $sale2->id,
            'product_id' => $productModels[7]->id, // Matcha Whisk (300.00)
            'quantity' => 1,
            'buying_price' => $productModels[7]->buying_price,
            'selling_price' => $productModels[7]->selling_price,
            'subtotal' => 300.00,
        ]);
        SaleItem::create([
            'sale_id' => $sale2->id,
            'product_id' => $productModels[1]->id, // Santoku Knife (2800.00)
            'quantity' => 1,
            'buying_price' => $productModels[1]->buying_price,
            'selling_price' => $productModels[1]->selling_price,
            'subtotal' => 2800.00,
        ]);

        // 8. Seed Customers
        \App\Models\Customer::create([
            'name' => 'Ichiro Suzuki',
            'phone' => '+639171112222',
            'subscribed' => true,
        ]);
        \App\Models\Customer::create([
            'name' => 'Naomi Osaka',
            'phone' => '+639183334444',
            'subscribed' => true,
        ]);
        \App\Models\Customer::create([
            'name' => 'Taro Yamada',
            'phone' => '+639195556666',
            'subscribed' => false,
        ]);
    }
}
