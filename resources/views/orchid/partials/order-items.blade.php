<div class="bg-light p-3 rounded mb-3">
    <h5>🛒 주문 상품 목록</h5>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>상품명</th>
                    <th>카테고리</th>
                    <th class="text-center">단가</th>
                    <th class="text-center">수량</th>
                    <th class="text-end">소계</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->orderItems as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product->name }}</strong>
                            @if($item->product->description)
                                <br><small class="text-muted">{{ Str::limit($item->product->description, 50) }}</small>
                            @endif
                        </td>
                        <td>
                                    @if($item->product->first_category)
            <span class="badge bg-secondary">{{ $item->product->first_category->name }}</span>
                            @else
                                <span class="badge bg-light text-dark">카테고리 없음</span>
                            @endif
                        </td>
                        <td class="text-center">
                            ₩{{ number_format($item->price) }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $item->quantity }}개</span>
                        </td>
                        <td class="text-end">
                            <strong>₩{{ number_format($item->price * $item->quantity) }}</strong>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            주문 상품이 없습니다.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($order->orderItems->count() > 0)
                <tfoot>
                    <tr class="table-active">
                        <th colspan="4" class="text-end">총 주문 금액:</th>
                        <th class="text-end">
                            <span class="h5 text-primary">₩{{ number_format($order->total_amount) }}</span>
                        </th>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>