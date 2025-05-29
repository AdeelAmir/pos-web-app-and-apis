<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\PartialPayment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfficeLoanController extends Controller
{
    public function index()
    {
        return view('admin.office-loan.index');
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
                    DB::raw(
                        '(
                        SELECT 
                        COALESCE(SUM(partial_payments.amount),0)
                        FROM partial_payments
                        WHERE partial_payments.office_sale_id = sales.id
                        )
                        AS partial'
                    )
                )
                ->where('sales.type', 'Office')
                ->where('sales.loan', 1)
                ->whereNull('sales.deleted_at')
                ->orderBy($columnName, $columnSortOrder);
            if ($limit == -1) {
                $fetch_data = $fetch_data->get();
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
                    DB::raw(
                        '(
                        SELECT 
                        COALESCE(SUM(partial_payments.amount),0)
                        FROM partial_payments
                        WHERE partial_payments.office_sale_id = sales.id
                        )
                        AS partial'
                    )
                )
                ->where('sales.type', 'Office')
                ->where('sales.loan', 1)
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
                    DB::raw(
                        '(
                        SELECT 
                        COALESCE(SUM(partial_payments.amount),0)
                        FROM partial_payments
                        WHERE partial_payments.office_sale_id = sales.id
                        )
                        AS partial'
                    )
                )
                ->where('sales.type', 'Office')
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
                    DB::raw(
                        '(
                        SELECT 
                        COALESCE(SUM(partial_payments.amount),0)
                        FROM partial_payments
                        WHERE partial_payments.office_sale_id = sales.id
                        )
                        AS partial'
                    )
                )
                ->where('sales.loan', 1)
                ->whereNull('sales.deleted_at')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $View = route('loan.office.view', array($item->id));
            $sub_array = array();
            $sub_array['date'] = Carbon::parse($item->date)->format('d/m/Y');
            $sub_array['seller_id_name'] = $item->seller_name . '/' . $item->seller_id;
            $sub_array['total_items'] = $item->total_quantity;
            $sub_array['total_total'] = SiteHelper::settings()['Currency_Icon'] . $item->grand_total;
            if ($item->status == 'Pending') {
                $sub_array['payment_status'] = '<span id="' . $item->status . '||' . $item->id . '" onclick="changeOfficeLoanStatus(this.id)" class="btn-sm btn-warning cursor-pointer">Pending</span>';
            } elseif ($item->status == 'Completed') {
                $sub_array['payment_status'] = '<span id="' . $item->status . '||' . $item->id . '" class="btn-sm btn-success cursor-pointer">Completed</span>';
            }
            $sub_array['cash'] = SiteHelper::settings()['Currency_Icon'] . $item->partial;
            $sub_array['loan'] = ($item->office_payment_type == 'Credit' && $item->status == 'Pending') ? SiteHelper::settings()['Currency_Icon'] . ((int)$item->grand_total - (int)$item->partial) : SiteHelper::settings()['Currency_Icon'] . 0;
            $sub_array['action'] = '
                <a href="' . $View . '" class="text-secondary fs-6 me-1" data-toggle="tooltip" title="' . __('messages.btns.view') . '">
                    <i class="far fa-eye"></i>
                </a>
                <span onclick="AddOfficeLoanPartialPayment(' . $item->id . ');" class="text-primary fs-6 me-1 cursor-pointer" data-toggle="tooltip" title="' . __('messages.btns.add_partial_payment') . '">
                    <i class="fas fa-hand-holding-usd"></i>
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
        $sellers = User::where('role', 'office seller')->get();
        $cities = City::where('type', 'Warehouse')->get();
        $sellingCities = City::where('type', 'Selling')->get();
        return view('admin.office-loan.add', compact('sellers', 'cities', 'sellingCities'));
    }

    public function edit($id)
    {
        $sellers = User::where('role', 'office seller')->get();
        $cities = City::where('type', 'Warehouse')->get();
        $sellingCities = City::where('type', 'Selling')->get();
        $sale = Sale::whereId($id)->first();
        $saleDetails = SaleDetail::join('products', 'products.id', 'sale_details.product_id')
            ->select(
                'sale_details.*',
                'products.name',
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
                'quantity' => $value->quantity,
                'sub_total' => $value->sub_total
            ];
        }
        $jsonProducts = json_encode($array);

        return view('admin.office-loan.edit', compact('sale', 'saleDetails', 'sellers', 'cities', 'sellingCities', 'jsonProducts'));
    }

    public function view($id)
    {
        $sale = Sale::where('sales.id', $id)
            ->join('cities as warehouse', 'warehouse.id', 'sales.city_id')
            ->join('cities as selling', 'selling.id', 'sales.selling_city_id')
            ->select('sales.*', 'warehouse.name AS warehouse_city', 'selling.name AS selling_city')
            ->first();
        $seller = User::whereId($sale->seller_id)->first();
        $saleDetails = SaleDetail::join('products', 'products.id', 'sale_details.product_id')
            ->select(
                'sale_details.*',
                'products.name',
                'products.retail_price'
            )
            ->where('sale_id', $id)
            ->get();
        $partialPayments = PartialPayment::where('office_sale_id', $id)->get();
        return view('admin.office-loan.view', compact('sale', 'seller', 'saleDetails', 'partialPayments'));
    }

    public function statusUpdate(Request $request)
    {
        $sale = Sale::whereId($request['id'])->first();

        DB::beginTransaction();
        $affected = null;
        if ($sale->office_payment_type == 'Credit') {
            $sale->office_payment_type = 'Cash';
        }
        $sale->status = $request['status'];
        $affected = $sale->save();

        if ($affected) {
            DB::commit();
            return redirect()->route('loan.office')->with('success-message', 'Office sale status updated successfully');
        } else {
            DB::rollBack();
            return redirect()->route('loan.office')->with('error-message', 'An unhandled error occurred');
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

        $products = Product::where(function ($query) use ($searchTerm) {
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
            ->get()
            ->toArray();

        return json_encode($products);
    }

    public function partialPaymentStore(Request $request)
    {
        $office_sale_amount = Sale::query()->whereId($request->id)->first();
        $partialPaymentAmount = PartialPayment::query()->where('office_sale_id', $request->id)->sum('amount');
        if ($office_sale_amount->grand_total <= $partialPaymentAmount) {
            return redirect()->route('loan.office')->with('error-message', 'Loan payment is already completed');
        }
        $remainingAmount = $office_sale_amount->grand_total - $partialPaymentAmount;
        if ($remainingAmount < $request->amount) {
            return redirect()->route('loan.office')->with('error-message', 'Amount exceeds the loan amount');
        }
        $parPayment = new PartialPayment();
        $parPayment->office_sale_id = $request->id;
        $parPayment->amount = $request->amount;
        $affected = $parPayment->save();

        if ($affected) {
            return redirect()->route('loan.office')->with('success-message', 'Loan partial payment added successfully');
        } else {
            return redirect()->route('loan.office')->with('error-message', 'An unhandled error occurred');
        }
    }
}
