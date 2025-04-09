<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\WelcomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/producto/{product}', [WelcomeController::class, 'show'])->name('product.show');

// Ruta pública para visualizar un pedido mediante URL firmada
Route::get('/orders/{order}', function (Order $order) {
    $order->load(['orderItems.size', 'orderItems.product']);
    return view('orders.show', compact('order'));
})->name('orders.show');
