<div class="small">
    @if($order->orderItems->count() > 0)
        @foreach($order->orderItems as $item)
            <div class="mb-1">
                {{ $item->product->name }}
                @if($item->quantity > 1)
                    <span class="text-muted">({{ $item->quantity }}개)</span>
                @endif
            </div>
        @endforeach
    @else
        <span class="text-muted">상품 없음</span>
    @endif
</div>