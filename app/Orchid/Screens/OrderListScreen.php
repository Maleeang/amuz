<?php

namespace App\Orchid\Screens;

use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\User;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Illuminate\Http\Request;

class OrderListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        if (request('updateStatus') && request('id') && request('newStatus')) {
            $this->handleStatusUpdate();
        }
        
        if (request('deleteOrder') && request('id')) {
            $this->handleOrderDelete();
        }
        
        if (request('restoreOrder') && request('id')) {
            $this->handleOrderRestore();
        }
        
        $status = request('status', 'all');
        $search = request('search');
        $orderIdSearch = request('order_id');
        $sortBy = request('sort_by', 'created_at');
        $sortOrder = request('sort_order', 'desc');
        
        $ordersQuery = Order::with(['user', 'orderItems.product.categories'])
            ->withCount('orderItems');
            
        if ($status === 'archived') {
            $ordersQuery->onlyTrashed();
        } else {
            if ($status !== 'all') {
                $ordersQuery->where('status', $status);
            }
        }
        
        if ($search) {
            $ordersQuery->whereHas('user', function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($orderIdSearch) {
            $ordersQuery->where('id', $orderIdSearch);
        }

        

        
        if ($sortBy === 'amount') {
            $ordersQuery->orderBy('total_amount', $sortOrder);
        } elseif ($sortBy === 'items') {
            $ordersQuery->orderBy('order_items_count', $sortOrder);
        } else {
            $ordersQuery->orderBy($sortBy, $sortOrder);
        }

        return [
            'orders' => $ordersQuery->paginate(15)->withQueryString(),
            'orderStats' => [
                'all' => Order::count(),
                'pending' => Order::where('status', 'pending')->count(),
                'paid' => Order::where('status', 'paid')->count(),
                'shipped' => Order::where('status', 'shipped')->count(),
                'cancelled' => Order::where('status', 'cancelled')->count(),
                'archived' => Order::onlyTrashed()->count(),
            ],
            'currentStatus' => $status,
            'search' => $search,
            'order_id' => $orderIdSearch,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'hasFilters' => $search || $orderIdSearch || $sortBy !== 'created_at' || $status !== 'all'
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return '주문 관리';
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
            Layout::rows([
                Group::make([
                    Input::make('search')
                        ->title('👤 사용자 검색')
                        ->placeholder('사용자명 또는 이메일로 검색')
                        ->value(request('search')),
                    
                    Input::make('order_id')
                        ->type('number')
                        ->title('🔢 주문번호')
                        ->placeholder('주문 ID')
                        ->value(request('order_id')),
                        
                    Select::make('status')
                        ->title('📋 주문 상태')
                        ->options([
                            'all' => '전체',
                            'pending' => '대기중',
                            'paid' => '결제완료',
                            'shipped' => '배송중',
                            'cancelled' => '취소됨',
                            'archived' => '삭제기록'
                        ])
                        ->value(request('status', 'all')),
                ])->alignEnd(),
                
                Group::make([
                    Select::make('sort_by')
                        ->title('🔄 정렬')
                        ->options([
                            'created_at' => '주문일',
                            'amount' => '주문 금액',
                            'items' => '상품 수량'
                        ])
                        ->value(request('sort_by', 'created_at')),
                        
                    Select::make('sort_order')
                        ->title('⬆️⬇️ 순서')
                        ->options([
                            'desc' => '내림차순',
                            'asc' => '오름차순'
                        ])
                        ->value(request('sort_order', 'desc')),
                        
                    Button::make('검색 적용')
                        ->icon('magnifier')
                        ->class('btn btn-primary')
                        ->method('applyFilters'),
                        
                    Link::make('초기화')
                        ->icon('refresh')
                        ->class('btn btn-outline-secondary')
                        ->route('platform.orders'),
                ])->autoWidth()->alignEnd(),
            ])->title('🔍 주문 검색'),
            Layout::table('orders', [
                TD::make('id', '주문번호')
                    ->sort()
                    ->cantHide()
                    ->render(function (Order $order) {
                        $orderNumber = "#" . str_pad($order->id, 6, '0', STR_PAD_LEFT);
                        if ($order->trashed()) {
                            return $orderNumber;
                        }
                        return "<a href='" . route('platform.orders.edit', $order) . "' class='text-primary'>{$orderNumber}</a>";
                    }),

                TD::make('user.name', '주문자')
                    ->sort()
                    ->render(function (Order $order) {
                        return $order->user->name . '<br><small class="text-muted">' . $order->user->email . '</small>';
                    }),

                TD::make('order_items', '주문 상품')
                    ->width('200px')
                    ->render(function (Order $order) {
                        return view('orchid.partials.order-items-list', [
                            'order' => $order
                        ])->render();
                    }),

                TD::make('total_amount', '주문 금액')
                    ->sort()
                    ->render(function (Order $order) {
                        return '₩' . number_format($order->total_amount);
                    }),

                TD::make('status', '주문 상태')
                    ->sort()
                    ->width('150px')
                    ->render(function (Order $order) {
                        $statusOptions = [
                            'pending' => '대기중',
                            'paid' => '결제완료', 
                            'shipped' => '배송중',
                            'cancelled' => '취소됨'
                        ];
                        
                        return view('orchid.partials.status-select', [
                            'order' => $order,
                            'statusOptions' => $statusOptions,
                            'currentStatus' => $order->status
                        ])->render();
                    }),

                TD::make('ordered_at', '주문일시')
                    ->sort()
                    ->render(function (Order $order) {
                        return $order->ordered_at->format('Y-m-d H:i');
                    }),

                TD::make('actions', '작업')
                    ->align(TD::ALIGN_CENTER)
                    ->width('100px')
                    ->render(function (Order $order) {
                        return view('orchid.partials.order-simple-actions', [
                            'order' => $order
                        ])->render();
                    }),
            ])->title('주문 목록')
        ];
    }



    /**
     * 주문 상태 변경
     */
    public function updateStatus(Request $request): void
    {
        $order = Order::findOrFail($request->get('id'));
        $newStatus = $request->get('status');
        
        if ($newStatus === 'cancelled' && $order->status !== 'cancelled') {
            foreach ($order->orderItems as $item) {
                $item->product->restoreStock($item->quantity);
            }
        }
        
        $order->update(['status' => $newStatus]);
        
        $statusLabels = [
            'pending' => '대기중',
            'paid' => '결제완료',
            'shipped' => '배송중',
            'cancelled' => '취소됨',
        ];
        
        if ($newStatus === 'cancelled') {
            Toast::success("주문 #{$order->id}이(가) 취소되었으며 재고가 복원되었습니다.");
        } else {
            Toast::info("주문 #{$order->id}이(가) {$statusLabels[$newStatus]}로 변경되었습니다.");
        }
    }

    /**
     * 주문 취소 및 재고 복원
     */
    public function cancelOrder(Request $request): void
    {
        $order = Order::findOrFail($request->get('id'));
        
        if ($order->status === 'cancelled') {
            Toast::warning('이미 취소된 주문입니다.');
            return;
        }

        foreach ($order->orderItems as $item) {
            $item->product->restoreStock($item->quantity);
        }

        $order->update(['status' => 'cancelled']);
        
        Toast::success("주문 #{$order->id}이(가) 취소되었으며 재고가 복원되었습니다.");
    }

    /**
     * 주문 삭제
     */
    public function deleteOrder(Request $request): void
    {
        $order = Order::findOrFail($request->get('id'));
        
        if ($order->status !== 'cancelled') {
            foreach ($order->orderItems as $item) {
                $item->product->restoreStock($item->quantity);
            }
        }
        
        $orderId = $order->id;
        $order->delete();
        
        Toast::success("주문 #{$orderId}이(가) 삭제되었습니다.");
    }

    /**
     * 검색 적용
     */
    public function applyFilters(Request $request)
    {
        $filters = $request->only([
            'search', 'order_id', 'status', 'sort_by', 'sort_order'
        ]);
        
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });
        
        return redirect()->route('platform.orders', $filters);
    }

    /**
     * GET 방식으로 상태 업데이트 처리
     */
    private function handleStatusUpdate(): void
    {
        $order = Order::with('orderItems.product.categories')->findOrFail(request('id'));
        $oldStatus = $order->status;
        $newStatus = request('newStatus');
        
        if ($oldStatus === $newStatus) {
            return;
        }
        
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            foreach ($order->orderItems as $item) {
                $item->product->restoreStock($item->quantity);
            }
        } elseif ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
            foreach ($order->orderItems as $item) {
                $beforeStock = $item->product->stock_quantity;
                $result = $item->product->decreaseStock($item->quantity);
                $afterStock = $item->product->fresh()->stock_quantity;
                
                if ($result) {
                    \Log::info("재고 차감 성공: {$item->product->name} ({$beforeStock} → {$afterStock})");
                } else {
                    \Log::warning("재고 부족: {$item->product->name} (현재:{$beforeStock}, 필요:{$item->quantity})");
                    Toast::warning("상품 '{$item->product->name}'의 재고가 부족합니다.");
                }
            }
        }
        
        $order->update(['status' => $newStatus]);
        
        OrderHistory::log($order->id, 'status_changed', [
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'reason' => "관리자에 의한 상태 변경: {$oldStatus} → {$newStatus}",
            'changed_by' => 'admin',
            'metadata' => [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]
        ]);
        
        $statusLabels = [
            'pending' => '대기중',
            'paid' => '결제완료',
            'shipped' => '배송중',
            'cancelled' => '취소됨',
        ];
        
        if ($newStatus === 'cancelled') {
            Toast::success("주문 #{$order->id}이(가) 취소되었으며 재고가 복원되었습니다.");
        } elseif ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
            Toast::success("주문 #{$order->id}이(가) {$statusLabels[$newStatus]}로 변경되었으며 재고가 차감되었습니다.");
        } else {
            Toast::info("주문 #{$order->id}이(가) {$statusLabels[$newStatus]}로 변경되었습니다.");
        }
        
        redirect()->route('platform.orders')->send();
    }

    private function handleOrderDelete(): void
    {
        $order = Order::findOrFail(request('id'));
        
        if ($order->status !== 'cancelled') {
            foreach ($order->orderItems as $item) {
                $item->product->restoreStock($item->quantity);
            }
        }
        
        $orderId = $order->id;
        $oldStatus = $order->status;

        $order->delete();
        
        OrderHistory::log($orderId, 'archived', [
            'old_status' => $oldStatus,
            'new_status' => null,
            'reason' => '관리자에 의한 주문 삭제',
            'changed_by' => 'admin',
            'metadata' => [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'stock_restored' => $order->status !== 'cancelled'
            ]
        ]);
        
        Toast::success("주문 #{$orderId}이(가) 삭제되었습니다.");
        
        redirect()->route('platform.orders')->send();
    }

    private function handleOrderRestore(): void
    {
        $order = Order::onlyTrashed()->findOrFail(request('id'));
        
        $orderId = $order->id;
        $status = $order->status;
        
        $order->restore();
        
        OrderHistory::log($orderId, 'restored', [
            'old_status' => null,
            'new_status' => $status,
            'reason' => '관리자에 의한 주문 복원',
            'changed_by' => 'admin',
            'metadata' => [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]
        ]);
        
        Toast::success("주문 #{$orderId}이(가) 복원되었습니다.");
        
        redirect()->route('platform.orders')->send();
    }
}
