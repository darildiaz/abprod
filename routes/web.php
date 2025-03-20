<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

Route::get('/', function () {
    return view('welcome');
});

// Ruta pública para visualizar un pedido mediante URL firmada
Route::get('/pedido-publico/{orderId}', function (string $orderId) {
    try {
        // Cargar el pedido
        $order = Order::findOrFail($orderId);
        
        // Preparar QR codes
        foreach ($order->orderItems as $item) {
            $item->type = \Illuminate\Support\Facades\DB::table('order_references')
                ->join('products', 'order_references.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->join('sizes', 'order_references.size_id', '=', 'sizes.id')
                ->select(\Illuminate\Support\Facades\DB::raw('CONCAT(categories.name, " (", sizes.name, ")") as category_size'))
                ->where('order_references.order_id', $order->id)
                ->where('order_references.item', $item->item)
                ->pluck('category_size')
                ->implode(' + ');
        }
        
        foreach ($order->orderMolds as $model) {
            $qrCode = QrCode::size(100)->generate(asset('storage/' . $model->imagen ?? 'N/A'));
            $qrCodeBase64 = base64_encode($qrCode);
            $model->qr = 'data:image/svg+xml;base64,' . $qrCodeBase64;
        }
        
        // Mostrar la vista
        return view('public.order-view', ['order' => $order]);
        
    } catch (\Exception $e) {
        abort(404, 'El pedido solicitado no existe o no está disponible');
    }
})->name('orden.publica')->middleware('signed');
