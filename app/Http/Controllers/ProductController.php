<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * 상품 목록 페이지 (메인 페이지)
     */
    public function index(Request $request): Response
    {
        try {
            $search = $request->input('search');
            $categoryId = $request->input('category_id');
            $minPrice = $request->input('min_price');
            $maxPrice = $request->input('max_price');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            $productsQuery = Product::with('categories')
                ->where('is_active', true)
                ->where('stock_quantity', '>', 0);

            if ($search) {
                $productsQuery->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($categoryId) {
                $productsQuery->whereHas('categories', function($query) use ($categoryId) {
                    $query->where('category_id', $categoryId);
                });
            }

            if ($minPrice && is_numeric($minPrice)) {
                $productsQuery->where('price', '>=', $minPrice);
            }
            if ($maxPrice && is_numeric($maxPrice)) {
                $productsQuery->where('price', '<=', $maxPrice);
            }

            if ($startDate) {
                $productsQuery->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $productsQuery->whereDate('created_at', '<=', $endDate);
            }

            $allowedSortFields = ['created_at', 'name', 'price', 'stock_quantity'];
            if (in_array($sortBy, $allowedSortFields)) {
                $productsQuery->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
            } else {
                $productsQuery->orderBy('created_at', 'desc');
            }

            $products = $productsQuery->paginate(12)
                ->withQueryString()
                ->through(function ($product) {
                    $firstCategory = $product->categories->first();
                    
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'description' => $product->description,
                        'price' => $product->price,
                        'stock_quantity' => $product->stock_quantity,
                        'image_path' => $product->image_path,
                        'image_url' => $product->image_url,
                        'category' => $firstCategory ? [
                            'id' => $firstCategory->id,
                            'name' => $firstCategory->name,
                        ] : null,
                        'categories' => $product->categories->map(function ($category) {
                            return [
                                'id' => $category->id,
                                'name' => $category->name,
                            ];
                        }),
                        'formatted_price' => '₩' . number_format($product->price),
                    ];
                });

            $categories = Category::where('is_active', true)
                ->withCount(['products' => function($query) {
                    $query->where('is_active', true)
                          ->where('stock_quantity', '>', 0);
                }])
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'products_count' => $category->products_count,
                    ];
                });

            $filters = [
                'search' => $search ?? '',
                'category_id' => $categoryId ?? '',
                'min_price' => $minPrice ?? '',
                'max_price' => $maxPrice ?? '',
                'start_date' => $startDate ?? '',
                'end_date' => $endDate ?? '',
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ];

            return Inertia::render('Products/Index', [
                'products' => $products,
                'categories' => $categories,
                'filters' => $filters,
            ]);
        } catch (\Exception $e) {
            \Log::error('ProductController index error: ' . $e->getMessage());
            
            return Inertia::render('Products/Index', [
                'products' => [
                    'data' => [],
                    'links' => []
                ],
                'categories' => [],
                'filters' => [
                    'search' => $request->input('search', ''),
                    'category_id' => $request->input('category_id', ''),
                    'min_price' => $request->input('min_price', ''),
                    'max_price' => $request->input('max_price', ''),
                    'start_date' => $request->input('start_date', ''),
                    'end_date' => $request->input('end_date', ''),
                    'sort_by' => $request->input('sort_by', 'created_at'),
                    'sort_order' => $request->input('sort_order', 'desc'),
                ]
            ]);
        }
    }

    public function show(Product $product): Response
    {
        if (!$product->is_active) {
            abort(404);
        }

        $product->load('categories');

        $categoryIds = $product->categories->pluck('id');
        $relatedProducts = collect();
        
        if ($categoryIds->isNotEmpty()) {
            $relatedProducts = Product::whereHas('categories', function ($query) use ($categoryIds) {
                    $query->whereIn('category_id', $categoryIds);
                })
                ->where('id', '!=', $product->id)
                ->where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->limit(4)
                ->get()
                ->map(function ($relatedProduct) {
                    return [
                        'id' => $relatedProduct->id,
                        'name' => $relatedProduct->name,
                        'price' => $relatedProduct->price,
                        'image_path' => $relatedProduct->image_path,
                        'image_url' => $relatedProduct->image_url,
                        'formatted_price' => '₩' . number_format($relatedProduct->price),
                    ];
                });
        }

        $firstCategory = $product->categories->first();

        return Inertia::render('Products/Show', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock_quantity' => $product->stock_quantity,
                'image_path' => $product->image_path,
                'image_url' => $product->image_url,
                'category' => $firstCategory ? [
                    'id' => $firstCategory->id,
                    'name' => $firstCategory->name,
                ] : null,
                'categories' => $product->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                    ];
                }),
                'formatted_price' => '₩' . number_format($product->price),
                'in_stock' => $product->stock_quantity > 0,
            ],
            'relatedProducts' => $relatedProducts,
        ]);
    }

    /**
     * AJAX 검색 API
     */
    public function search(Request $request)
    {
        $term = $request->input('q', '');
        
        if (empty($term)) {
            return response()->json([]);
        }

        $products = Product::with('categories')
            ->active()
            ->inStock()
            ->search($term)
            ->limit(10)
            ->get()
            ->map(function ($product) {
                        $firstCategory = $product->categories->first();
                
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'category_name' => $firstCategory ? $firstCategory->name : '카테고리 없음',
                    'category_names' => $product->categories->pluck('name')->join(', '),
                    'formatted_price' => '₩' . number_format($product->price),
                ];
            });

        return response()->json($products);
    }
}
