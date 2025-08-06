<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 사용자 주문 목록
     */
    public function index(Request $request): Response
    {
        $query = Auth::user()->orders()
            ->with(['orderItems.product.categories'])
            ->latest();

        if ($request->filled('status')) {
            $query->byStatus($request->input('status'));
        }

        if ($request->filled('start_date')) {
            $query->byDateRange($request->input('start_date'), $request->input('end_date'));
        }

        if ($request->filled('category_id')) {
            $query->whereHas('orderItems.product.categories', function($query) use ($request) {
                $query->where('categories.id', $request->input('category_id'));
            });
        }

        $orders = $query->paginate(10)
            ->withQueryString()
            ->through(function ($order) {
                return [
                    'id' => $order->id,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'status_label' => $order->status_label,
                    'ordered_at' => $order->ordered_at->format('Y-m-d H:i'),
                    'notes' => $order->notes,
                    'items_count' => $order->orderItems->count(),
                    'formatted_amount' => '₩ ' . number_format($order->total_amount),
                    'items' => $order->orderItems->map(function ($item) {
                        return [
                            'product_name' => $item->product->name,
                            'product_description' => $item->product->description,
                            'category_name' => $item->product->first_category->name ?? '카테고리 없음',
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'total_price' => $item->total_price,
                            'formatted_price' => '₩ ' . number_format($item->price),
                            'formatted_total' => '₩ ' . number_format($item->total_price),
                            'stock' => $item->product->stock,
                        ];
                    }),
                    'can_cancel' => $order->status === Order::STATUS_PENDING,
                ];
            });

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
            'filters' => [
                'status' => $request->input('status', ''),
                'start_date' => $request->input('start_date', ''),
                'end_date' => $request->input('end_date', ''),
                'category_id' => $request->input('category_id', ''),
            ],
            'statuses' => [
                ['value' => '', 'label' => '전체'],
                ['value' => Order::STATUS_PENDING, 'label' => '대기중'],
                ['value' => Order::STATUS_PAID, 'label' => '결제완료'],
                ['value' => Order::STATUS_SHIPPED, 'label' => '배송중'],
                ['value' => Order::STATUS_DELIVERED, 'label' => '배송완료'],
                ['value' => Order::STATUS_CANCELLED, 'label' => '취소됨'],
            ],
            'categories' => Category::active()->orderBy('name')->get()->map(function($category) {
                return [
                    'value' => $category->id,
                    'label' => $category->name
                ];
            }),
        ]);
    }

    /**
     * 주문 생성 폼 표시
     */
    public function create()
    {
        return Inertia::render('Orders/Create');
    }

    /**
     * 주문 생성
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1|max:99',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($validated['items'] as $itemData) {
                $product = Product::lockForUpdate()->find($itemData['product_id']);

                if (!$product) {
                    throw new \Exception("상품을 찾을 수 없습니다.");
                }

                if (!$product->is_active) {
                    throw new \Exception("{$product->name}은(는) 현재 판매중인 상품이 아닙니다.");
                }

                if (!$product->hasStock($itemData['quantity'])) {
                    throw new \Exception("{$product->name}의 재고가 부족합니다. (현재 재고: {$product->stock_quantity}개)");
                }

                $itemTotal = $product->price * $itemData['quantity'];
                $totalAmount += $itemTotal;

                $orderItemsData[] = [
                    'product' => $product,
                    'quantity' => $itemData['quantity'],
                    'price' => $product->price,
                    'total_price' => $itemTotal,
                ];
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $totalAmount,
                'status' => Order::STATUS_PENDING,
                'ordered_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($orderItemsData as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product']->id,
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'total_price' => $itemData['total_price'],
                ]);

                $itemData['product']->decreaseStock($itemData['quantity']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '주문이 성공적으로 생성되었습니다.',
                'order_id' => $order->id,
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * 주문 상세보기
     */
    public function show(Order $order): Response
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['orderItems.product.categories']);

        return Inertia::render('Orders/Show', [
            'order' => [
                'id' => $order->id,
                'total_amount' => $order->total_amount,
                'status' => $order->status,
                'status_label' => $order->status_label,
                'ordered_at' => $order->ordered_at->format('Y-m-d H:i:s'),
                'notes' => $order->notes,
                'formatted_amount' => '₩ ' . number_format($order->total_amount),
                'can_cancel' => $order->status === Order::STATUS_PENDING,
                'items' => $order->orderItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product' => [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'image_path' => $item->product->image_path,
                            'image_url' => $item->product->image_url,
                            'category_name' => $item->product->first_category->name ?? '카테고리 없음',
                        ],
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'total_price' => $item->total_price,
                        'formatted_price' => '₩ ' . number_format($item->price),
                        'formatted_total' => '₩ ' . number_format($item->total_price),
                    ];
                }),
            ],
        ]);
    }

    /**
     * 주문 취소
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== Order::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'message' => '대기중인 주문만 취소할 수 있습니다.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            foreach ($order->orderItems as $item) {
                $item->product->restoreStock($item->quantity);
            }

            $order->update(['status' => Order::STATUS_CANCELLED]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '주문이 취소되었습니다.',
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => '주문 취소 중 오류가 발생했습니다.',
            ], 500);
        }
    }
}