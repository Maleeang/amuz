<?php

namespace App\Orchid\Screens;

use App\Models\Category;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Illuminate\Http\Request;

class CategoryEditScreen extends Screen
{
    /**
     * @var Category
     */
    public $category;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Category $category = null): iterable
    {
        if (!$category) {
            $category = new Category();
        }
        
        return [
            'category' => $category
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        $category = request()->route('category');
        return ($category && $category->exists) 
            ? '카테고리 편집: ' . $category->name
            : '새 카테고리 생성';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('저장')
                ->icon('check')
                ->method('save')
                ->class('btn btn-success'),

            Button::make('취소')
                ->icon('close')
                ->route('platform.categories')
                ->class('btn btn-secondary'),
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
                Input::make('category.name')
                    ->title('카테고리명')
                    ->placeholder('예: 전자제품, 의류, 도서 등')
                    ->required(),

                TextArea::make('category.description')
                    ->title('설명')
                    ->placeholder('카테고리에 대한 간단한 설명을 입력하세요.')
                    ->rows(4),

                CheckBox::make('category.is_active')
                    ->title('활성화 상태')
                    ->placeholder('활성화된 카테고리만 사용자에게 표시됩니다.')
                    ->value(true)
                    ->sendTrueOrFalse(),
            ])->title('카테고리 정보'),

            ...(request()->route('category') && request()->route('category')->exists) ? [
                Layout::rows([
                    Input::make('info.created_at')
                        ->title('생성일')
                        ->value(request()->route('category')->created_at->format('Y-m-d H:i:s'))
                        ->readonly(),

                    Input::make('info.updated_at')
                        ->title('수정일')
                        ->value(request()->route('category')->updated_at->format('Y-m-d H:i:s'))
                        ->readonly(),

                    Input::make('info.products_count')
                        ->title('등록된 상품 수')
                        ->value(request()->route('category')->products()->count() . '개')
                        ->readonly(),
                ])->title('카테고리 통계')
            ] : [],
        ];
    }

    /**
     * 카테고리 저장
     *
     * @param Category $category
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Category $category, Request $request)
    {
        $request->validate([
            'category.name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'category.description' => 'nullable|string|max:1000',
            'category.is_active' => 'boolean',
        ], [
            'category.name.required' => '카테고리명은 필수입니다.',
            'category.name.unique' => '이미 존재하는 카테고리명입니다.',
            'category.description.max' => '설명은 1000자 이하로 입력해주세요.',
        ]);

        $category->fill($request->get('category'))->save();

        if ($category->wasRecentlyCreated) {
            Toast::success('새 카테고리가 생성되었습니다.');
        } else {
            Toast::info('카테고리가 수정되었습니다.');
        }

        return redirect()->route('platform.categories');
    }
}
