<?php

namespace App\Orchid\Screens;

use App\Models\Category;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Group;
use Illuminate\Http\Request;

class CategoryListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $search = request('search');
        $productCountFilter = request('product_count_filter');
        $statusFilter = request('status_filter');
        $sortBy = request('sort_by', 'created_at');
        $sortOrder = request('sort_order', 'desc');
        
        $categoriesQuery = Category::withCount('products');
        
        if ($search) {
            $categoriesQuery->where('name', 'like', "%{$search}%")
                           ->orWhere('description', 'like', "%{$search}%");
        }
        
        if ($productCountFilter) {
            if ($productCountFilter === 'many') {
                $categoriesQuery->having('products_count', '>=', 5);
            } elseif ($productCountFilter === 'few') {
                $categoriesQuery->having('products_count', '>', 0)
                               ->having('products_count', '<', 5);
            } elseif ($productCountFilter === 'empty') {
                $categoriesQuery->having('products_count', '=', 0);
            }
        }
        
        if ($statusFilter === 'active') {
            $categoriesQuery->where('is_active', true);
        } elseif ($statusFilter === 'inactive') {
            $categoriesQuery->where('is_active', false);
        }
        
        if ($sortBy === 'product_count') {
            $categoriesQuery->orderBy('products_count', $sortOrder);
        } else {
            $categoriesQuery->orderBy($sortBy, $sortOrder);
        }
        
        return [
            'categories' => $categoriesQuery->paginate(15)->withQueryString(),
            'search' => $search,
            'product_count_filter' => $productCountFilter,
            'status_filter' => $statusFilter,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'hasFilters' => $search || $productCountFilter || $statusFilter || $sortBy !== 'created_at'
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return '카테고리 관리';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('새 카테고리 생성')
                ->icon('plus')
                ->route('platform.categories.create')
                ->class('btn btn-success'),
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
                Group::make([
                    Input::make('search')
                        ->title('검색')
                        ->placeholder('카테고리명 또는 설명으로 검색')
                        ->value(request('search')),
                    
                    Select::make('product_count_filter')
                        ->title('상품 수')
                        ->empty('전체')
                        ->options([
                            'many' => '많음 (5개 이상)',
                            'few' => '적음 (1-4개)',
                            'empty' => '없음 (0개)'
                        ])
                        ->value(request('product_count_filter')),
                ])->alignEnd(),
                
                Group::make([
                    Select::make('status_filter')
                        ->title('상태')
                        ->empty('전체')
                        ->options([
                            'active' => '활성',
                            'inactive' => '비활성'
                        ])
                        ->value(request('status_filter')),
                    
                    Select::make('sort_by')
                        ->title('정렬')
                        ->options([
                            'created_at' => '생성일',
                            'name' => '이름',
                            'product_count' => '상품 수'
                        ])
                        ->value(request('sort_by', 'created_at')),
                        
                    Select::make('sort_order')
                        ->title('순서')
                        ->options([
                            'desc' => '내림차순',
                            'asc' => '오름차순'
                        ])
                        ->value(request('sort_order', 'desc')),
                ])->alignEnd(),
                
                Group::make([
                    Button::make('검색 적용')
                        ->icon('magnifier')
                        ->class('btn btn-primary')
                        ->method('applyFilters'),
                        
                    Link::make('초기화')
                        ->icon('refresh')
                        ->class('btn btn-outline-secondary')
                        ->route('platform.categories'),
                ])->autoWidth()->alignEnd(),
                            ])->title('카테고리 검색'),
            Layout::table('categories', [
                TD::make('name', '카테고리명')
                    ->sort()
                    ->cantHide()
                    ->render(function (Category $category) {
                        return Link::make($category->name)
                            ->route('platform.categories.edit', $category);
                    }),

                TD::make('description', '설명')
                    ->render(function (Category $category) {
                        return $category->description 
                            ? \Illuminate\Support\Str::limit($category->description, 50)
                            : '설명 없음';
                    }),

                TD::make('products_count', '상품 수')
                    ->sort()
                    ->render(function (Category $category) {
                        return $category->products_count . '개';
                    }),

                TD::make('is_active', '상태')
                    ->sort()
                    ->render(function (Category $category) {
                        return $category->is_active 
                            ? '<span class="badge bg-success">활성</span>'
                            : '<span class="badge bg-secondary">비활성</span>';
                    }),

                TD::make('created_at', '생성일')
                    ->sort()
                    ->render(function (Category $category) {
                        return $category->created_at->format('Y-m-d H:i');
                    }),

                TD::make('actions', '작업')
                    ->align(TD::ALIGN_CENTER)
                    ->width('160px')
                    ->render(function (Category $category) {
                        return view('orchid.partials.actions', [
                            'editUrl' => route('platform.categories.edit', $category),
                            'deleteConfirm' => '정말 삭제하시겠습니까?',
                            'deleteAction' => 'remove',
                            'itemId' => $category->id
                        ])->render();
                    }),
            ])->title('카테고리 목록')
        ];
    }

    public function applyFilters(Request $request)
    {
        $filters = $request->only([
            'search', 'product_count_filter', 'status_filter', 
            'sort_by', 'sort_order'
        ]);
        
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });
        
        return redirect()->route('platform.categories', $filters);
    }

    public function remove(Request $request): void
    {
        $category = Category::findOrFail($request->get('id'));
        
        if ($category->products()->count() > 0) {
            Toast::warning('이 카테고리에 속한 상품이 있어 삭제할 수 없습니다.');
            return;
        }

        $category->delete();
        Toast::info('카테고리가 삭제되었습니다.');
    }
}
