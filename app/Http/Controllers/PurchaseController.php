<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\Item;
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
        //
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        //
    }
}
