<?php

namespace App\Orchid\Screens;

use App\Models\Order;
use App\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Illuminate\Http\Request;

class OrderEditScreen extends Screen
{
    /**
     * @var Order
     */
    public $order;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Order $order): iterable
    {
        return [
            'order' => $order->load(['orderItems.product.categories', 'user']),
            'users' => User::pluck('name', 'id'),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->order->exists 
            ? '📝 주문 수정: #' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT)
            : '➕ 새 주문 생성';
    }


    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('💾 저장')
                ->icon('check')
                ->method('save'),

            Button::make('↩️ 목록으로')
                ->icon('arrow-left')
                ->method('goToList'),
        ];
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
                Input::make('order.total_amount')
                    ->title('주문 금액')
                    ->type('number')
                    ->step(0.01)
                    ->required(),

                Select::make('order.user_id')
                    ->title('주문자')
                    ->fromQuery(User::query(), 'name')
                    ->required(),

                Select::make('order.status')
                    ->title('주문 상태')
                    ->options([
                        'pending' => '대기중',
                        'paid' => '결제완료',
                        'shipped' => '배송중',
                        'delivered' => '배송완료',
                        'cancelled' => '취소됨',
                    ])
                    ->required(),

                TextArea::make('order.notes')
                    ->title('주문 메모')
                    ->rows(3),
            ])->title('주문 정보'),

            ...(request()->route('order') && request()->route('order')->exists) ? [
                Layout::view('orchid.partials.order-items', [
                    'order' => request()->route('order')
                ])
            ] : [],

            ...(request()->route('order') && request()->route('order')->exists) ? [
                Layout::view('orchid.partials.order-history', [
                    'order' => request()->route('order')
                ])
            ] : [],
        ];
    }

    /**
     * 주문 저장
     */
    public function save(Request $request)
    {
        $orderData = $request->validate([
            'order.total_amount' => 'required|numeric|min:0',
            'order.user_id' => 'required|exists:users,id',
            'order.status' => 'required|in:pending,paid,shipped,delivered,cancelled',
            'order.notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $this->order->getOriginal('status');
        $newStatus = $orderData['order']['status'];
        
        if ($oldStatus && $oldStatus !== $newStatus) {
            $this->order->load('orderItems.product.categories');
            
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($this->order->orderItems as $item) {
                    $item->product->restoreStock($item->quantity);
                    \Log::info("재고 복원: {$item->product->name} (+{$item->quantity})");
                }
                Toast::success("주문이 취소되었으며 재고가 복원되었습니다.");
            } elseif ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                foreach ($this->order->orderItems as $item) {
                    $beforeStock = $item->product->stock_quantity;
                    $result = $item->product->decreaseStock($item->quantity);
                    
                    if ($result) {
                        \Log::info("재고 차감: {$item->product->name} (-{$item->quantity})");
                    } else {
                        \Log::warning("재고 부족: {$item->product->name} (현재:{$beforeStock}, 필요:{$item->quantity})");
                        Toast::warning("상품 '{$item->product->name}'의 재고가 부족합니다.");
                        return redirect()->back()->withInput();
                    }
                }
                Toast::success("주문 상태가 변경되었으며 재고가 차감되었습니다.");
            } else {
                Toast::info('주문이 저장되었습니다.');
            }
        } else {
            Toast::info('주문이 저장되었습니다.');
        }

        $this->order->fill($orderData['order']);
        
        if (!$this->order->exists) {
            $this->order->ordered_at = now();
        }
        
        $this->order->save();
        
        return redirect()->route('platform.orders');
    }

    /**
     * 목록으로 돌아가기
     */
    public function goToList(): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('platform.orders');
    }
}
