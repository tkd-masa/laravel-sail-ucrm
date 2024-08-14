<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\Item;
use App\Models\Order;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::groupBy('id')
            ->selectRaw('id, sum(subtotal) as total,
        customer_name, status, created_at')
            ->paginate(50);

        return Inertia::render('Purchases/Index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = Item::select('id', 'name', 'price')->where('is_selling', true)->get();

        return Inertia::render('Purchases/Create', [
            'items' => $items,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $purchase = Purchase::create([
                    'customer_id' => $request->customer_id,
                    'status' => $request->status,
                ]);

                foreach ($request->items as $item) {
                    $purchase->items()->attach($purchase->id, [
                        'item_id' => $item['id'],
                        'quantity' => $item['quantity'],
                    ]);
                }
            });

            return to_route('dashboard')->with([
                'message' => '登録しました。',
                'status' => 'success',
            ]);
        } catch (\Exception $e) {
            \Log::error($e);

            return to_route('dashboard')->with([
                'message' => '登録に失敗しました。',
                'status' => 'danger',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        // 小計
        $items = Order::where('id', $purchase->id)->get();

        // 合計
        $order = Order::groupBy('id')
            ->where('id', $purchase->id)
            ->selectRaw('id, sum(subtotal) as total,
            customer_name, status, created_at')
            ->get();

        return Inertia::render('Purchases/Show', [
            'items' => $items,
            'order' => $order,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase)
    {
        $purchase = Purchase::find($purchase->id);
        $allItems = Item::select('id', 'name', 'price')
            ->get();
        $items = [];

        foreach ($allItems as $allItem) {
            $quantity = 0;
            foreach ($purchase->items as $item) {
                if ($allItem->id === $item->id) {
                    $quantity = $item->pivot->quantity;
                }
            }
            array_push($items, [
                'id' => $allItem->id,
                'name' => $allItem->name,
                'price' => $allItem->price,
                'quantity' => $quantity,
            ]);
        }
        $order = Order::groupBy('id')
            ->where('id', $purchase->id)
            ->selectRaw('id, customer_id,
            customer_name, status, created_at')
            ->get();

        return Inertia::render('Purchases/Edit', [
            'items' => $items,
            'order' => $order,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        try {
            DB::transaction(function () use ($request, $purchase) {
                //中間テーブルを更新するにはsync()が便利
                $purchase->status = $request->status;
                $purchase->save();

                $items = [];
                foreach ($request->items as $item) {
                    $items = $items + [
                        $item['id'] => [
                            'quantity' => $item['quantity'],
                        ],
                    ];
                }
                $purchase->items()->sync($items);
            });

            return to_route('purchases.index')->with([
                'message' => '更新しました。',
                'status' => 'success',
            ]);
        } catch (\Exception $e) {
            \Log::error($e);

            return to_route('purchases.index')->with([
                'message' => '更新に失敗しました。',
                'status' => 'danger',
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        //
    }
}
