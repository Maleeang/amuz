<div class="d-flex gap-2 justify-content-center">
    @if($order->trashed())
        {{-- 삭제된 주문 복원 버튼 --}}
        <button type="button" 
                class="btn btn-sm btn-success" 
                style="width: 40px; height: 30px; display: flex; align-items: center; justify-content: center; padding: 0;"
                title="주문 복원"
                onclick="
                    if(confirm('삭제된 주문 #{{ $order->id }}을(를) 복원하시겠습니까?')) {
                        window.location.href = '{{ route('platform.orders') }}?restoreOrder=1&id={{ $order->id }}';
                    }
                ">
            복원
        </button>
    @else
        {{-- 일반 주문 수정 및 삭제 버튼 --}}
        <a href="{{ route('platform.orders.edit', $order) }}" 
           class="btn btn-sm btn-primary" 
           style="width: 40px; height: 30px; display: flex; align-items: center; justify-content: center; padding: 0;" 
           title="주문 수정">
            수정
        </a>
        
        <button type="button" 
                class="btn btn-sm btn-danger" 
                style="width: 40px; height: 30px; display: flex; align-items: center; justify-content: center; padding: 0;"
                title="주문 삭제"
                onclick="
                    if(confirm('주문 #{{ $order->id }}을(를) 삭제하시겠습니까?')) {
                        window.location.href = '{{ route('platform.orders') }}?deleteOrder=1&id={{ $order->id }}';
                    }
                ">
            삭제
        </button>
    @endif
</div>