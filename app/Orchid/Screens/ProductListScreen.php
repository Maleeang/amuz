<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use App\Models\Category;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Group;
use Illuminate\Http\Request;

class ProductListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $search = request('search');
        $categoryFilter = request('category_filter');
        $minPrice = request('min_price');
        $maxPrice = request('max_price');
        $stockFilter = request('stock_filter');
        
        $productsQuery = Product::with(['categories', 'orderItems'])
            ->withCount('orderItems');
        
        if ($search) {
            $productsQuery->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($categoryFilter) {
            $productsQuery->whereHas('categories', function($query) use ($categoryFilter) {
                $query->where('category_id', $categoryFilter);
            });
        }
        
        if ($minPrice) {
            $productsQuery->where('price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $productsQuery->where('price', '<=', $maxPrice);
        }
        
        if ($stockFilter) {
            if ($stockFilter === 'in_stock') {
                $productsQuery->where('stock_quantity', '>', 0);
            } elseif ($stockFilter === 'out_of_stock') {
                $productsQuery->where('stock_quantity', '<=', 0);
            } elseif ($stockFilter === 'low_stock') {
                $productsQuery->where('stock_quantity', '>', 0)
                             ->where('stock_quantity', '<=', 10);
            }
        }
        
        return [
            'products' => $productsQuery->orderBy('created_at', 'desc')
                ->paginate(15)->withQueryString(),
            'categories' => Category::active()->pluck('name', 'id'),
            'search' => $search,
            'category_filter' => $categoryFilter,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'stock_filter' => $stockFilter,
            'hasFilters' => $search || $categoryFilter || $minPrice || $maxPrice || $stockFilter
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return '상품 관리';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('새 상품 등록')
                ->icon('plus')
                ->route('platform.products.create')
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
                        ->title('🔍 상품 검색')
                        ->placeholder('상품명 또는 설명으로 검색')
                        ->value(request('search')),
                    
                    Select::make('category_filter')
                        ->title('📁 카테고리')
                        ->empty('전체 카테고리')
                        ->options(Category::active()->pluck('name', 'id'))
                        ->value(request('category_filter')),
                ])->alignEnd(),
                
                Group::make([
                    Input::make('min_price')
                        ->type('number')
                        ->title('💰 최소 가격')
                        ->placeholder('0')
                        ->value(request('min_price')),
                    
                    Input::make('max_price')
                        ->type('number') 
                        ->title('💰 최대 가격')
                        ->placeholder('0')
                        ->value(request('max_price')),
                        
                    Select::make('stock_filter')
                        ->title('📦 재고 상태')
                        ->empty('전체')
                        ->options([
                            'in_stock' => '재고 있음',
                            'low_stock' => '재고 부족 (10개 이하)',
                            'out_of_stock' => '재고 없음'
                        ])
                        ->value(request('stock_filter')),
                ])->alignEnd(),
                
                Group::make([
                    Button::make('검색 적용')
                        ->icon('magnifier')
                        ->class('btn btn-primary')
                        ->method('applyFilters'),
                        
                    Link::make('초기화')
                        ->icon('refresh')
                        ->class('btn btn-outline-secondary')
                        ->route('platform.products'),
                ])->autoWidth()->alignEnd(),
            ])->title('🔍 상품 검색'),
            Layout::table('products', [
                TD::make('image_path', '이미지')
                    ->width('80px')
                    ->render(function (Product $product) {
                        if ($product->image_path) {
                            return '<img src="' . asset('storage/' . $product->image_path) . '" 
                                         alt="' . $product->name . '" 
                                         class="img-thumbnail" 
                                         style="width: 60px; height: 60px; object-fit: cover;">';
                        }
                        return '<div class="bg-light d-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px; border-radius: 4px;">
                                    <small class="text-muted">이미지<br>없음</small>
                                </div>';
                    }),

                TD::make('name', '상품명')
                    ->sort()
                    ->cantHide()
                    ->render(function (Product $product) {
                        return Link::make($product->name)
                            ->route('platform.products.edit', $product);
                    }),

                TD::make('categories', '카테고리')
                    ->render(function (Product $product) {
                        if ($product->categories->count() > 0) {
                            $badges = '';
                            foreach ($product->categories as $category) {
                                $badges .= '<span class="badge bg-info me-1">' . $category->name . '</span>';
                            }
                            return $badges;
                        }
                        return '<span class="badge bg-secondary">미분류</span>';
                    }),

                TD::make('price', '가격')
                    ->sort()
                    ->render(function (Product $product) {
                        return '₩' . number_format($product->price);
                    }),

                TD::make('stock_quantity', '재고')
                    ->sort()
                    ->render(function (Product $product) {
                        $class = $product->stock_quantity <= 5 ? 'text-danger' : 
                                ($product->stock_quantity <= 10 ? 'text-warning' : 'text-success');
                        
                        return '<span class="' . $class . '">' . $product->stock_quantity . '개</span>';
                    }),

                TD::make('order_items_count', '주문 건수')
                    ->sort()
                    ->render(function (Product $product) {
                        return $product->order_items_count . '건';
                    }),

                TD::make('is_active', '상태')
                    ->sort()
                    ->render(function (Product $product) {
                        return $product->is_active 
                            ? '<span class="badge bg-success">판매중</span>'
                            : '<span class="badge bg-secondary">판매중지</span>';
                    }),

                TD::make('created_at', '등록일')
                    ->sort()
                    ->render(function (Product $product) {
                        return $product->created_at->format('Y-m-d H:i');
                    }),

                TD::make('actions', '작업')
                    ->align(TD::ALIGN_CENTER)
                    ->width('160px')
                    ->render(function (Product $product) {
                        return view('orchid.partials.actions', [
                            'editUrl' => route('platform.products.edit', $product),
                            'deleteConfirm' => '정말 삭제하시겠습니까? 관련 주문 정보는 유지됩니다.',
                            'deleteAction' => 'remove',
                            'itemId' => $product->id
                        ])->render();
                    }),
            ])->title('상품 목록')
        ];
    }

    /**
     * 검색 적용
     */
    public function applyFilters(Request $request)
    {
        $filters = $request->only([
            'search', 'category_filter', 'min_price', 'max_price', 'stock_filter'
        ]);
        
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });
        
        return redirect()->route('platform.products', $filters);
    }

    /**
     * 상품 삭제
     *
     * @param Request $request
     * @return void
     */
    public function remove(Request $request): void
    {
        $product = Product::findOrFail($request->get('id'));
        
        if ($product->orderItems()->count() > 0) {
            $product->update(['is_active' => false]);
            Toast::warning('주문 내역이 있는 상품은 판매 중지됩니다. (완전 삭제 불가)');
            return;
        }

        $product->delete();
        Toast::info('상품이 삭제되었습니다.');
    }
}
