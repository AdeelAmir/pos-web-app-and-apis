<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\PartialPayment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.loan.index');
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
                ->where('orders.status', 'Pending')
                ->whereNull('orders.deleted_at')
                ->select(
                    'orders.*',
                    'sellers.name as seller_name',
                    'shops.name as shop_name',
                    DB::raw(
                        '(
                        SELECT 
                        COALESCE(SUM(partial_payments.amount),0)
                        FROM partial_payments
                        WHERE partial_payments.order_id = orders.id
                        )
                        AS partial'
                    )
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
            $recordsFiltered = Order::join('users', 'users.id', 'orders.seller_id')
                ->join('shops', 'shops.id', 'orders.shop_id')
                ->where('orders.status', 'Pending')
                ->whereNull('orders.deleted_at')
                ->select(
                    'orders.*',
                    'sellers.name as seller_name',
                    'shops.name as shop_name',
                    DB::raw(
                        '(
                        SELECT 
                        COALESCE(SUM(partial_payments.amount),0)
                        FROM partial_payments
                        WHERE partial_payments.order_id = orders.id
                        )
                        AS partial'
                    )
                )
                ->orderBy('orders.id', 'asc')
                ->count();
        } else {
            $fetch_data = Order::join('users', 'users.id', 'orders.seller_id')
                ->join('shops', 'shops.id', 'orders.shop_id')
                ->where(function ($query) {
                    $query->where([
                        ['orders.deleted_at', '=', null]
                    ]);
                })
                ->where('orders.status', 'Pending')
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('products.name', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('products.retail_price', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('products.wholesale_price', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select(
                    'orders.*',
                    'sellers.name as seller_name',
                    'shops.name as shop_name',
                    DB::raw(
                        '(
                        SELECT 
                        COALESCE(SUM(partial_payments.amount),0)
                        FROM partial_payments
                        WHERE partial_payments.order_id = orders.id
                        )
                        AS partial'
                    )
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
            $recordsFiltered = Order::join('users', 'users.id', 'orders.seller_id')
                ->join('shops', 'shops.id', 'orders.shop_id')
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('products.name', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('products.retail_price', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('products.wholesale_price', 'LIKE', '%' . $searchTerm . '%');
                })
                ->where('orders.status', 'Pending')
                ->select(
                    'orders.*',
                    'sellers.name as seller_name',
                    'shops.name as shop_name',
                    DB::raw(
                        '(
                        SELECT 
                        COALESCE(SUM(partial_payments.amount),0)
                        FROM partial_payments
                        WHERE partial_payments.order_id = orders.id
                        )
                        AS partial'
                    )
                )
                ->orderBy('orders.id', 'asc')
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $View = route('loan.view', array($item->id));
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['date'] = Carbon::parse($item->date)->format('d-m-Y');
            $sub_array['seller_name_id'] = $item->seller_name . '/' . $item->seller_id;
            $sub_array['shop_name_id'] = $item->shop_name;
            if ($item->status == 'Pending') {
                $sub_array['status'] = '<span id="' . $item->status . '||' . $item->id . '" onclick="(this.id)" class="btn-sm btn-warning cursor-pointer">Pending</span>';
            } else if ($item->status == 'Completed') {
                $sub_array['status'] = '<span id="' . $item->status . '||' . $item->id . '" onclick="(this.id)" class="btn-sm btn-success cursor-pointer">Completed</span>';
            }
            if ($item->sale_type == 'Bonus') {
                $sub_array['sale_type'] = '<span id="' . $item->sale_type . '||' . $item->id . '" class="btn-sm btn-info cursor-pointer">Bonus</span>';
            } elseif ($item->sale_type == 'Stock') {
                $sub_array['sale_type'] = '<span id="' . $item->sale_type . '||' . $item->id . '" class="btn-sm btn-success cursor-pointer">Stock</span>';
            }
            $sub_array['cash'] = SiteHelper::settings()['Currency_Icon'] . $item->partial;
            $sub_array['credit'] = SiteHelper::settings()['Currency_Icon'] . ((int) $item->grand_total - (int) $item->partial);
            // $sub_array['action'] = '
            //     <a href="' . $View . '" class="text-secondary cursor-pointer fs-6 me-1" data-toggle="tooltip" title="' . __('messages.btns.view') . '">
            //         <i class="fa fa-eye"></i>
            //     </a>
            //     <span id="delete||' . $item->id . '" onclick="DeleteOrder(' . $item->id . ');" class="text-danger fs-6 cursor-pointer ml-1" data-toggle="tooltip" title="' . __('messages.btns.delete') . '">
            //         <i class="fas fa-trash-alt"></i>
            //     </span>';
            $sub_array['action'] = '
                <a href="' . $View . '" class="text-secondary cursor-pointer fs-6 me-1" data-toggle="tooltip" title="' . __('messages.btns.view') . '">
                    <i class="fa fa-eye"></i>
                </a>';
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

    public function add()
    {
        $sellers = User::where('role', 'seller')->get();
        $cities = City::get();
        $products = Product::get();
        return view('admin.exchange-cities.add', compact('sellers', 'cities', 'products'));
    }

    function store(Request $request)
    {
        $Image = "";
        if ($request->has('image')) {
            $Image = 'product-image_' . Carbon::now()->format('Ymd-His') . '.' . $request->file('image')->extension();
            $request->file('image')->storeAs('public/products/', $Image);
        }

        $Affected = null;

        $product = new Product();
        $product->category_id = $request->category_id;
        $product->city_id = $request->city_id;
        $product->name = $request->name;
        $product->image = $Image;
        $product->pieces = $request->pieces;
        $product->box = $request->box;
        $product->stock = $request->stock;
        $product->retail_price = $request->retail_price;
        $product->wholesale_price = $request->wholesale_price;
        $product->extra_price = $request->extra_price;
        $product->description = $request->description;
        $Affected = $product->save();

        if ($Affected) {
            return redirect()->route('damage_replace')->with('success-message', 'Product added successfully');
        } else {
            return redirect()->route('damage_replace')->with('error-message', 'An unhandled error occurred');
        }
    }

    function edit($id)
    {
        $sellers = User::where('role', 'seller')->get();
        $cities = City::get();
        $products = Product::get();
        return view('admin.exchange-cities.edit', compact('sellers', 'cities', 'products'));
    }

    function update(Request $request)
    {
        $Image = $request['oldImage'];
        if ($request->hasFile('image')) {
            if ($request['oldImage'] != '') {
                $explodedOldImage = explode('/', $request->oldImage);
                $oldImage = end($explodedOldImage);
                $path = public_path('storage/products') . '/' . $oldImage;
                // Unlink the old file if it exists
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $Image = 'product-image_' . Carbon::now()->format('Ymd-His') . '.' . $request->file('image')->extension();
            $request->file('image')->storeAs('public/products/', $Image);
        } else {
            $explodedOldImage = explode('/', $request->oldImage);
            $Image = end($explodedOldImage);
        }

        $Affected = null;

        $product = Product::whereId($request->product_id)->first();
        $product->category_id = $request->category_id;
        $product->city_id = $request->city_id;
        $product->name = $request->name;
        $product->image = $Image;
        $product->pieces = $request->pieces;
        $product->box = $request->box;
        $product->stock = $request->stock;
        $product->retail_price = $request->retail_price;
        $product->wholesale_price = $request->wholesale_price;
        $product->extra_price = $request->extra_price;
        $product->description = $request->description;
        $Affected = $product->save();

        if ($Affected) {
            return redirect()->route('damage_replace')->with('success-message', 'Product updated successfully');
        } else {
            return redirect()->route('damage_replace')->with('error-message', 'An unhandled error occurred');
        }
    }

    function delete(Request $request)
    {
        $product = Product::whereId($request->id)->first();
        if (!empty($product->image)) {
            $explodedImage = explode('/', $product->image);
            $image = end($explodedImage);
            $path = public_path('storage/products') . '/' . $image;
            // Unlink the old file if it exists
            if (file_exists($path)) {
                unlink($path);
            }
        }
        Product::whereId($request->id)->delete();
        return response()->json(['success' => true]);
    }

    function view($id)
    {
        $loan = Order::join('users AS sellers', 'sellers.id', 'orders.seller_id')
            ->join('shops', 'shops.id', 'orders.shop_id')
            ->where('orders.status', 'Pending')
            ->where('orders.id', $id)
            ->select(
                'orders.*',
                'sellers.name as seller_name',
                'sellers.email',
                'sellers.phone',
                'sellers.profile_image',
                'shops.name as shop_name',
                'shops.shop_id as shops_id',
                'shops.location',
                'shops.address',
            )->first();

        if ($loan->price_type == 'retail_price') {
            $loan->price_type = 'Retail Price';
        } elseif ($loan->price_type == 'wholesale_price') {
            $loan->price_type = 'WholeSale Price';
        } elseif ($loan->price_type == 'extra_price') {
            $loan->price_type = 'Extra Price';
        }

        $loan->profile_image = asset('public/storage/users') . '.' . $loan->profile_image;

        $loanDetails = OrderDetail::with('product')->where('order_details.order_id', $loan->id)->get();
        $partialPayments = PartialPayment::where('order_id', $loan->id)->get();

        return view('admin.loan.view', compact('loan', 'loanDetails', 'partialPayments'));
    }
}
