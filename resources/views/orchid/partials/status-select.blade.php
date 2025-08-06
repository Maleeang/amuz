@php
    $statusClasses = [
        'pending' => 'btn-warning',
        'paid' => 'btn-success',
        'shipped' => 'btn-info',
        'cancelled' => 'btn-dark',
    ];
    $btnClass = $statusClasses[$order->status] ?? 'btn-secondary';
@endphp

@if($order->trashed())
    {{-- 삭제된 주문은 변경 불가 --}}
    <span class="btn btn-sm btn-outline-dark" style="min-width: 90px; cursor: default;">
        🗑️ 삭제됨
    </span>
@else
    {{-- 모든 주문에 상태 변경 가능 --}}
    <select class="btn btn-sm {{ $btnClass }}" 
            style="min-width: 90px; border: none; appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;4&quot; height=&quot;5&quot;><path fill=&quot;%23ffffff&quot; d=&quot;M2 0L0 2h4z&quot;/></svg>'); background-repeat: no-repeat; background-position: right 8px center; padding-right: 25px;"
            onchange="
                if(this.value === 'cancelled' && !confirm('주문을 취소하시겠습니까? 재고가 복원됩니다.')) { 
                    this.value = '{{ $order->status }}'; 
                    return false; 
                }
                window.location.href = '{{ route('platform.orders') }}?updateStatus=1&id={{ $order->id }}&newStatus=' + this.value;
            ">
        
        @foreach($statusOptions as $value => $label)
            <option value="{{ $value }}" 
                    {{ $order->status === $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
@endif