<?php

namespace App\Orchid\Screens;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Orchid\Layouts\Charts\OrderTrendChart;
use App\Orchid\Layouts\Charts\TopProductsChart;
use App\Orchid\Layouts\Charts\OrderStatusChart;
use App\Orchid\Layouts\Charts\UserGrowthChart;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Metrics\Value;
use Carbon\Carbon;

class MainDashboardScreen extends Screen
{

    public function query(): iterable
    {
        $totalUsers = User::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::whereNotIn('status', ['cancelled'])->sum('total_amount');
        $pendingOrders = Order::where('status', 'pending')->count();
        
        $topProducts = Product::withCount('orderItems')
            ->with('categories')
            ->orderBy('order_items_count', 'desc')
            ->limit(5)
            ->get();
            
        $categoriesWithProducts = Category::withCount('products')->get();

        $last7DaysUsers = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $totalUserCount = User::where('created_at', '<=', $date->endOfDay())->count();
            $last7DaysUsers->push([
                'date' => $date->format('m/d'),
                'count' => $totalUserCount
            ]);
        }
        
        $userGrowthData = [
            [
                'name' => '전체 회원수',
                'values' => $last7DaysUsers->pluck('count')->toArray(),
                'labels' => $last7DaysUsers->pluck('date')->toArray(),
            ]
        ];

        $last7DaysOrders = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $orderCount = Order::whereDate('created_at', $date)->count();
            $last7DaysOrders->push([
                'date' => $date->format('m/d'),
                'count' => $orderCount
            ]);
        }
        
        $orderTrendData = [
            [
                'name' => '일별 주문 건수',
                'values' => $last7DaysOrders->pluck('count')->toArray(),
                'labels' => $last7DaysOrders->pluck('date')->toArray(),
            ]
        ];

        $topProductsChart = Product::withCount('orderItems')
            ->with('categories')
            ->orderBy('order_items_count', 'desc')
            ->limit(5)
            ->get();
            
        $topProductsData = [
            [
                'name' => '주문 건수',
                'values' => $topProductsChart->pluck('order_items_count')->toArray(),
                'labels' => $topProductsChart->pluck('name')->toArray(),
            ]
        ];

        $statusLabels = [
            'pending' => '대기중',
            'paid' => '결제완료', 
            'shipped' => '배송중',
            'delivered' => '배송완료',
            'cancelled' => '취소됨'
        ];
        
        $ordersByStatus = Order::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
            
        $statusData = [
            [
                'name' => '주문 상태 분포',
                'values' => array_values($ordersByStatus),
                'labels' => array_map(function($status) use ($statusLabels) {
                    return $statusLabels[$status] ?? $status;
                }, array_keys($ordersByStatus)),
            ]
        ];

        return [
            'totalUsers' => number_format($totalUsers),
            'totalOrders' => number_format($totalOrders),
            'totalRevenue' => '₩' . number_format($totalRevenue),
            'pendingOrders' => number_format($pendingOrders),
            
            'topProducts' => $topProducts,
            'topProducts' => $topProducts,
            'categoriesWithProducts' => $categoriesWithProducts,
            
            'userGrowthChart' => $userGrowthData,
            'orderTrendChart' => $orderTrendData,
            'topProductsChart' => $topProductsData,
            'orderStatusChart' => $statusData,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return '대시보드';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::metrics([
                '전체 사용자' => 'totalUsers',
                '총 주문 건수' => 'totalOrders', 
                '총 매출액' => 'totalRevenue',
                '대기중 주문' => 'pendingOrders',
            ]),

            Layout::columns([
                UserGrowthChart::make('userGrowthChart', '최근 7일간 전체 회원수'),
                    
                OrderTrendChart::make('orderTrendChart', '최근 7일간 주문 추이'),
                    
                OrderStatusChart::make('orderStatusChart', '주문 상태별 분포'),
            ]),

            TopProductsChart::make('topProductsChart', 'Top 5 인기 상품'),

            Layout::columns([
                Layout::table('topProducts', [
                    TD::make('name', '상품명')
                        ->cantHide(),
                    TD::make('category.name', '카테고리')
                        ->render(function (Product $product) {
                            return $product->first_category ? $product->first_category->name : '미분류';
                        }),
                    TD::make('order_items_count', '주문 건수')
                        ->render(function (Product $product) {
                            return '<span class="badge bg-primary">' . $product->order_items_count . '건</span>';
                        }),
                    TD::make('stock_quantity', '재고수량')
                        ->render(function (Product $product) {
                            $color = $product->stock_quantity < 10 ? 'danger' : 'success';
                            return '<span class="badge bg-' . $color . '">' . $product->stock_quantity . '개</span>';
                        }),
                    TD::make('price', '가격')
                        ->render(function (Product $product) {
                            return '<strong>₩' . number_format($product->price) . '</strong>';
                        }),
                ])->title('Top 5 주문 건수 상품 - 상세'),
                
                Layout::table('categoriesWithProducts', [
                    TD::make('name', '카테고리명')
                        ->cantHide(),
                    TD::make('products_count', '상품 수')
                        ->render(function (Category $category) {
                            return '<span class="badge bg-info">' . $category->products_count . '개</span>';
                        }),
                    TD::make('description', '설명')
                        ->render(function (Category $category) {
                            return $category->description ?: '<em class="text-muted">설명 없음</em>';
                        }),
                    TD::make('is_active', '활성화')
                        ->render(function (Category $category) {
                            return $category->is_active 
                                ? '<span class="badge bg-success">활성</span>' 
                                : '<span class="badge bg-secondary">비활성</span>';
                        }),
                ])->title('카테고리별 상품 현황'),
            ]),
        ];
    }
}
