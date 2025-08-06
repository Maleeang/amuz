<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use App\Models\Category;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Upload;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductEditScreen extends Screen
{
    /**
     * @var Product
     */
    public $product;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Product $product = null): iterable
    {
        if (!$product) {
            $product = new Product();
        } else {
            if ($product->exists && $product->image_path) {
                $imagePath = $product->image_path;
                
                if (is_string($imagePath) && (str_starts_with($imagePath, '[') || str_contains($imagePath, 'undefined'))) {
                    try {
                        $decoded = json_decode($imagePath, true);
                        if (is_array($decoded)) {
                            $validPaths = array_filter($decoded, function($path) {
                                return $path && $path !== 'undefined' && $path !== '' && !is_null($path);
                            });
                            
                            if (!empty($validPaths)) {
                                $product->image_path = end($validPaths);
                            } else {
                                $product->image_path = null;
                            }
                            $product->save();
                        }
                    } catch (\Exception $e) {
                        $product->image_path = null;
                        $product->save();
                    }
                } elseif ($imagePath === 'undefined' || $imagePath === '') {
                    $product->image_path = null;
                    $product->save();
                }
            }
        }
        
        return [
            'product' => $product,
            'categories' => Category::active()->pluck('name', 'id'),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        $product = request()->route('product');
        return ($product && $product->exists) 
            ? '상품 편집: ' . $product->name
            : '새 상품 등록';
    }

    /**
     * The description displayed under the header.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        $product = request()->route('product');
        return ($product && $product->exists) 
            ? '상품 정보를 수정합니다.'
            : '새로운 상품을 등록합니다.';
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
                ->route('platform.products')
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
                Input::make('product.name')
                    ->title('상품명')
                    ->placeholder('예: iPhone 15 Pro, MacBook Pro 등')
                    ->required(),

                TextArea::make('product.description')
                    ->title('상품 설명')
                    ->placeholder('상품에 대한 자세한 설명을 입력하세요.')
                    ->rows(4),

            ])->title('기본 정보'),


            Layout::rows([
                \Orchid\Screen\Fields\Input::make('categories_display')
                    ->title('현재 선택된 카테고리')
                    ->value($this->getCurrentSelectedCategoriesText())
                    ->readonly()
                    ->help('아래 버튼들을 클릭하여 카테고리를 추가/제거하세요'),
                    
                \Orchid\Screen\Fields\Input::make('category_buttons_html')
                    ->type('hidden')
                    ->help(view('orchid.partials.category-buttons', [
                        'categories' => Category::active()->get(),
                        'selectedCategoryIds' => $this->getSelectedCategoryIds()
                    ])->render()),
            ])->title('카테고리 선택'),

            Layout::rows([
                Input::make('product.price')
                    ->title('가격 (원)')
                    ->type('number')
                    ->min(0)
                    ->step(100)
                    ->placeholder('0')
                    ->value(function($value) {
                        return $value ? (int) $value : null;
                    })
                    ->required(),

                Input::make('product.stock_quantity')
                    ->title('재고 수량')
                    ->type('number')
                    ->min(0)
                    ->required(),

                CheckBox::make('product.is_active')
                    ->title('판매 상태')
                    ->placeholder('체크하면 판매 가능한 상품으로 설정됩니다.')
                    ->value(true)
                    ->sendTrueOrFalse(),
            ])->title('판매 정보'),

            Layout::rows([
                ...(request()->route('product')?->image_path) ? [
                    \Orchid\Screen\Fields\Input::make('current_image_info')
                        ->type('hidden')
                        ->help('<div style="margin-bottom: 1rem;"><strong>현재 이미지:</strong><br><img src="' . request()->route('product')->image_url . '" alt="현재 이미지" style="max-width: 200px; max-height: 200px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-top: 0.5rem;"></div>')
                ] : [],
                
                Upload::make('new_image_upload')
                    ->title(
                        (request()->route('product') && request()->route('product')->exists) 
                            ? '새 이미지 업로드 (기존 이미지 교체)' 
                            : '상품 이미지 업로드'
                    )
                    ->maxFiles(1)
                    ->acceptedFiles('image/*')
                    ->value(null)
            ])->title('이미지'),

            ...(request()->route('product') && request()->route('product')->exists) ? [
                Layout::rows([
                    Input::make('info.created_at')
                        ->title('등록일')
                        ->value(request()->route('product')->created_at->format('Y-m-d H:i:s'))
                        ->readonly()
                        ->help('<style>input[readonly] { color: #000 !important; }</style>'),

                    Input::make('info.updated_at')
                        ->title('수정일')
                        ->value(request()->route('product')->updated_at->format('Y-m-d H:i:s'))
                        ->readonly()
                        ->help('<style>input[readonly] { color: #000 !important; }</style>'),

                    Input::make('info.order_count')
                        ->title('총 주문 건수')
                        ->value(request()->route('product')->orderItems()->count() . '건')
                        ->readonly()
                        ->help('<style>input[readonly] { color: #000 !important; }</style>'),

                    Input::make('info.total_sold')
                        ->title('총 판매 수량')
                        ->value(request()->route('product')->orderItems()->sum('quantity') . '개')
                        ->readonly()
                        ->help('<style>input[readonly] { color: #000 !important; }</style>'),
                ])->title('판매 통계')
            ] : [],
        ];
    }

    /**
     * 현재 선택된 카테고리 텍스트 가져오기
     *
     * @return string
     */
    private function getCurrentSelectedCategoriesText(): string
    {
        $product = null;
        if (request()->route('product')) {
            $product = request()->route('product');
            $product->load('categories');
        } else {
            $queryData = $this->query();
            $product = $queryData['product'] ?? null;
            if ($product) {
                $product->load('categories');
            }
        }
        
        if ($product && $product->exists && $product->categories->count() > 0) {
            return $product->categories->pluck('name')->join(', ');
        }
        
        return '선택된 카테고리가 없습니다';
    }

    /**
     * 선택된 카테고리 ID 목록 가져오기
     *
     * @return array
     */
    private function getSelectedCategoryIds(): array
    {
        $product = request()->route('product');
        
        if ($product && $product->exists) {
            return $product->categories->pluck('id')->toArray();
        }
        
        return [];
    }

    /**
     * 상품 저장
     *
     * @param Product $product
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Product $product, Request $request)
    {
        $request->validate([
            'product.name' => 'required|string|max:255',
            'product.description' => 'nullable|string|max:2000',
            'product.price' => 'required|numeric|min:0',
            'product.stock_quantity' => 'required|integer|min:0',
            'product.is_active' => 'boolean',
            'new_image_upload' => 'nullable|array',
        ]);

        $productData = $request->get('product');
        
        $categoryCheckboxes = $request->get('categories', []);
        
        $categories = [];
        foreach ($categoryCheckboxes as $categoryId => $isChecked) {
            if ($isChecked === true || $isChecked === '1') {
                $categories[] = $categoryId;
            }
        }
        
        if (empty($categories)) {
            return redirect()->back()
                ->withErrors(['categories' => '최소 하나의 카테고리를 선택해야 합니다.'])
                ->withInput();
        }

        $attachmentIds = $request->get('new_image_upload', []);
        $attachmentIds = $request->get('new_image_upload', []);
 
        if (is_array($attachmentIds)) {
            $validIds = array_filter($attachmentIds, function($id) {
                return $id !== null && $id !== 'undefined' && $id !== '' && is_numeric($id);
            });
            
            if (!empty($validIds)) {
                $attachmentId = end($validIds);
                $attachment = \Orchid\Attachment\Models\Attachment::find($attachmentId);
                
                if ($attachment) {
                    if ($product->exists && $product->image_path) {
                        $oldImagePath = $product->image_path;
                        
                        if (is_string($oldImagePath) && str_starts_with($oldImagePath, '[')) {
                            $oldImagePath = json_decode($oldImagePath, true);
                        }
                        
                        if (is_array($oldImagePath)) {
                            foreach ($oldImagePath as $path) {
                                if ($path && $path !== 'undefined' && \Storage::disk('public')->exists($path)) {
                                    \Storage::disk('public')->delete($path);
                                }
                            }
                        } elseif (is_string($oldImagePath) && $oldImagePath !== 'undefined') {
                            $oldPath = str_replace('/storage/', '', $oldImagePath);
                            if (\Storage::disk('public')->exists($oldPath)) {
                                \Storage::disk('public')->delete($oldPath);
                            }
                        }
                    }
                    
                    $fileName = $attachment->name . '.' . $attachment->extension;
                    $attachmentPath = $attachment->path;
                    
                    if (!str_starts_with($attachmentPath, '/')) {
                        $attachmentPath = '/' . $attachmentPath;
                    }
                    if (!str_ends_with($attachmentPath, '/')) {
                        $attachmentPath = $attachmentPath . '/';
                    }
                    
                    $sourcePath = storage_path('app/public' . $attachmentPath . $fileName);
                    $destinationPath = 'products/' . $fileName;
                    
                    if (file_exists($sourcePath)) {
                        \Storage::disk('public')->put($destinationPath, file_get_contents($sourcePath));
                        $productData['image_path'] = $destinationPath;
                    }
                }
            }
        }

        $product->fill($productData)->save();
        
        $product->categories()->sync($categories);

        if ($product->wasRecentlyCreated) {
            Toast::success('새 상품이 등록되었습니다.');
        } else {
            Toast::info('상품이 수정되었습니다.');
        }

        return redirect()->route('platform.products');
    }
}
