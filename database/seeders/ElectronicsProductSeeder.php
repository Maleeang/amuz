<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;

class ElectronicsProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronicsCategory = Category::firstOrCreate([
            'name' => '전자제품',
            'description' => '다양한 전자기기와 가전제품'
        ]);

        $smartphoneCategory = Category::firstOrCreate([
            'name' => '스마트폰',
            'description' => '최신 스마트폰과 액세서리'
        ]);

        $laptopCategory = Category::firstOrCreate([
            'name' => '노트북',
            'description' => '휴대용 컴퓨터와 액세서리'
        ]);

        $audioCategory = Category::firstOrCreate([
            'name' => '오디오',
            'description' => '헤드폰, 스피커, 오디오 장비'
        ]);

        $cameraCategory = Category::firstOrCreate([
            'name' => '카메라',
            'description' => '디지털 카메라와 드론'
        ]);

        $gamingCategory = Category::firstOrCreate([
            'name' => '게이밍',
            'description' => '게임 콘솔과 액세서리'
        ]);

        $tabletCategory = Category::firstOrCreate([
            'name' => '태블릿',
            'description' => '태블릿 PC와 액세서리'
        ]);

        $watchCategory = Category::firstOrCreate([
            'name' => '스마트워치',
            'description' => '스마트워치와 웨어러블 기기'
        ]);

        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'Apple의 최신 플래그십 스마트폰. A17 Pro 칩과 티타늄 디자인',
                'price' => 1500000,
                'stock_quantity' => 50,
                'categories' => [$smartphoneCategory->id]
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'description' => '삼성의 최고급 스마트폰. S Pen과 AI 기능 탑재',
                'price' => 1800000,
                'stock_quantity' => 30,
                'categories' => [$smartphoneCategory->id]
            ],
            [
                'name' => 'Google Pixel 8 Pro',
                'description' => 'Google의 AI 중심 스마트폰. 최고의 카메라 성능',
                'price' => 1200000,
                'stock_quantity' => 25,
                'categories' => [$smartphoneCategory->id]
            ],

            [
                'name' => 'MacBook Pro 14" M3',
                'description' => 'Apple Silicon M3 칩 탑재. 전문가용 노트북',
                'price' => 2800000,
                'stock_quantity' => 20,
                'categories' => [$laptopCategory->id]
            ],
            [
                'name' => 'Dell XPS 13 Plus',
                'description' => '울트라북의 대표주자. 인피니티 엣지 디스플레이',
                'price' => 2200000,
                'stock_quantity' => 15,
                'categories' => [$laptopCategory->id]
            ],
            [
                'name' => 'ASUS ROG Zephyrus G14',
                'description' => '게이밍 노트북. AMD Ryzen 9와 RTX 4060',
                'price' => 2400000,
                'stock_quantity' => 10,
                'categories' => [$laptopCategory->id, $gamingCategory->id]
            ],

            [
                'name' => 'Sony WH-1000XM5',
                'description' => '노이즈 캔슬링 헤드폰의 최고봉',
                'price' => 450000,
                'stock_quantity' => 40,
                'categories' => [$audioCategory->id]
            ],
            [
                'name' => 'AirPods Pro 2',
                'description' => 'Apple의 프리미엄 무선 이어폰',
                'price' => 350000,
                'stock_quantity' => 60,
                'categories' => [$audioCategory->id]
            ],
            [
                'name' => 'Bose QuietComfort 45',
                'description' => 'Bose의 클래식 노이즈 캔슬링 헤드폰',
                'price' => 380000,
                'stock_quantity' => 25,
                'categories' => [$audioCategory->id]
            ],

            [
                'name' => 'Canon EOS R6 Mark II',
                'description' => '풀프레임 미러리스 카메라. 4K 60fps 동영상',
                'price' => 3200000,
                'stock_quantity' => 8,
                'categories' => [$cameraCategory->id]
            ],
            [
                'name' => 'Sony A7 IV',
                'description' => '33MP 풀프레임 센서. 전문가용 카메라',
                'price' => 3500000,
                'stock_quantity' => 12,
                'categories' => [$cameraCategory->id]
            ],
            [
                'name' => 'DJI Mini 3 Pro',
                'description' => '249g 초경량 드론. 4K 동영상 촬영',
                'price' => 1200000,
                'stock_quantity' => 15,
                'categories' => [$cameraCategory->id]
            ],

            [
                'name' => 'PlayStation 5',
                'description' => 'Sony의 최신 게임 콘솔. 4K 게이밍',
                'price' => 600000,
                'stock_quantity' => 30,
                'categories' => [$gamingCategory->id]
            ],
            [
                'name' => 'Nintendo Switch OLED',
                'description' => 'Nintendo의 휴대용 게임기. 7인치 OLED 화면',
                'price' => 400000,
                'stock_quantity' => 45,
                'categories' => [$gamingCategory->id]
            ],
            [
                'name' => 'Xbox Series X',
                'description' => 'Microsoft의 최고급 게임 콘솔',
                'price' => 650000,
                'stock_quantity' => 20,
                'categories' => [$gamingCategory->id]
            ],

            [
                'name' => 'iPad Air 5',
                'description' => 'Apple의 중급 태블릿. M1 칩 탑재',
                'price' => 900000,
                'stock_quantity' => 35,
                'categories' => [$tabletCategory->id]
            ],
            [
                'name' => 'Samsung Galaxy Tab S9',
                'description' => 'Android 태블릿의 최고봉. S Pen 포함',
                'price' => 1100000,
                'stock_quantity' => 25,
                'categories' => [$tabletCategory->id]
            ],

            [
                'name' => 'Apple Watch Series 9',
                'description' => 'Apple의 최신 스마트워치. 건강 모니터링',
                'price' => 550000,
                'stock_quantity' => 40,
                'categories' => [$watchCategory->id]
            ],
            [
                'name' => 'Samsung Galaxy Watch 6',
                'description' => 'Android 호환 스마트워치. 원형 디스플레이',
                'price' => 450000,
                'stock_quantity' => 30,
                'categories' => [$watchCategory->id]
            ]
        ];

        foreach ($products as $productData) {
            $categories = $productData['categories'];
            unset($productData['categories']);
            
            $product = Product::create($productData);
            $product->categories()->attach($categories);
        }

        $user = User::firstOrCreate([
            'email' => 'user@amuz.com'
        ], [
            'name' => '테스트 사용자',
            'password' => bcrypt('password')
        ]);

        $this->createOrders($user, Product::all());
    }

    private function createOrders(User $user, $products)
    {
        $orderStatuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];
        $orderNotes = [
            '빠른 배송 부탁드립니다.',
            '선물용으로 포장해주세요.',
            '오후에만 배송 가능합니다.',
            '문 앞에 놓고 가주세요.',
            '회사 주소로 배송해주세요.',
            '생일 선물입니다.',
            '조심히 다뤄주세요.',
            '부재시 경비실에 맡겨주세요.',
            '전화 연락 후 배송해주세요.',
            '특별한 포장 부탁드립니다.'
        ];

        for ($i = 1; $i <= 20; $i++) {
            $order = Order::create([
                'user_id' => $user->id,
                'status' => $orderStatuses[array_rand($orderStatuses)],
                'total_amount' => 0,
                'notes' => $orderNotes[array_rand($orderNotes)],
                'ordered_at' => now()->subDays(rand(1, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59))
            ]);

            $orderItems = [];
            $totalAmount = 0;
            $itemCount = rand(1, 3);
            
            $selectedProducts = $products->random($itemCount);
            
            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $totalPrice = $product->price * $quantity;
                $totalAmount += $totalPrice;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'total_price' => $totalPrice
                ]);
            }

            $order->update(['total_amount' => $totalAmount]);
        }

        echo "✅ 20개의 주문 데이터가 생성되었습니다!\n";
    }
}
