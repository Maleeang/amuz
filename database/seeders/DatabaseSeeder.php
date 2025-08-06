<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Orchid\Platform\Models\Role;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedRoles();
        $this->seedUsers();
        $this->seedCategories();
        $this->seedProducts();
        $this->seedOrders();

        $this->command->info('시드 완료');
        $this->command->info('총 사용자: ' . User::count() . '명');
        $this->command->info('총 상품: ' . Product::count() . '개');
        $this->command->info('총 주문: ' . Order::count() . '건');
    }

    private function seedRoles()
    {
        $adminRole = Role::firstOrCreate([
            'slug' => 'admin',
        ], [
            'name' => '관리자',
            'permissions' => [
                'platform.index' => 1,
                'platform.systems.index' => 1,
                'platform.systems.users' => 1,
                'platform.systems.roles' => 1,
                'platform.systems.attachment' => 1,
            ],
        ]);

        $userRole = Role::firstOrCreate([
            'slug' => 'user',
        ], [
            'name' => '일반 사용자',
            'permissions' => [],
        ]);

        $this->command->info('권한 시드 완료');
    }

    private function seedUsers()
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $userRole = Role::where('slug', 'user')->first();

        // 관리자 계정
        $admin = User::firstOrCreate([
            'email' => 'admin@amuz.com',
        ], [
            'name' => '아뮤즈 관리자',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->roles()->sync([$adminRole->id]);

        // 테스트 사용자
        $testUser = User::firstOrCreate([
            'email' => 'user@amuz.com',
        ], [
            'name' => '테스트 사용자',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $testUser->roles()->sync([$userRole->id]);

        // 추가 사용자 생성 (20명)
        User::factory(20)->create()->each(function ($user) use ($userRole) {
            $user->roles()->sync([$userRole->id]);
        });

        $this->command->info('사용자 시드 완료 (총 ' . User::count() . '명)');
    }

    private function seedCategories()
    {
        $categories = [
            ['name' => '전자제품', 'description' => '노트북, 스마트폰, 태블릿 등 최신 전자제품'],
            ['name' => '의류', 'description' => '남성/여성 의류, 신발, 액세서리'],
            ['name' => '도서', 'description' => '소설, 에세이, 전문서적, 만화책'],
            ['name' => '홈&리빙', 'description' => '가구, 인테리어 소품, 생활용품'],
            ['name' => '스포츠', 'description' => '운동기구, 스포츠웨어, 아웃도어 용품'],
            ['name' => '뷰티', 'description' => '화장품, 스킨케어, 향수'],
            ['name' => '식품', 'description' => '건강식품, 간식, 음료'],
            ['name' => '취미', 'description' => '게임, 레고, 수집품'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }

        $this->command->info('카테고리 시드 완료 (' . Category::count() . '개)');
    }

    private function seedProducts()
    {
        $products = [
            // 전자제품
            ['name' => 'MacBook Pro 16인치', 'description' => 'M3 Pro 칩 탑재, 512GB SSD, 최신 맥북', 'price' => 3200000, 'stock_quantity' => 15, 'category_id' => 1],
            ['name' => 'iPhone 15 Pro', 'description' => '256GB, 티타늄 블루, 최신 아이폰', 'price' => 1550000, 'stock_quantity' => 25, 'category_id' => 1],
            ['name' => 'Galaxy S24 Ultra', 'description' => '512GB, S펜 포함, 안드로이드 플래그십', 'price' => 1400000, 'stock_quantity' => 18, 'category_id' => 1],
            ['name' => 'iPad Air', 'description' => '11인치, Wi-Fi, 256GB, 휴대용 태블릿', 'price' => 850000, 'stock_quantity' => 20, 'category_id' => 1],
            ['name' => 'AirPods Pro', 'description' => '노이즈 캔슬링, 무선 이어폰', 'price' => 350000, 'stock_quantity' => 30, 'category_id' => 1],
            ['name' => 'Apple Watch Series 9', 'description' => '45mm, GPS, 스마트워치', 'price' => 550000, 'stock_quantity' => 12, 'category_id' => 1],
            
            // 의류
            ['name' => '유니클로 히트텍 이너웨어', 'description' => '겨울 필수 아이템, 극세사 소재', 'price' => 19900, 'stock_quantity' => 100, 'category_id' => 2],
            ['name' => '나이키 에어포스1', 'description' => '클래식 화이트 스니커즈', 'price' => 129000, 'stock_quantity' => 30, 'category_id' => 2],
            ['name' => '자라 트렌치코트', 'description' => '베이지 컬러, 봄/가을용', 'price' => 89000, 'stock_quantity' => 12, 'category_id' => 2],
            ['name' => '아디다스 트레이닝 세트', 'description' => '편안한 운동복, 상하의 세트', 'price' => 65000, 'stock_quantity' => 25, 'category_id' => 2],
            ['name' => '구찌 가죽 백팩', 'description' => '고급스러운 가죽 백팩', 'price' => 2800000, 'stock_quantity' => 5, 'category_id' => 2],
            
            // 도서
            ['name' => '해리포터 시리즈 세트', 'description' => '전 7권 세트, 한국어판', 'price' => 98000, 'stock_quantity' => 15, 'category_id' => 3],
            ['name' => '부자 아빠 가난한 아빠', 'description' => '로버트 기요사키, 재테크 필독서', 'price' => 15000, 'stock_quantity' => 50, 'category_id' => 3],
            ['name' => '코딩 테스트 완전 정복', 'description' => '알고리즘 문제 풀이 가이드', 'price' => 25000, 'stock_quantity' => 20, 'category_id' => 3],
            ['name' => 'Laravel 완전 가이드', 'description' => 'PHP 프레임워크 마스터하기', 'price' => 35000, 'stock_quantity' => 18, 'category_id' => 3],
            
            // 홈&리빙
            ['name' => 'IKEA 침대 프레임', 'description' => '퀸사이즈, 심플한 디자인', 'price' => 450000, 'stock_quantity' => 8, 'category_id' => 4],
            ['name' => '다이슨 공기청정기', 'description' => 'H13 HEPA 필터, 스마트 센서', 'price' => 650000, 'stock_quantity' => 10, 'category_id' => 4],
            ['name' => '필립스 휴브 LED 조명', 'description' => '스마트 조명, 색상 조절 가능', 'price' => 89000, 'stock_quantity' => 15, 'category_id' => 4],
            
            // 스포츠
            ['name' => '나이키 러닝화', 'description' => '쿠션감 좋은 러닝화', 'price' => 159000, 'stock_quantity' => 22, 'category_id' => 5],
            ['name' => '요가 매트', 'description' => '고급 요가 매트, 미끄럼 방지', 'price' => 45000, 'stock_quantity' => 35, 'category_id' => 5],
            ['name' => '덤벨 세트', 'description' => '5kg, 10kg, 15kg 세트', 'price' => 120000, 'stock_quantity' => 12, 'category_id' => 5],
            
            // 뷰티
            ['name' => 'SK-II 페이셜 트리트먼트', 'description' => '230ml, 피부 진정 효과', 'price' => 180000, 'stock_quantity' => 20, 'category_id' => 6],
            ['name' => '샤넬 No.5 향수', 'description' => '50ml, 클래식한 향', 'price' => 220000, 'stock_quantity' => 8, 'category_id' => 6],
            ['name' => '디올 립스틱', 'description' => '999 컬러, 매트한 텍스처', 'price' => 45000, 'stock_quantity' => 25, 'category_id' => 6],
            
            // 식품
            ['name' => '프로틴 파우더', 'description' => '체리맛, 1kg, 운동 후 보충제', 'price' => 55000, 'stock_quantity' => 30, 'category_id' => 7],
            ['name' => '비타민 C', 'description' => '1000mg, 면역력 증진', 'price' => 25000, 'stock_quantity' => 40, 'category_id' => 7],
            
            // 취미
            ['name' => 'PS5 게임기', 'description' => '디지털 에디션, 최신 게임기', 'price' => 550000, 'stock_quantity' => 10, 'category_id' => 8],
            ['name' => '레고 스타워즈 세트', 'description' => '밀레니엄 팔콘, 7541피스', 'price' => 120000, 'stock_quantity' => 5, 'category_id' => 8],
        ];

        foreach ($products as $product) {
            $categoryId = $product['category_id'];
            unset($product['category_id']);
            
            $newProduct = Product::firstOrCreate(['name' => $product['name']], $product);
            
            // 카테고리 연결
            $category = Category::find($categoryId);
            if ($category) {
                $newProduct->categories()->sync([$categoryId]);
            }
        }

        $this->command->info('상품 시드 완료 (' . Product::count() . '개)');
    }

    private function seedOrders()
    {
        $users = User::where('email', '!=', 'admin@amuz.com')->get();
        $products = Product::all();
        $statuses = [Order::STATUS_PENDING, Order::STATUS_PAID, Order::STATUS_SHIPPED, Order::STATUS_DELIVERED, Order::STATUS_CANCELLED];
        
        // 50개의 주문 생성
        for ($i = 0; $i < 50; $i++) {
            $user = $users->random();
            $status = $statuses[array_rand($statuses)];
            $orderDate = Carbon::now()->subDays(rand(1, 90));
            
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => 0, // 임시값, 나중에 계산
                'status' => $status,
                'ordered_at' => $orderDate,
                'notes' => rand(0, 1) ? '배송 시 문 앞에 놓아주세요' : null,
            ]);

            // 주문 아이템 생성 (1-3개 상품)
            $orderItems = [];
            $totalAmount = 0;
            
            $orderProductCount = rand(1, 3);
            $selectedProducts = $products->random($orderProductCount);
            
            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $price = $product->price;
                $totalPrice = $quantity * $price;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total_price' => $totalPrice,
                ]);
                
                $totalAmount += $totalPrice;
                
                // 재고 차감 (대기중이 아닌 주문만)
                if ($status !== Order::STATUS_PENDING) {
                    $product->decreaseStock($quantity);
                }
            }
            
            // 주문 총액 업데이트
            $order->update(['total_amount' => $totalAmount]);
        }

        $this->command->info('주문 시드 완료 (' . Order::count() . '건)');
        $this->command->info('상태별 주문 수:');
        foreach ($statuses as $status) {
            $count = Order::where('status', $status)->count();
            $this->command->info("   - {$status}: {$count}건");
        }
    }
}
