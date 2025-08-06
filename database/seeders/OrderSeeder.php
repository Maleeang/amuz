<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'user@amuz.com')->first();
        
        if (!$user) {
            $this->command->error('user@amuz.com 유저를 찾을 수 없습니다.');
            return;
        }

        $products = Product::where('is_active', true)->get();
        
        if ($products->isEmpty()) {
            $this->command->error('활성화된 상품이 없습니다.');
            return;
        }

        $this->command->info('user@amuz.com 유저에게 20개의 주문을 생성합니다...');

        $statuses = [
            Order::STATUS_PENDING,
            Order::STATUS_PAID,
            Order::STATUS_SHIPPED,
            Order::STATUS_DELIVERED,
            Order::STATUS_CANCELLED
        ];
        $statusLabels = [
            Order::STATUS_PENDING => '대기중',
            Order::STATUS_PAID => '결제완료',
            Order::STATUS_SHIPPED => '배송중',
            Order::STATUS_DELIVERED => '배송완료',
            Order::STATUS_CANCELLED => '취소됨'
        ];

        $notes = [
            '빠른 배송 부탁드립니다.',
            '택배함에 보관해주세요.',
            '문 앞에 놓아주세요.',
            '부재시 연락주세요.',
            '조심히 다뤄주세요.',
            null,
        ];

        for ($i = 1; $i <= 20; $i++) {
            DB::beginTransaction();
            
            try {
                $orderDate = now()->subDays(rand(0, 90))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
                
                $status = $statuses[array_rand($statuses)];
                
                $order = Order::create([
                    'user_id' => $user->id,
                    'total_amount' => 0,
                    'status' => $status,
                    'ordered_at' => $orderDate,
                    'notes' => $notes[array_rand($notes)],
                ]);

                $orderItems = [];
                $totalAmount = 0;
                $selectedProducts = $products->random(rand(1, min(3, $products->count())));
                
                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, min(3, $product->stock_quantity));
                    $itemTotal = $product->price * $quantity;
                    $totalAmount += $itemTotal;
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $product->price,
                        'total_price' => $itemTotal,
                    ]);
                    
                    if ($status !== Order::STATUS_PENDING) {
                        $product->decreaseStock($quantity);
                    }
                }
                
                $order->update(['total_amount' => $total_amount]);
                
                DB::commit();
                
                $this->command->info("주문 #{$order->id} 생성 완료 - 총액: ₩" . number_format($totalAmount));
                
            } catch (\Exception $e) {
                DB::rollback();
                $this->command->error("주문 생성 실패: " . $e->getMessage());
            }
        }

        $this->command->info('주문 생성이 완료되었습니다!');
        
        
        $orderCount = Order::where('user_id', $user->id)->count();
        $this->command->info("총 주문 수: {$orderCount}개");
        

        foreach ($statuses as $status) {
            $count = Order::where('user_id', $user->id)->where('status', $status)->count();
            $this->command->info("{$statusLabels[$status]}: {$count}개");
        }
    }
}
