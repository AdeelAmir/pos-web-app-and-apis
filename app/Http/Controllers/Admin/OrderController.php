<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.orders.index');
    }

    public function load(Request $request)
    {
        $limit = $request->post('length');
        $start = $request->post('start');
        // $searchTerm = $request->post('search')['value'];
        $searchTerm = $request['searchTerm'];

        $columnIndex = $request->post('order')[0]['column']; // Column index
        $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;
        if ($searchTerm == '') {
            $fetch_data = Order::join('users AS sellers', 'sellers.id', 'orders.seller_id')
                ->join('shops', 'shops.id', 'orders.shop_id')
                ->where('orders.status', 'Completed')
                ->whereNull('orders.deleted_at')
                ->select(
                    'orders.*',
                    'sellers.name as seller_name',
                    'shops.name as shop_name',
                )
                ->orderBy('orders.id', 'asc');
            if ($limit == -1) {
                $fetch_data = $fetch_data
                    ->get();
            } else {
                $fetch_data = $fetch_data
                    ->offset($start)
                    ->limit($limit)
                    ->get();
            }
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = Order::join('users AS sellers', 'sellers.id', 'orders.seller_id')
                ->join('shops', 'shops.id', 'orders.shop_id')
                ->where('orders.status', 'Completed')
                ->whereNull('orders.deleted_at')
                ->select(
                    'orders.*',
                    'sellers.name as seller_name',
                    'shops.name as shop_name'
                )
                ->orderBy('orders.id', 'asc')
                ->count();
        } else {
            $fetch_data = Order::join('users AS sellers', 'sellers.id', 'orders.seller_id')
                ->join('shops', 'shops.id', 'orders.shop_id')
                ->where(function ($query) {
                    $query->where([
                        ['orders.deleted_at', '=', null]
                    ]);
                })
                ->where('orders.status', 'Completed')
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('sellers.name', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('shops.name', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select(
                    'orders.*',
                    'sellers.name as seller_name',
                    'shops.name as shop_name'
                )
                ->orderBy('orders.id', 'asc');
            if ($limit == -1) {
                $fetch_data = $fetch_data
                    ->get();
            } else {
                $fetch_data = $fetch_data
                    ->offset($start)
                    ->limit($limit)
                    ->get();
            }
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = Order::join('users AS sellers', 'sellers.id', 'orders.seller_id')
                ->join('shops', 'shops.id', 'orders.shop_id')
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('sellers.name', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('shops.name', 'LIKE', '%' . $searchTerm . '%');
                })
                ->where('orders.status', 'Completed')
                ->select(
                    'orders.*',
                    'sellers.name as seller_name',
                    'shops.name as shop_name'
                )
                ->orderBy('orders.id', 'asc')
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $View = route('orders.view', array($item->id));
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['date'] = Carbon::parse($item->date)->format('d-m-Y');
            $sub_array['seller_name_id'] = $item->seller_name . '/' . $item->seller_id;
            $sub_array['shop_name_id'] = $item->shop_name . '/' . $item->shop_id;
            $sub_array['price_type'] = $item->price_type == 'wholesale_price' ? 'Wholesale' : ($item->price_type == 'extra_price' ? 'Extra' : 'Retail');
            if ($item->sale_type == 'Bonus') {
                $sub_array['sale_type'] = '<span id="' . $item->sale_type . '||' . $item->id . '" class="btn-sm btn-info cursor-pointer">Bonus</span>';
            } elseif ($item->sale_type == 'Stock') {
                $sub_array['sale_type'] = '<span id="' . $item->sale_type . '||' . $item->id . '" class="btn-sm btn-success cursor-pointer">Stock</span>';
            }
            $sub_array['grand_total'] = SiteHelper::settings()['Currency_Icon'] . $item->grand_total;
            $sub_array['action'] = '
                <a href="' . $View . '" class="text-secondary cursor-pointer fs-6 me-1" data-toggle="tooltip" title="' . __('messages.btns.view') . '">
                    <i class="fa fa-eye"></i>
                </a>
                <span id="delete||' . $item->id . '" onclick="DeleteOrder(' . $item->id . ');" class="text-danger fs-6 cursor-pointer ml-1" data-toggle="tooltip" title="' . __('messages.btns.delete') . '">
                    <i class="fas fa-trash-alt"></i>
                </span>';
            $SrNo++;
            $data[] = $sub_array;
        }

        $json_data = array(
            "draw" => intval($request->post('draw')),
            "iTotalRecords" => $recordsTotal,
            "iTotalDisplayRecords" => $recordsFiltered,
            "aaData" => $data
        );

        echo json_encode($json_data);
    }

    // public function add()
    // {
    //     $sellers = User::where('role', 'seller')->get();
    //     $shops = Shop::get();
    //     return view('admin.orders.add', compact('sellers', 'shops'));
    // }

    // public function store(Request $request)
    // {
    //     $Image = "";
    //     if ($request->has('image')) {
    //         $Image = 'product-image_' . Carbon::now()->format('Ymd-His') . '.' . $request->file('image')->extension();
    //         $request->file('image')->storeAs('public/products/', $Image);
    //     }

    //     $Affected = null;

    //     $product = new Product();
    //     $product->category_id = $request->category_id;
    //     $product->city_id = $request->city_id;
    //     $product->name = $request->name;
    //     $product->image = $Image;
    //     $product->retail_price = $request->retail_price;
    //     $product->wholesale_price = $request->wholesale_price;
    //     $product->extra_price = $request->extra_price;
    //     $product->description = $request->description;
    //     $Affected = $product->save();

    //     if ($Affected) {
    //         return redirect()->route('products')->with('success-message', 'Product added successfully');
    //     } else {
    //         return redirect()->route('products')->with('error-message', 'An unhandled error occurred');
    //     }
    // }

    // public function edit($id)
    // {
    //     $order = Order::whereId($id)->first();
    //     $sellers = User::where('role', 'seller')->get();
    //     $shops = Shop::get();
    //     return view('admin.orders.edit', compact('order', 'sellers', 'shops'));
    // }

    // public function update(Request $request)
    // {
    //     $Affected = null;

    //     $product = Product::whereId($request->product_id)->first();
    //     $product->category_id = $request->category_id;
    //     $product->city_id = $request->city_id;
    //     $product->name = $request->name;
    //     $product->image = $Image;
    //     // $product->pieces = $request->pieces;
    //     // $product->box = $request->box;
    //     // $product->stock = $request->stock;
    //     $product->retail_price = $request->retail_price;
    //     $product->wholesale_price = $request->wholesale_price;
    //     $product->extra_price = $request->extra_price;
    //     $product->description = $request->description;
    //     $Affected = $product->save();

    //     if ($Affected) {
    //         return redirect()->route('products')->with('success-message', 'Product updated successfully');
    //     } else {
    //         return redirect()->route('products')->with('error-message', 'An unhandled error occurred');
    //     }
    // }

    public function view($id)
    {
        $order = Order::whereId($id)->first();
        $orderDetails = OrderDetail::join('products', 'products.id', 'order_details.product_id')
            ->select('order_details.*', 'products.name as product_name', 'products.pieces_in_box')
            ->where('order_details.order_id', $id)
            ->get();
        $seller = User::whereId($order->seller_id)->where('role', 'seller')->first();
        $shop = Shop::whereId($order->shop_id)->first();
        return view('admin.orders.view', compact('order', 'orderDetails', 'seller', 'shop'));
    }

    public function delete(Request $request)
    {
        Order::whereId($request->id)->delete();
        OrderDetail::where('order_id', $request->id)->delete();
        return response()->json(['success' => true]);
    }
}
