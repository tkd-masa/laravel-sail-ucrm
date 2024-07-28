<?php

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/searchCustomers', function (Request $request) {
    return Customer::searchCustomers($request->search)
        ->select('id', 'name', 'kana', 'tel')->paginate();
})->middleware('auth:sanctum');
