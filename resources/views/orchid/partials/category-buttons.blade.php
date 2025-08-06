<div class="category-buttons-container">
    @foreach($categories as $category)
        <div class="category-button-item">
            <input type="checkbox" 
                   name="categories[{{ $category->id }}]" 
                   id="category_{{ $category->id }}" 
                   value="1" 
                   {{ in_array($category->id, $selectedCategoryIds) ? 'checked' : '' }}
                   onchange="updateSelectedCategories()">
            <label for="category_{{ $category->id }}" data-category-name="{{ $category->name }}">
                {{ $category->name }}
            </label>
        </div>
    @endforeach
</div>

<style>
    .category-buttons-container { 
        margin: 1rem 0; 
        padding: 0.5rem; 
        border: 1px solid #dee2e6; 
        border-radius: 0.375rem; 
        background-color: #f8f9fa; 
    }
    .category-button-item { 
        display: inline-block; 
        margin: 0.25rem; 
    }
    .category-button-item input[type="checkbox"] { 
        display: none; 
    }
    .category-button-item label {
        display: inline-block;
        padding: 0.5rem 1rem;
        background-color: #ffffff;
        border: 2px solid #dee2e6;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        font-weight: 500;
        user-select: none;
        margin: 0;
        min-width: 80px;
        text-align: center;
    }
    .category-button-item label:hover {
        background-color: #e9ecef;
        border-color: #adb5bd;
    }
    .category-button-item input:checked + label {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }
</style>

<script>
function updateSelectedCategories() {
    const checkboxes = document.querySelectorAll('input[name^="categories["]');
    const selectedCategories = [];
    
    checkboxes.forEach(function(checkbox) {
        if (checkbox.checked) {
            const label = document.querySelector('label[for="' + checkbox.id + '"]');
            const categoryName = label.getAttribute('data-category-name');
            selectedCategories.push(categoryName);
        }
    });
    
    const displayField = document.querySelector('input[name="categories_display"]');
    if (displayField) {
        if (selectedCategories.length > 0) {
            displayField.value = selectedCategories.join(', ');
        } else {
            displayField.value = '선택된 카테고리가 없습니다';
        }
    }
}


document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCategories();
});
</script> 