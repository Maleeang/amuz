<div class="bg-light p-3 rounded">
    <h5>📋 주문 변경 이력</h5>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>시간</th>
                    <th>작업</th>
                    <th>변경사항</th>
                    <th>변경자</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->histories as $history)
                    <tr>
                        <td class="text-muted small">
                            {{ $history->created_at->format('m/d H:i') }}
                        </td>
                        <td>
                            @switch($history->action)
                                @case('status_changed')
                                    <span class="badge bg-info">상태변경</span>
                                    @break
                                @case('order_updated')
                                    <span class="badge bg-warning">정보수정</span>
                                    @break
                                @case('archived')
                                    <span class="badge bg-secondary">삭제</span>
                                    @break
                                @case('restored')
                                    <span class="badge bg-success">복원</span>
                                    @break
                                @default
                                    <span class="badge bg-light text-dark">{{ $history->action }}</span>
                            @endswitch
                        </td>
                        <td class="small">
                            @if($history->old_status && $history->new_status)
                                {{ $history->old_status }} → {{ $history->new_status }}
                            @elseif($history->metadata && isset($history->metadata['changes']))
                                @foreach($history->metadata['changes'] as $field => $change)
                                    <strong>{{ $field }}:</strong> 
                                    {{ $change['old'] ?? 'null' }} → {{ $change['new'] }}
                                    @if(!$loop->last)<br>@endif
                                @endforeach
                            @else
                                {{ $history->reason }}
                            @endif
                        </td>
                        <td class="small text-muted">
                            {{ $history->changed_by }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            변경 이력이 없습니다.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>