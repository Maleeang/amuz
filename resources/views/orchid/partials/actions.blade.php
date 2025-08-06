<div class="d-flex gap-2 justify-content-center">
    <a href="{{ $editUrl }}" 
       class="btn btn-sm btn-primary" 
       style="width: 50px; height: 32px; display: flex; align-items: center; justify-content: center; padding: 0;" 
       title="수정">
        수정
    </a>
    
    <form method="POST" action="{{ request()->url() }}" style="display: inline;">
        @csrf
        <input type="hidden" name="method" value="{{ $deleteAction }}">
        <input type="hidden" name="id" value="{{ $itemId }}">
        <button type="submit" 
                class="btn btn-sm btn-danger" 
                style="width: 50px; height: 32px; display: flex; align-items: center; justify-content: center; padding: 0;"
                title="삭제"
                onclick="return confirm('{{ $deleteConfirm }}')">
            삭제
        </button>
    </form>
</div>