<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Order;
use App\Models\Product;
use App\Models\Returns;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        return view('admin.sales.index');
    }

    public function load(Request $request)
    {
        $limit = $request->post('length');
        $start = $request->post('start');
        $searchTerm = $request['searchTerm'];

        $columnIndex = $request->post('order')[0]['column']; // Column index
        $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;
        if ($searchTerm == '') {
            $fetch_data = Sale::join('users', 'users.id', 'sales.seller_id')
                ->select(
                    'sales.*',
                    'users.name as seller_name',
                    DB::raw('(SELECT SUM(sale_details.quantity) FROM sale_details WHERE sale_details.sale_id = sales.id) as total_quantity'),
                    DB::raw('(SELECT COALESCE(SUM(orders.grand_total), 0) FROM orders WHERE orders.seller_id = sales.seller_id AND orders.date = sales.date AND orders.payment_type = "Credit" AND orders.status = "Pending") as total_loan'),
                    DB::raw('(SELECT products.pieces_in_box FROM products INNER JOIN sale_details ON sale_details.product_id = products.id WHERE sale_details.sale_id = sales.id LIMIT 1) as pieces_in_box')
                )
                ->where('sales.type', 'Normal')
                ->whereNull('sales.deleted_at')
                ->orderBy($columnName, $columnSortOrder);
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
            $recordsFiltered = Sale::join('users', 'users.id', 'sales.seller_id')
                ->select(
                    'sales.*',
                    'users.name as seller_name',
                    DB::raw('(SELECT SUM(sale_details.quantity) FROM sale_details WHERE sale_details.sale_id = sales.id) as total_quantity'),
                    DB::raw('(SELECT COALESCE(SUM(orders.grand_total), 0) FROM orders WHERE orders.seller_id = sales.seller_id AND orders.date = sales.date AND orders.payment_type = "Credit" AND orders.status = "Pending") as total_loan'),
                    DB::raw('(SELECT products.pieces_in_box FROM products INNER JOIN sale_details ON sale_details.product_id = products.id WHERE sale_details.sale_id = sales.id LIMIT 1) as pieces_in_box')
                )
                ->where('sales.type', 'Normal')
                ->whereNull('sales.deleted_at')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = Sale::join('users', 'users.id', 'sales.seller_id')
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('sales.date', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('users.name', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('sales.grand_total', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('sales.status', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select(
                    'sales.*',
                    'users.name as seller_name',
                    DB::raw('(SELECT SUM(sale_details.quantity) FROM sale_details WHERE sale_details.sale_id = sales.id) as total_quantity'),
                    DB::raw('(SELECT COALESCE(SUM(orders.grand_total), 0) FROM orders WHERE orders.seller_id = sales.seller_id AND orders.date = sales.date AND orders.payment_type = "Credit" AND orders.status = "Pending") as total_loan'),
                    DB::raw('(SELECT products.pieces_in_box FROM products INNER JOIN sale_details ON sale_details.product_id = products.id WHERE sale_details.sale_id = sales.id LIMIT 1) as pieces_in_box')
                )
                ->where('sales.type', 'Normal')
                ->whereNull('sales.deleted_at')
                ->orderBy($columnName, $columnSortOrder);
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
            $recordsFiltered = Sale::join('users', 'users.id', 'sales.seller_id')
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('sales.date', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('users.name', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('sales.grand_total', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('sales.status', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select(
                    'sales.*',
                    'users.name as seller_name',
                    DB::raw('(SELECT SUM(sale_details.quantity) FROM sale_details WHERE sale_details.sale_id = sales.id) as total_quantity'),
                    DB::raw('(SELECT COALESCE(SUM(orders.grand_total), 0) FROM orders WHERE orders.seller_id = sales.seller_id AND orders.date = sales.date AND orders.payment_type = "Credit" AND orders.status = "Pending") as total_loan'),
                    DB::raw('(SELECT products.pieces_in_box FROM products INNER JOIN sale_details ON sale_details.product_id = products.id WHERE sale_details.sale_id = sales.id LIMIT 1) as pieces_in_box')
                )
                ->where('sales.type', 'Normal')
                ->whereNull('sales.deleted_at')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $View = route('sales.view', array($item->id));
            $Edit = route('sales.edit', array($item->id));
            $sub_array = array();
            $sub_array['date'] = Carbon::parse($item->date)->format('d/m/Y');
            $sub_array['seller_id_name'] = $item->seller_name . '/' . $item->seller_id;
            $sub_array['total_items'] = $item->total_quantity;
            $sub_array['total_total'] = SiteHelper::settings()['Currency_Icon'] . $item->grand_total;
            if ($item->status == 'Pending') {
                $sub_array['payment_status'] = '<span id="' . $item->status . '||' . $item->id . '" onclick="changeSaleStatus(this.id)" class="btn-sm btn-warning cursor-pointer">Pending</span>';
            } elseif ($item->status == 'Completed') {
                $sub_array['payment_status'] = '<span id="' . $item->status . '||' . $item->id . '" class="btn-sm btn-success cursor-pointer">Completed</span>';
            }
            $sub_array['cash'] = $item->bonus == 0 ? SiteHelper::settings()['Currency_Icon'] . $item->grand_total : 'Bonus';
            $sub_array['loan'] = SiteHelper::settings()['Currency_Icon'] . $item->total_loan;
            $sub_array['action'] = '
                <a href="' . $View . '" class="text-secondary fs-6 me-1" data-toggle="tooltip" title="' . __('messages.btns.view') . '">
                    <i class="far fa-eye"></i>
                </a>
                <a href="' . $Edit . '" class="text-primary fs-6 me-1" data-toggle="tooltip" title="' . __('messages.btns.edit') . '">
                    <i class="far fa-edit"></i>
                </a>
                <span id="delete||' . $item->id . '" onclick="DeleteSale(' . $item->id . ');" class="text-danger fs-6 cursor-pointer ml-1" data-toggle="tooltip" title="' . __('messages.btns.delete') . '">
                    <i class="far fa-trash-alt"></i>
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

    public function add()
    {
        $sellers = User::where('role', 'seller')->get();
        $cities = City::where('type', 'Warehouse')->get();
        return view('admin.sales.add', compact('sellers', 'cities'));
    }

    public function store(Request $request)
    {
        $affected = null;
        // $product_ids = [];

        $sale = new Sale();
        $sale->date = Carbon::parse($request->date)->format('Y-m-d');
        $sale->seller_id = $request->seller_id;
        $sale->city_id = $request->city_id;
        $sale->bonus = $request->bonus;
        $sale->grand_total = $request->grand_total;
        $sale->payment_type = $request->payment_type;
        if ($request->bonus == 1) {
            $sale->status = 'Completed';
        }
        $affected = $sale->save();

        if ($request->jsonProducts != '') {
            $products = json_decode($request->jsonProducts, true);
            foreach ($products as $item) {
                $saleDetails = new SaleDetail();
                $saleDetails->sale_id = $sale->id;
                $saleDetails->product_id = $item['id'];
                $saleDetails->retail_price = $item['price'];
                $saleDetails->wholesale_price = $item['wholesale_price'];
                $saleDetails->extra_price = $item['extra_price'];
                $saleDetails->quantity = $item['quantity'];
                $saleDetails->sub_total = $item['sub_total'];
                $saleDetails->save();
            }
        }

        if ($affected) {
            return redirect()->route('sales')->with('success-message', 'Sale added successfully');
        } else {
            return redirect()->route('sales')->with('error-message', 'An unhandled error occurred');
        }
    }

    public function edit($id)
    {
        $sellers = User::where('role', 'seller')->get();
        $cities = City::where('type', 'Warehouse')->get();
        $sale = Sale::whereId($id)->first();
        $saleDetails = SaleDetail::join('products', 'products.id', 'sale_details.product_id')
            ->select(
                'sale_details.*',
                'products.name',
                'products.pieces_in_box',
                DB::raw(
                    '(
                        SELECT 
                            COALESCE(SUM(stocks.stock), 0) - COALESCE(
                                (
                                    SELECT SUM(sale_details.quantity)
                                    FROM sale_details
                                    INNER JOIN sales ON sales.id = sale_details.sale_id
                                    WHERE sale_details.product_id = products.id
                                    AND sales.bonus = 0
                                ), 
                                0
                            ) - COALESCE(
                                (
                                    SELECT SUM(damage_replace_items.quantity)
                                    FROM damage_replace_items
                                    INNER JOIN damage_replaces ON damage_replaces.id = damage_replace_items.damage_id
                                    WHERE damage_replace_items.product_id = products.id
                                ),
                                0
                            )
                        FROM stocks
                        WHERE stocks.product_id = products.id
                    ) 
                    AS total_stock'
                )
            )
            ->where('sale_id', $id)
            ->get();

        $array = [];
        foreach ($saleDetails as $value) {
            $array[] = [
                'id' => $value->product_id,
                'name' => $value->name,
                'price' => $value->retail_price,
                'wholesale_price' => $value->wholesale_price,
                'extra_price' => $value->extra_price,
                'stock' => $value->total_stock,
                'pieces' => (!empty($value->total_stock) && !empty($value->pieces_in_box)) ? SiteHelper::makeBoxes($value->total_stock, $value->pieces_in_box) : 0,
                'quantity' => $value->quantity,
                'sub_total' => $value->sub_total
            ];
        }
        $jsonProducts = json_encode($array);

        return view('admin.sales.edit', compact('sale', 'saleDetails', 'sellers', 'cities', 'jsonProducts'));
    }

    public function update(Request $request)
    {
        $affected = null;

        $sale = Sale::whereId($request->id)->first();
        $sale->date = Carbon::parse($request->date)->format('Y-m-d');
        $sale->seller_id = $request->seller_id;
        $sale->city_id = $request->city_id;
        $sale->bonus = $request->bonus;
        $sale->grand_total = $request->grand_total;
        $sale->payment_type = $request->payment_type;
        if ($request->bonus == 1) {
            $sale->status = 'Completed';
        } elseif ($request->bonus == 0) {
            $sale->status = 'Pending';
        }
        $affected = $sale->save();

        if ($request->jsonProducts != '') {
            $saleDetails = SaleDetail::where('sale_id', $request->id)->delete();

            $products = json_decode($request->jsonProducts, true);
            foreach ($products as $item) {
                $saleDetails = new SaleDetail();
                $saleDetails->sale_id = $sale->id;
                $saleDetails->product_id = $item['id'];
                $saleDetails->retail_price = $item['price'];
                $saleDetails->wholesale_price = $item['wholesale_price'];
                $saleDetails->extra_price = $item['extra_price'];
                $saleDetails->quantity = $item['quantity'];
                $saleDetails->sub_total = $item['sub_total'];
                $saleDetails->save();
            }
        }

        if ($affected) {
            return redirect()->route('sales')->with('success-message', 'Sale updated successfully');
        } else {
            return redirect()->route('sales')->with('error-message', 'An unhandled error occurred');
        }
    }

    public function view($id)
    {
        $sale = Sale::whereId($id)->first();
        $seller = User::whereId($sale->seller_id)->first();
        $saleDetails = SaleDetail::join('products', 'products.id', 'sale_details.product_id')
            ->select(
                'sale_details.*',
                'products.name',
                'products.retail_price',
                'products.pieces_in_box',
            )
            ->where('sale_id', $id)
            ->get();
        return view('admin.sales.view', compact('sale', 'seller', 'saleDetails'));
    }

    public function statusUpdate(Request $request)
    {
        $sale = Sale::whereId($request['id'])->first();

        DB::beginTransaction();
        $affected = null;
        $sale->status = $request['status'];
        $affected = $sale->save();

        if ($affected) {
            DB::commit();
            return redirect()->route('sales')->with('success-message', 'Sale status updated successfully');
        } else {
            DB::rollBack();
            return redirect()->route('sales')->with('error-message', 'An unhandled error occurred');
        }
    }

    function delete(Request $request)
    {
        Sale::whereId($request->id)->delete();
        SaleDetail::where('sale_id', $request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function getAllProducts(Request $request)
    {
        $searchTerm = $request->searchTerm;

        $products = Product::where('city_id', $request->city_id)
            ->where(function ($query) use ($searchTerm) {
                $query->orWhere('name', 'LIKE', "%{$searchTerm}%");
            })
            ->select(
                'products.*',
                DB::raw(
                    '(
                        SELECT 
                            COALESCE(SUM(stocks.stock), 0) - COALESCE(
                                (
                                    SELECT SUM(sale_details.quantity)
                                    FROM sale_details
                                    INNER JOIN sales ON sales.id = sale_details.sale_id
                                    WHERE sale_details.product_id = products.id
                                    AND (sales.bonus = 0 OR (sales.bonus = 1 AND sales.status = "Completed"))
                                ), 
                                0
                            ) - COALESCE(
                                (
                                    SELECT SUM(damage_replace_items.quantity)
                                    FROM damage_replace_items
                                    INNER JOIN damage_replaces ON damage_replaces.id = damage_replace_items.damage_id
                                    WHERE damage_replace_items.product_id = products.id
                                ),
                                0
                            )
                        FROM stocks
                        WHERE stocks.product_id = products.id
                    ) 
                    AS total_stock'
                )
            )
            ->get()
            ->toArray();

        $exchangeCityProducts = Returns::join('return_details', 'return_details.return_id', 'returns.id')
            ->join('products', 'products.id', 'return_details.product_id')
            ->where('returns.to_city_id', $request->city_id)
            ->select(
                'products.*',
                DB::raw(
                    '(
                            COALESCE(
                                (
                                    SUM(return_details.return_quantity)
                                ), 
                                0
                            ) 
                            -
                            COALESCE(
                                (
                                    SELECT SUM(sale_details.quantity)
                                    FROM sales
                                    INNER JOIN sale_details ON sales.id = sale_details.sale_id
                                    WHERE sale_details.product_id = products.id
                                    AND sales.city_id = returns.to_city_id
                                    AND (sales.bonus = 0 OR (sales.bonus = 1 AND sales.status = "Completed"))
                                ), 
                                0
                            )
                        ) AS total_stock'
                )
            )
            ->groupBy('return_details.product_id')
            ->get()
            ->toArray();

        $mergedProducts = [];

        // Index exchangeCityProducts by product ID for easy lookup
        $exchangeCityProductsByName = [];
        foreach ($exchangeCityProducts as $product) {
            $exchangeCityProductsByName[$product['name']] = $product;
        }

        // Loop through $products
        foreach ($products as $product) {
            $productName = $product['name'];

            // If the product exists in $exchangeCityProducts, sum the total_stock
            if (isset($exchangeCityProductsByName[$productName])) {
                $product['total_stock'] += $exchangeCityProductsByName[$productName]['total_stock'];
                unset($exchangeCityProductsByName[$productName]); // Remove it after merging
            }

            $product['boxes_pieces'] = (!empty($product['total_stock']) && !empty($product['pieces_in_box'])) ? SiteHelper::makeBoxes($product['total_stock'], $product['pieces_in_box']) : 0;

            // Add the product to the merged list
            $mergedProducts[] = $product;
        }

        // Add any remaining exchangeCityProducts that weren't in $products
        foreach ($exchangeCityProductsByName as $remainingProduct) {
            $mergedProducts[] = $remainingProduct;
        }

        return json_encode($mergedProducts);
    }
}
