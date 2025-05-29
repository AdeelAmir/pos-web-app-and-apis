<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = null;
        $endDate = null;
        $dateFilter = '';
        $dateFilterStartDate = null;
        $dateFilterEndDate = null;
        if (isset($request->startDate, $request->endDate)) {
            $startDate = Carbon::parse($request->startDate)->format('Y-m-d');
            $endDate = Carbon::parse($request->endDate)->format('Y-m-d');

            $dateFilterStartDate = Carbon::parse($startDate)->format('m/d/Y');
            $dateFilterEndDate = Carbon::parse($endDate)->format('m/d/Y');

            $dateFilter = "{$dateFilterStartDate} - {$dateFilterEndDate}";
        }

        $products = Product::get();
        $sellers = User::where('role', 'seller')->get();
        $orders = Order::join('order_details', 'orders.id', 'order_details.order_id')
            ->where(function ($query) use ($startDate, $endDate) {
                if ($startDate != '' && $endDate != '') {
                    $query->whereBetween('orders.date', [$startDate, $endDate]);
                }
            })
            ->select(
                'orders.seller_id',
                'order_details.product_id',
                DB::raw('SUM(order_details.quantity) as quantity'),
                DB::raw('(SUM(order_details.quantity) * order_details.price) as price')
            )
            ->groupBy('orders.seller_id', 'order_details.product_id')
            ->get();

        $productMap = $products->keyBy('id');
        $sellerProducts = [];
        foreach ($orders as $order) {
            $sellerIndex = array_search($order->seller_id, array_column($sellerProducts, 'seller_id'));
            if ($sellerIndex === false) {
                $sellerProducts[] = [
                    'seller_id' => $order->seller_id,
                    'products' => []
                ];
                $sellerIndex = count($sellerProducts) - 1;
            }
            $sellerProducts[$sellerIndex]['products'][] = [
                'product_id' => $order->product_id,
                'price' => $order->price,
                'quantity' => $order->quantity
            ];
        }
        foreach ($sellerProducts as &$sellerProduct) {
            $existingProductIds = array_column($sellerProduct['products'], 'product_id');
            foreach ($productMap as $productId => $product) {
                if (!in_array($productId, $existingProductIds)) {
                    $sellerProduct['products'][] = [
                        'product_id' => $productId,
                        'price' => 0,
                        'quantity' => 0
                    ];
                }
            }
            usort($sellerProduct['products'], function ($a, $b) {
                return $a['product_id'] <=> $b['product_id'];
            });
        }
        // dd($products, $sellerProducts);

        return view('admin.reports.index', compact('products', 'sellers', 'sellerProducts', 'dateFilter', 'startDate', 'endDate'));
    }
}
