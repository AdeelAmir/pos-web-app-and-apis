<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\ReturnDetail;
use App\Models\Returns;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExchangeCityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.exchange-cities.index');
    }

    public function load(Request $request)
    {
        $limit = $request->post('length');
        $start = $request->post('start');
        // $searchTerm = $request->post('search')['value'];
        $searchTerm = $request['searchTerm'];

        $columnIndex = $request->post('order')[0]['column'];
        $columnName = $request->post('columns')[$columnIndex]['data'];
        $columnSortOrder = $request->post('order')[0]['dir'];

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;
        if ($searchTerm == '') {
            $fetch_data = Returns::join('users', 'users.id', 'returns.seller_id')
                ->select(
                    'returns.*',
                    'users.name as seller_name',
                    DB::raw('(SELECT COALESCE(SUM(return_details.return_quantity), 0) FROM return_details WHERE return_details.return_id = returns.id) as return_details_count')
                )
                ->whereNotNull('returns.to_city_id')
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
            $recordsFiltered = Returns::join('users', 'users.id', 'returns.seller_id')
                ->select(
                    'returns.*',
                    'users.name as seller_name',
                    DB::raw('(SELECT COALESCE(SUM(return_details.return_quantity), 0) FROM return_details WHERE return_details.return_id = returns.id) as return_details_count')
                )
                ->whereNotNull('returns.to_city_id')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = Returns::join('users', 'users.id', 'returns.seller_id')
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('returns.grand_total', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('users.name', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('users.id', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select(
                    'returns.*',
                    'users.name as seller_name',
                    DB::raw('(SELECT COALESCE(SUM(return_details.return_quantity), 0) FROM return_details WHERE return_details.return_id = returns.id) as return_details_count')
                )
                ->whereNotNull('returns.to_city_id')
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
            $recordsFiltered = Returns::join('users', 'users.id', 'returns.seller_id')
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('returns.grand_total', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('users.name', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('users.id', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select(
                    'returns.*',
                    'users.name as seller_name',
                    DB::raw('(SELECT COALESCE(SUM(return_details.return_quantity), 0) FROM return_details WHERE return_details.return_id = returns.id) as return_details_count')
                )
                ->whereNotNull('returns.to_city_id')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $Edit = route('exchange_city.edit', array($item->id));
            $sub_array = array();
            $sub_array['date'] = Carbon::parse($item->date)->format('d/m/Y');
            $sub_array['seller_id_name'] = $item->seller_name . '/' . $item->seller_id;
            $sub_array['total_items'] = $item->return_details_count;
            $sub_array['grand_total'] = SiteHelper::settings()['Currency_Icon'] . $item->grand_total;
            $sub_array['action'] = '
                <a href="' . $Edit . '" class="text-primary fs-6 me-1" data-toggle="tooltip" title="' . __('messages.btns.edit') . '">
                    <i class="far fa-edit"></i>
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
        $cities = City::where('type', 'Warehouse')->get();
        return view('admin.exchange-cities.add', compact('sellers', 'cities'));
    }

    function store(Request $request)
    {
        DB::beginTransaction();
        $affected = null;
        $returns = new Returns();
        if ($request->exchange_city_grand_total != 0) {
            $returns->date = Carbon::parse($request->date)->format('Y-m-d');
            $returns->seller_id = $request->seller_id;
            $returns->city_id = $request->from_city_id;
            $returns->to_city_id = $request->to_city_id;
            $returns->sale_id = $request->sale_id;
            $returns->grand_total = $request->exchange_city_grand_total;
            $affected = $returns->save();
        }

        if ($request->jsonProducts != '') {
            $products = json_decode($request->jsonProducts, true);
            foreach ($products as $product) {
                if ($product['quantity'] != 0) {
                    $returnDetails = new ReturnDetail();
                    $returnDetails->return_id = $returns->id;
                    $returnDetails->product_id = $product['product_id'];
                    $returnDetails->retail_price = $product['retail_price'];
                    $returnDetails->return_quantity = $product['quantity'];
                    $returnDetails->sub_total = $product['sub_total'];
                    $returnDetails->save();

                    $saleDetails = SaleDetail::where('sale_id', $request->sale_id)
                        ->where('product_id', $product['product_id'])
                        ->first();

                    $saleDetails->decrement('quantity', $product['quantity']);
                    $saleDetails->decrement('sub_total', $product['sub_total']);
                    $saleDetails->save();

                    Sale::whereId($request->sale_id)->decrement('grand_total', $product['sub_total']);
                }
            }
        }

        if ($affected) {
            DB::commit();
            return redirect()->route('exchange_city')->with('success-message', 'Exchange City added successfully');
        } else {
            DB::rollBack();
            return redirect()->route('exchange_city')->with('error-message', 'An unhandled error occurred');
        }
    }

    function edit($id)
    {
        $sellers = User::where('role', 'seller')->get();
        $cities = City::where('type', 'Warehouse')->get();
        $exchangeCity = Returns::whereId($id)->first();
        $exchangeCityItems = Returns::join('return_details', 'returns.id', 'return_details.return_id')
            ->join('products', 'products.id', 'return_details.product_id')
            ->select(
                'return_details.*',
                'products.name',
                'products.pieces_in_box',
                DB::raw('(SELECT COALESCE(sale_details.quantity, 0) FROM sale_details WHERE sale_details.sale_id = returns.sale_id AND sale_details.product_id = return_details.product_id) as total_stock'),
                DB::raw(
                    '( 
                    COALESCE(
                    (
                        SELECT SUM(B.quantity)
                        FROM sales A 
                        JOIN sale_details B ON A.id = B.sale_id
                        WHERE A.date = ?
                        AND B.product_id = products.id
                    ),
                    0
                    ) - COALESCE(
                        (
                            SELECT SUM(D.quantity)
                            FROM orders C
                            INNER JOIN order_details D ON C.id = D.order_id
                            WHERE D.product_id = products.id
                            AND C.seller_id = returns.seller_id
                            AND C.date = ?
                        ), 
                        0
                    )
                ) AS remaining_product'
                )
            )
            ->addBinding([Carbon::parse($exchangeCity['date'])->format('Y-m-d'), Carbon::parse($exchangeCity['date'])->format('Y-m-d')])
            ->where('returns.id', $id)
            ->get();

        // dd($exchangeCityItems);

        $array = [];
        foreach ($exchangeCityItems as $value) {
            $value->pieces = (!empty($value->remaining_product) && !empty($value->pieces_in_box)) ? SiteHelper::makeBoxes($value->remaining_product, $value->pieces_in_box) : 0;
            $array[] = [
                'sale_id' => $exchangeCity->sale_id,
                'product_id' => $value->product_id,
                'name' => $value->name,
                'id' => $value->id,
                'total_stock' => $value->total_stock,
                'retail_price' => $value->retail_price,
                'remaining_product' => $value->remaining_product,
                'pieces' => (!empty($value->remaining_product) && !empty($value->pieces_in_box)) ? SiteHelper::makeBoxes($value->remaining_product, $value->pieces_in_box) : 0,
                'sub_total' => $value->sub_total,
                'quantity' => $value->return_quantity
            ];
        }

        $jsonProducts = json_encode($array);

        return view('admin.exchange-cities.edit', compact('sellers', 'cities', 'exchangeCity', 'exchangeCityItems', 'jsonProducts'));
    }

    function update(Request $request)
    {
        $affected = null;

        $returns = Returns::whereId($request->id)->first();
        $returns->date = Carbon::parse($request->date)->format('Y-m-d');
        $returns->seller_id = $request->seller_id;
        $returns->city_id = $request->from_city_id;
        $returns->to_city_id = $request->to_city_id;
        $returns->sale_id = $request->sale_id;
        $returns->grand_total = $request->exchange_city_grand_total;
        $affected = $returns->save();

        if ($request->jsonProducts != '') {
            $returnDetails = ReturnDetail::where('return_id', $request->id)->get();

            $dataArray = [];
            foreach ($returnDetails as $item) {
                $subArray = [];
                $subArray['product_id'] = $item->product_id;
                $subArray['retail_price'] = $item->retail_price;
                $subArray['return_quantity'] = $item->return_quantity;
                $subArray['sub_total'] = $item->sub_total;
                $dataArray[] = $subArray;
            }

            ReturnDetail::where('return_id', $request->id)->delete();

            $products = json_decode($request->jsonProducts, true);
            foreach ($products as $product) {
                $returnDetails = new ReturnDetail();
                $returnDetails->return_id = $returns->id;
                $returnDetails->product_id = $product['product_id'];
                $returnDetails->retail_price = $product['retail_price'];
                $returnDetails->return_quantity = $product['quantity'];
                $returnDetails->sub_total = $product['sub_total'];
                $returnDetails->save();

                foreach ($dataArray as $data) {
                    if ($data['product_id'] == $product['product_id']) {
                        $newDecrementedQuantity = (int) $product['quantity'] - (int) $data['return_quantity'];
                        $newDecrementedSubTotal = (int) $product['sub_total'] - (int) $data['sub_total'];

                        $saleDetails = SaleDetail::where('sale_id', $request->sale_id)
                            ->where('product_id', $product['product_id'])
                            ->first();

                        $saleDetails->decrement('quantity', $newDecrementedQuantity);
                        $saleDetails->decrement('sub_total', $newDecrementedSubTotal);
                        $saleDetails->save();

                        Sale::whereId($request->sale_id)->decrement('grand_total', $newDecrementedSubTotal);
                    }
                }
            }
        }

        if ($affected) {
            return redirect()->route('exchange_city')->with('success-message', 'Exchange City updated successfully');
        } else {
            return redirect()->route('exchange_city')->with('error-message', 'An unhandled error occurred');
        }
    }

    public function delete(Request $request)
    {
        Returns::whereId($request->id)->delete();
        ReturnDetail::where('return_id', $request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function getSellerProducts(Request $request)
    {
        $details = Sale::join('sale_details', 'sale_details.sale_id', 'sales.id')
            ->join('products', 'products.id', '=', 'sale_details.product_id')
            ->where('sales.date', Carbon::parse($request->date)->format('Y-m-d'))
            ->where('sales.seller_id', $request->seller_id)
            ->where('sales.city_id', $request->city_id)
            ->where('sales.bonus', 0)
            ->select(
                'sales.id as sale_id',
                'products.id as product_id',
                'products.name',
                'products.pieces_in_box',
                'sale_details.id',
                'sale_details.quantity as total_stock',
                'sale_details.retail_price',
                DB::raw(
                    'COALESCE(
                        (
                            SELECT SUM(sd.quantity)
                            FROM sales s
                            JOIN sale_details sd ON s.id = sd.sale_id
                            WHERE s.date = "' . Carbon::parse($request->date)->format('Y-m-d') . '"
                            AND s.seller_id = ' . $request->seller_id . '
                            AND sd.product_id = products.id
                            AND s.bonus = 0
                        ), 0
                    ) - COALESCE(
                        (
                            SELECT SUM(od.quantity)
                            FROM orders o
                            JOIN order_details od ON o.id = od.order_id
                            WHERE o.date = "' . Carbon::parse($request->date)->format('Y-m-d') . '"
                            AND o.seller_id = ' . $request->seller_id . '
                            AND od.product_id = products.id
                            AND o.sale_type = "Stock"
                        ), 0
                    ) AS remaining_product'
                )
            )
            ->get();

        foreach ($details as $item) {
            $item->pieces = (!empty($item->remaining_product) && !empty($item->pieces_in_box)) ? SiteHelper::makeBoxes($item->remaining_product, $item->pieces_in_box) : 0;
        }

        return json_encode($details);
    }
}
