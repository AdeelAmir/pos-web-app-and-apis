<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\OfficeExpenditure;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SellerTarget;
use App\Models\SellerTargetDetails;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SellerController extends Controller
{
    public function index()
    {
        return view('admin.sellers.index');
    }

    function load(Request $request)
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
            $fetch_data = User::where('deleted_at', '=', null)
                ->where('role', 'seller')
                ->select('users.*')
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
            $recordsFiltered = User::where('deleted_at', '=', null)
                ->where('role', 'seller')
                ->select('users.*')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = User::where(function ($query) {
                $query->where([
                    ['deleted_at', '=', null]
                ]);
            })
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('phone', 'LIKE', '%' . $searchTerm . '%');
                })
                ->where('role', 'seller')
                ->select('users.*')
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
            $recordsFiltered = User::where(function ($query) use ($searchTerm) {
                $query->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                $query->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
                $query->orWhere('phone', 'LIKE', '%' . $searchTerm . '%');
            })
                ->where('role', 'seller')
                ->select('users.*')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $Target = route('sellers.target', array($item->id));
            $View = route('sellers.view', array($item->id));
            $Edit = route('sellers.edit', array($item->id));
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['seller_id'] = $item->id;
            $sub_array['profile'] = $item->profile_image != '' ? '<img src="' . $item->profile_image . '" alt="Image" class="img-fluid rounded_circle" width="100px" height="100px">' : '';
            $sub_array['name'] = $item->name;
            $sub_array['email'] = $item->email;
            $sub_array['phone'] = $item->phone;
            if ($item->status == 0) {
                $sub_array['status'] = '<span id="' . $item->status . '||' . $item->id . '" onclick="changeSellerStatus(this.id)" class="btn-sm btn-danger cursor-pointer">Ban</span>';
            } else if ($item->status == 1) {
                $sub_array['status'] = '<span id="' . $item->status . '||' . $item->id . '" onclick="changeSellerStatus(this.id)" class="btn-sm btn-success cursor-pointer">Active</span>';
            }
            $sub_array['action'] = '
                <a href="' . $Target . '" class="text-secondary fs-6 me-1" data-toggle="tooltip" title="' . __('messages.btns.target') . '">
                    <i class="fas fa-bullseye"></i>
                </a>
                <a href="' . $View . '" class="text-secondary fs-6 me-1" data-toggle="tooltip" title="' . __('messages.btns.view') . '">
                    <i class="far fa-eye"></i>
                </a>
                <a href="' . $Edit . '" class="text-primary fs-6 me-1" data-toggle="tooltip" title="' . __('messages.btns.edit') . '">
                    <i class="far fa-edit"></i>
                </a>
                <span id="delete||' . $item->id . '" onclick="DeleteSellers(' . $item->id . ');" class="text-danger fs-6 cursor-pointer ml-1" data-toggle="tooltip" title="' . __('messages.btns.delete') . '">
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

    function add()
    {
        $latestId = User::max('id');
        $incrementedId = $latestId + 1;
        return view('admin.sellers.add', compact('incrementedId'));
    }

    function store(Request $request)
    {
        $profileImage = "";
        if ($request->has('profile')) {
            $profileImage = 'profile-picture_' . Carbon::now()->format('Ymd-His') . '.' . $request->file('profile')->extension();
            $request->file('profile')->storeAs('public/users/', $profileImage);
        }

        $affected = null;

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone = $request->phone;
        $user->profile_image = $profileImage;
        $user->description = $request->description;
        $user->role = 'seller';
        $affected = $user->save();

        if ($affected) {
            return redirect()->route('sellers')->with('success-message', 'Seller added successfully');
        } else {
            return redirect()->route('sellers')->with('error-message', 'An unhandled error occurred');
        }
    }

    function edit($id)
    {
        $seller = User::whereId($id)->first();
        return view('admin.sellers.edit', compact('seller'));
    }

    function update(Request $request)
    {
        $profileImage = '';
        if ($request->hasFile('profile')) {
            if ($request['oldProfile'] != '') {
                $explodedOldProfile = explode('/', $request->oldProfile);
                $oldProfile = end($explodedOldProfile);
                $path = public_path('storage/users') . '/' . $oldProfile;
                // Unlink the old file if it exists
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $profileImage = 'profile-picture_' . Carbon::now()->format('Ymd-His') . '.' . $request->file('profile')->extension();
            $request->file('profile')->storeAs('public/users/', $profileImage);
        } else {
            $explodedOldProfile = explode('/', $request->oldProfile);
            $profileImage = end($explodedOldProfile);
        }

        $affected = null;

        $user = User::whereId($request->id)->first();
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password != '') {
            $user->password = Hash::make($request->password);
        }
        $user->phone = $request->phone;
        $user->profile_image = $profileImage;
        $affected = $user->save();

        if ($affected) {
            return redirect()->route('sellers')->with('success-message', 'Seller updated successfully');
        } else {
            return redirect()->route('sellers')->with('error-message', 'An unhandled error occurred');
        }
    }

    function view($id)
    {
        $seller = User::whereId($id)->first();

        $totalSale = Order::where('seller_id', $id)
            ->where('payment_type', 'Cash')
            ->where('sale_type', 'Stock')
            ->where('status', 'Completed')
            ->sum('grand_total');

        $totalExpenses = OfficeExpenditure::join('office_expenditure_details', 'office_expenditures.id', 'office_expenditure_details.office_expenditure_id')
            ->where('office_expenditures.seller_id', $id)
            ->sum('office_expenditure_details.amount');

        // $totalGoods = Order::join('order_details', 'orders.id', 'order_details.order_id')
        //     ->where('seller_id', $id)
        //     ->where('payment_type', 'Cash')
        //     ->where('sale_type', 'Stock')
        //     ->where('status', 'Completed')
        //     ->sum('order_details.quantity');

        $products = Product::with('category', 'city')
            ->join('sale_details', 'sale_details.product_id', 'products.id')
            ->join('sales', 'sales.id', 'sale_details.sale_id')
            ->where('sales.seller_id', $id)
            ->where('sales.bonus', 0)
            ->select(
                'products.*',
                'sales.date',
                'sales.grand_total',
                'sale_details.quantity',
                'sale_details.sub_total',
                DB::raw(
                    'COALESCE(
                        (
                            SELECT SUM(sd.quantity)
                            FROM sales s
                            JOIN sale_details sd ON s.id = sd.sale_id
                            WHERE s.seller_id = ' . $id . '
                            AND s.bonus = 0
                            AND sd.product_id = products.id
                        ), 0
                    ) - COALESCE(
                        (
                            SELECT SUM(od.quantity)
                            FROM orders o
                            JOIN order_details od ON o.id = od.order_id
                            WHERE o.seller_id = ' . $id . '
                            AND o.sale_type = "Stock"
                            AND od.product_id = products.id
                        ), 0
                    ) AS remaining_product'
                )
            )
            ->groupBy('products.id')
            ->get();

        $totalGoods = 0;

        foreach ($products as $product) {
            $totalGoods += $product->remaining_product;
        }

        $totalCreditLeft = Order::where('seller_id', $id)
            ->where('payment_type', 'Credit')
            ->where('sale_type', 'Stock')
            ->where('status', 'Pending')
            ->sum('grand_total');

        $todaySale = Order::where('seller_id', $id)
            ->where('date', Carbon::now()->format('Y-m-d'))
            ->where('sale_type', 'Stock')
            ->where('payment_type', 'Cash')
            ->where('status', 'Completed')
            ->sum('grand_total');

        $totalBonus = Order::where('seller_id', $id)
            ->where('date', Carbon::now()->format('Y-m-d'))
            ->where('sale_type', 'Bonus')
            ->sum('grand_total');

        return view('admin.sellers.view', compact('seller', 'totalSale', 'totalExpenses', 'totalGoods', 'totalCreditLeft', 'todaySale', 'totalBonus'));
    }

    public function statusUpdate(Request $request)
    {
        $seller = User::whereId($request['id'])->where('role', 'seller')->first();

        DB::beginTransaction();
        $affected = null;
        $seller->status = $request['status'];
        $affected = $seller->save();

        if ($affected) {
            DB::commit();
            return redirect()->route('sellers')->with('success-message', 'Seller status updated successfully');
        } else {
            DB::rollBack();
            return redirect()->route('sellers')->with('error-message', 'An unhandled error occurred');
        }
    }

    function delete(Request $request)
    {
        $user = User::whereId($request->id)->first();
        if ($user->profile_image != '') {
            $explodedProfile = explode('/', $user->profile_image);
            $profile = end($explodedProfile);
            $path = public_path('storage/users') . '/' . $profile;
            // Unlink the old file if it exists
            if (file_exists($path)) {
                unlink($path);
            }
        }

        User::whereId($request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function target($id)
    {
        return view('admin.sellers.target.index', compact('id'));
    }

    function targetLoad(Request $request)
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
            $fetch_data = SellerTarget::join('seller_target_details', 'seller_target_details.target_id', 'seller_targets.id')
                ->where('seller_id', $request->id)
                ->select(
                    'seller_targets.*',
                    DB::raw('COALESCE(SUM(seller_target_details.quantity), 0) AS total_quantity')
                )
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
            $recordsFiltered = SellerTarget::join('seller_target_details', 'seller_target_details.target_id', 'seller_targets.id')
                ->where('seller_id', $request->id)
                ->select(
                    'seller_targets.*',
                    DB::raw('COALESCE(SUM(seller_target_details.quantity), 0) AS total_quantity')
                )
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = SellerTarget::join('seller_target_details', 'seller_target_details.target_id', 'seller_targets.id')
                ->where('seller_id', $request->id)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('phone', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select(
                    'seller_targets.*',
                    DB::raw('COALESCE(SUM(seller_target_details.quantity), 0) AS total_quantity')
                )
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
            $recordsFiltered = SellerTarget::join('seller_target_details', 'seller_target_details.target_id', 'seller_targets.id')
                ->where('seller_id', $request->id)
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('phone', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select(
                    'seller_targets.*',
                    DB::raw('COALESCE(SUM(seller_target_details.quantity), 0) AS total_quantity')
                )
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $Edit = route('sellers.target.edit', ['SellerId' => $item->seller_id, 'Id' => $item->id]);

            $sellerTargetDetails = SellerTargetDetails::where('target_id', $item->id)->get();

            $cal = 0;
            $startDate = Carbon::parse('01' . $item->month . $item->year)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::parse($startDate)->endOfMonth()->format('Y-m-d');
            foreach ($sellerTargetDetails as $targetData) {
                $orders = Order::join('order_details', 'order_details.order_id', 'orders.id')
                    ->whereBetween('orders.date', [$startDate, $endDate])
                    ->where('order_details.product_id', $targetData->product_id)
                    ->where('orders.seller_id', $item->seller_id)
                    ->where('orders.status', 'Completed')
                    ->sum('order_details.quantity');
                $cal += $orders;
            }

            $percentage = ($cal * 100) / $item->total_quantity;

            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['month'] = $item->month;
            $sub_array['year'] = $item->year;
            $sub_array['quantity'] = $item->total_quantity;
            $sub_array['completion'] = round(($percentage > 100 ? 100 : $percentage), 2) . '%';
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

    function targetAdd($id)
    {
        $products = Product::get();
        return view('admin.sellers.target.add', compact('id', 'products'));
    }

    function targetStore(Request $request)
    {
        $affected = null;
        $sellerTargetDetailsArray = [];

        $sellerTargetCheck = SellerTarget::where('seller_id', $request->id)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->first();
        if ($sellerTargetCheck) {
            return redirect()->route('sellers.target', [$request->id])->with('error-message', 'Seller Target already added!');
        }

        $sellerTarget = new SellerTarget();
        $sellerTarget->seller_id = $request->id;
        $sellerTarget->month = $request->month;
        $sellerTarget->month_number = Carbon::parse($request->month)->format('m');
        $sellerTarget->year = $request->year;
        $affected = $sellerTarget->save();

        if ($request->target_repeater != '') {
            foreach ($request->target_repeater as $data) {
                $subArray = [];
                $subArray['target_id'] = $sellerTarget->id;
                $subArray['product_id'] = $data['product_id'];
                $subArray['quantity'] = $data['quantity'];
                $subArray['created_at'] = Carbon::now();
                $sellerTargetDetailsArray[] = $subArray;
            }
            foreach (array_chunk($sellerTargetDetailsArray, 1000) as $cluster) {
                SellerTargetDetails::insert($cluster);
            }
        }

        if ($affected) {
            return redirect()->route('sellers.target', [$request->id])->with('success-message', 'Seller added successfully');
        } else {
            return redirect()->route('sellers.target', [$request->id])->with('error-message', 'An unhandled error occurred');
        }
    }

    function targetEdit($seller_id, $id)
    {
        $products = Product::get();
        $target = SellerTarget::whereId($id)->first();
        $sellerTargetDetails = SellerTargetDetails::where('target_id', operator: $id)->get();
        return view('admin.sellers.target.edit', compact('id', 'seller_id', 'products', 'target', 'sellerTargetDetails'));
    }

    public function targetUpdate(Request $request)
    {
        $affected = null;

        $sellerTarget = SellerTarget::whereId($request->id)->first();
        $sellerTarget->month = $request->month;
        $sellerTarget->month_number = Carbon::parse($request->month)->format('m');
        $sellerTarget->year = $request->year;
        $affected = $sellerTarget->save();

        if ($request->target_repeater != '') {
            SellerTargetDetails::where('target_id', $sellerTarget->id)->delete();
            foreach ($request->target_repeater as $data) {
                $subArray = [];
                $subArray['target_id'] = $sellerTarget->id;
                $subArray['product_id'] = $data['product_id'];
                $subArray['quantity'] = $data['quantity'];
                $subArray['created_at'] = Carbon::now();
                $sellerTargetDetailsArray[] = $subArray;
            }
            foreach (array_chunk($sellerTargetDetailsArray, 1000) as $cluster) {
                SellerTargetDetails::insert($cluster);
            }
        }

        if ($affected) {
            return redirect()->route('sellers.target', [$request->seller_id])->with('success-message', 'Seller added successfully');
        } else {
            return redirect()->route('sellers.target', [$request->seller_id])->with('error-message', 'An unhandled error occurred');
        }
    }

    public function getProducts()
    {
        $products = Product::get();
        return response()->json($products);
    }

    function loadSale(Request $request)
    {
        $limit = $request->post('length');
        $start = $request->post('start');
        // $searchTerm = $request->post('search')['value'];
        $searchTerm = $request['searchTerm'];
        $seller_id = $request['seller_id'];
        $type = $request['type'];

        $columnIndex = $request->post('order')[0]['column']; // Column index
        $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;
        if ($searchTerm == '') {
            $fetch_data = Order::join('shops', 'shops.id', 'orders.shop_id')
                ->where('orders.seller_id', $seller_id)
                ->where('orders.status', 'Completed')
                ->where('orders.sale_type', 'Stock')
                ->where(function ($query) use ($type) {
                    if ($type == 'today') {
                        $query->where('date', Carbon::now()->format('Y-m-d'));
                    }
                })
                ->select(
                    'orders.*',
                    'shops.name as shop_name'
                )
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
            $recordsFiltered = Order::join('shops', 'shops.id', 'orders.shop_id')
                ->where('orders.seller_id', $seller_id)
                ->where('orders.status', 'Completed')
                ->where('orders.sale_type', 'Stock')
                ->where(function ($query) use ($type) {
                    if ($type == 'today') {
                        $query->where('date', Carbon::now()->format('Y-m-d'));
                    }
                })
                ->select(
                    'orders.*',
                    'shops.name as shop_name',
                )
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = Order::join('shops', 'shops.id', 'orders.shop_id')
                // ->where(function ($query) use ($searchTerm) {
                //     $query->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                //     $query->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
                //     $query->orWhere('phone', 'LIKE', '%' . $searchTerm . '%');
                // })
                ->where('orders.seller_id', $seller_id)
                ->where('orders.status', 'Completed')
                ->where('orders.sale_type', 'Stock')
                ->where(function ($query) use ($type) {
                    if ($type == 'today') {
                        $query->where('date', Carbon::now()->format('Y-m-d'));
                    }
                })
                ->select(
                    'orders.*',
                    'shops.name as shop_name',
                )
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
            $recordsFiltered = Order::join('shops', 'shops.id', 'orders.shop_id')
                // ->where(function ($query) use ($searchTerm) {
                //     $query->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                //     $query->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
                //     $query->orWhere('phone', 'LIKE', '%' . $searchTerm . '%');
                // })
                ->where('orders.seller_id', $seller_id)
                ->where('orders.status', 'Completed')
                ->where('orders.sale_type', 'Stock')
                ->where(function ($query) use ($type) {
                    if ($type == 'today') {
                        $query->where('date', Carbon::now()->format('Y-m-d'));
                    }
                })
                ->select(
                    'orders.*',
                    'shops.name as shop_name',
                )
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['shop_name'] = $item->shop_name;
            $sub_array['cash'] = ($item->payment_type == 'Credit' && $item->status == 'Pending') ? SiteHelper::settings()['Currency_Icon'] . 0 : SiteHelper::settings()['Currency_Icon'] . $item->grand_total;
            $sub_array['loan'] = ($item->payment_type == 'Credit' && $item->status == 'Pending') ? SiteHelper::settings()['Currency_Icon'] . $item->grand_total : SiteHelper::settings()['Currency_Icon'] . 0;
            if ($item->status == 'Pending') {
                $sub_array['status'] = '<span id="' . $item->status . '||' . $item->id . '" onclick="(this.id)" class="btn-sm btn-warning cursor-pointer">Pending</span>';
            } elseif ($item->status == 'Completed') {
                $sub_array['status'] = '<span id="' . $item->status . '||' . $item->id . '" class="btn-sm btn-success cursor-pointer">Completed</span>';
            }
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

    function loadExpense(Request $request)
    {
        $limit = $request->post('length');
        $start = $request->post('start');
        // $searchTerm = $request->post('search')['value'];
        $searchTerm = $request['searchTerm'];
        $seller_id = $request['seller_id'];

        $columnIndex = $request->post('order')[0]['column']; // Column index
        $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;
        if ($searchTerm == '') {
            $fetch_data = OfficeExpenditure::join('users', 'users.id', 'office_expenditures.seller_id')
                ->where('office_expenditures.seller_id', $seller_id)
                ->select(
                    'office_expenditures.*',
                    'users.name as seller_name',
                    DB::raw('(SELECT COALESCE(SUM(office_expenditure_details.amount), 0) FROM office_expenditure_details WHERE office_expenditure_details.office_expenditure_id = office_expenditures.id) AS total_amount'),
                )
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
            $recordsFiltered = OfficeExpenditure::join('users', 'users.id', 'office_expenditures.seller_id')
                ->where('office_expenditures.seller_id', $seller_id)
                ->select(
                    'office_expenditures.*',
                    'users.name as seller_name',
                    DB::raw('(SELECT COALESCE(SUM(office_expenditure_details.amount), 0) FROM office_expenditure_details WHERE office_expenditure_details.office_expenditure_id = office_expenditures.id) AS total_amount'),
                )
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = OfficeExpenditure::join('users', 'users.id', 'office_expenditures.seller_id')
                // ->where(function ($query) use ($searchTerm) {
                //     $query->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                //     $query->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
                //     $query->orWhere('phone', 'LIKE', '%' . $searchTerm . '%');
                // })
                ->where('office_expenditures.seller_id', $seller_id)
                ->select(
                    'office_expenditures.*',
                    'users.name as seller_name',
                    DB::raw('(SELECT COALESCE(SUM(office_expenditure_details.amount), 0) FROM office_expenditure_details WHERE office_expenditure_details.office_expenditure_id = office_expenditures.id) AS total_amount'),
                )
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
            $recordsFiltered = OfficeExpenditure::join('users', 'users.id', 'office_expenditures.seller_id')
                // ->where(function ($query) use ($searchTerm) {
                //     $query->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                //     $query->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
                //     $query->orWhere('phone', 'LIKE', '%' . $searchTerm . '%');
                // })
                ->where('office_expenditures.seller_id', $seller_id)
                ->select(
                    'office_expenditures.*',
                    'users.name as seller_name',
                    DB::raw('(SELECT COALESCE(SUM(office_expenditure_details.amount), 0) FROM office_expenditure_details WHERE office_expenditure_details.office_expenditure_id = office_expenditures.id) AS total_amount'),
                )
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $Edit = route('expenditure.seller.edit', array($item->id));
            $View = '#';
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['seller_name'] = $item->seller_name;
            $sub_array['date'] = Carbon::parse($item->expenditure_date)->format('d-m-Y');
            $sub_array['amount'] = SiteHelper::settings()['Currency_Icon'] . $item->total_amount;
            $sub_array['action'] = '
                <a href="' . $Edit . '" class="text-primary fs-6 mr-1" data-toggle="tooltip" title="' . __('messages.btns.edit') . '">
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

    //    function loadItems(Request $request)
    //    {
    //        $limit = $request->post('length');
    //        $start = $request->post('start');
    //        // $searchTerm = $request->post('search')['value'];
    //        $searchTerm = $request['searchTerm'];
    //        $seller_id = $request['seller_id'];
    //
    //        $columnIndex = $request->post('order')[0]['column']; // Column index
    //        $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
    //        $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc
    //
    //        $fetch_data = null;
    //        $recordsTotal = null;
    //        $recordsFiltered = null;
    //        if ($searchTerm == '') {
    //            $fetch_data = Sale::join('sale_details', 'sale_details.sale_id', 'sales.id')
    //                ->join('products', 'products.id', 'sale_details.product_id')
    //                ->where('sales.seller_id', $seller_id)
    //                ->where('sales.status', 'Completed')
    //                ->select(
    //                    'sales.*',
    //                    'sale_details.quantity',
    //                    'sale_details.sub_total',
    //                    'products.name as product_name',
    //                    'products.pieces_in_box'
    //                )
    //                ->orderBy($columnName, $columnSortOrder);
    //            if ($limit == -1) {
    //                $fetch_data = $fetch_data
    //                    ->get();
    //            } else {
    //                $fetch_data = $fetch_data
    //                    ->offset($start)
    //                    ->limit($limit)
    //                    ->get();
    //            }
    //            $recordsTotal = sizeof($fetch_data);
    //            $recordsFiltered = Sale::join('sale_details', 'sale_details.sale_id', 'sales.id')
    //                ->join('products', 'products.id', 'sale_details.product_id')
    //                ->where('sales.seller_id', $seller_id)
    //                ->where('sales.status', 'Completed')
    //                ->select(
    //                    'sales.*',
    //                    'sale_details.quantity',
    //                    'sale_details.sub_total',
    //                    'products.name as product_name',
    //                    'products.pieces_in_box'
    //                )
    //                ->orderBy($columnName, $columnSortOrder)
    //                ->count();
    //        } else {
    //            $fetch_data = Sale::join('sale_details', 'sale_details.sale_id', 'sales.id')
    //                ->join('products', 'products.id', 'sale_details.product_id')
    //                ->where('sales.seller_id', $seller_id)
    //                ->where('sales.status', 'Completed')
    //                ->select(
    //                    'sales.*',
    //                    'sale_details.quantity',
    //                    'sale_details.sub_total',
    //                    'products.name as product_name',
    //                    'products.pieces_in_box'
    //                )
    //                ->orderBy($columnName, $columnSortOrder);
    //            if ($limit == -1) {
    //                $fetch_data = $fetch_data
    //                    ->get();
    //            } else {
    //                $fetch_data = $fetch_data
    //                    ->offset($start)
    //                    ->limit($limit)
    //                    ->get();
    //            }
    //            $recordsTotal = sizeof($fetch_data);
    //            $recordsFiltered = Sale::join('sale_details', 'sale_details.sale_id', 'sales.id')
    //                ->join('products', 'products.id', 'sale_details.product_id')
    //                ->where('sales.seller_id', $seller_id)
    //                ->where('sales.status', 'Completed')
    //                ->select(
    //                    'sales.*',
    //                    'sale_details.quantity',
    //                    'sale_details.sub_total',
    //                    'products.name as product_name',
    //                    'products.pieces_in_box'
    //                )
    //                ->orderBy($columnName, $columnSortOrder)
    //                ->count();
    //        }
    //
    //        $data = array();
    //        $SrNo = $start + 1;
    //        foreach ($fetch_data as $row => $item) {
    //            $View = '#';
    //            $sub_array = array();
    //            $sub_array['id'] = $SrNo;
    //            $sub_array['product_name'] = $item->product_name;
    //            $sub_array['quantity'] = $item->quantity;
    //            $sub_array['boxes'] = (!empty($item->quantity) && !empty($item->pieces_in_box)) ? SiteHelper::makeBoxes($item->quantity, $item->pieces_in_box) : 0;
    //            $sub_array['sub_total'] = SiteHelper::settings()['Currency_Icon'] . $item->sub_total;
    //            $SrNo++;
    //            $data[] = $sub_array;
    //        }
    //
    //        $json_data = array(
    //            "draw" => intval($request->post('draw')),
    //            "iTotalRecords" => $recordsTotal,
    //            "iTotalDisplayRecords" => $recordsFiltered,
    //            "aaData" => $data
    //        );
    //
    //        echo json_encode($json_data);
    //    }


    function loadItems(Request $request)
    {
        $limit = $request->post('length');
        $start = $request->post('start');
        // $searchTerm = $request->post('search')['value'];
        $searchTerm = $request['searchTerm'];
        $seller_id = $request['seller_id'];

        $columnIndex = $request->post('order')[0]['column']; // Column index
        $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;
        if ($searchTerm == '') {
            //            $fetch_data = Sale::join('sale_details', 'sale_details.sale_id', 'sales.id')
            //                ->join('products', 'products.id', 'sale_details.product_id')
            //                ->where('sales.seller_id', $seller_id)
            //                ->where('sales.status', 'Completed')
            //                ->select(
            //                    'sales.*',
            //                    'sale_details.quantity',
            //                    'sale_details.sub_total',
            //                    'products.name as product_name',
            //                    'products.pieces_in_box'
            //                )
            //                ->orderBy($columnName, $columnSortOrder);

            $fetch_data = Product::with('category', 'city')
                ->join('sale_details', 'sale_details.product_id', 'products.id')
                ->join('sales', 'sales.id', 'sale_details.sale_id')
                ->where('sales.seller_id', $seller_id)
                ->where('sales.bonus', 0)
                ->select(
                    'products.*',
                    'sales.date',
                    'sales.grand_total',
                    'sale_details.quantity',
                    'sale_details.sub_total',
                    DB::raw(
                        'COALESCE(
                        (
                            SELECT SUM(sd.quantity)
                            FROM sales s
                            JOIN sale_details sd ON s.id = sd.sale_id
                            WHERE s.seller_id = ' . $seller_id . '
                            AND s.bonus = 0
                            AND sd.product_id = products.id
                        ), 0
                    ) - COALESCE(
                        (
                            SELECT SUM(od.quantity)
                            FROM orders o
                            JOIN order_details od ON o.id = od.order_id
                            WHERE o.seller_id = ' . $seller_id . '
                            AND o.sale_type = "Stock"
                            AND od.product_id = products.id
                        ), 0
                    ) AS remaining_product'
                    )
                )
                ->groupBy('products.id')
                ->orderBy('products.id', $columnSortOrder);

            if ($limit == -1) {
                $fetch_data = $fetch_data->get();
            } else {
                $fetch_data = $fetch_data
                    // ->offset($start)
                    // ->limit($limit)
                    ->get();
            }
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = Product::with('category', 'city')
                ->join('sale_details', 'sale_details.product_id', 'products.id')
                ->join('sales', 'sales.id', 'sale_details.sale_id')
                ->where('sales.seller_id', $seller_id)
                ->where('sales.bonus', 0)
                ->select(
                    'products.*',
                    'sales.date',
                    'sales.grand_total',
                    'sale_details.quantity',
                    'sale_details.sub_total',
                    DB::raw(
                        'COALESCE(
                            (
                                SELECT SUM(sd.quantity)
                                FROM sales s
                                JOIN sale_details sd ON s.id = sd.sale_id
                                WHERE s.seller_id = ' . $seller_id . '
                                AND s.bonus = 0
                                AND sd.product_id = products.id
                            ), 0
                        ) - COALESCE(
                            (
                                SELECT SUM(od.quantity)
                                FROM orders o
                                JOIN order_details od ON o.id = od.order_id
                                WHERE o.seller_id = ' . $seller_id . '
                                AND o.sale_type = "Stock"
                                AND od.product_id = products.id
                            ), 0
                        ) AS remaining_product'
                    )
                )
                ->groupBy('products.id')
                ->orderBy('products.id', $columnSortOrder)
                ->count();
        } else {
            $fetch_data = Product::with('category', 'city')
                ->join('sale_details', 'sale_details.product_id', 'products.id')
                ->join('sales', 'sales.id', 'sale_details.sale_id')
                ->where('sales.seller_id', $seller_id)
                ->where('sales.bonus', 0)
                ->select(
                    'products.*',
                    'sales.date',
                    'sales.grand_total',
                    'sale_details.quantity',
                    'sale_details.sub_total',
                    DB::raw(
                        'COALESCE(
                    (
                        SELECT SUM(sd.quantity)
                        FROM sales s
                        JOIN sale_details sd ON s.id = sd.sale_id
                        WHERE s.seller_id = ' . $seller_id . '
                        AND s.bonus = 0
                        AND sd.product_id = products.id
                    ), 0
                ) - COALESCE(
                    (
                        SELECT SUM(od.quantity)
                        FROM orders o
                        JOIN order_details od ON o.id = od.order_id
                        WHERE o.seller_id = ' . $seller_id . '
                        AND o.sale_type = "Stock"
                        AND od.product_id = products.id
                    ), 0
                ) AS remaining_product'
                    )
                )
                ->groupBy('products.id')
                ->orderBy('products.id', $columnSortOrder);
            if ($limit == -1) {
                $fetch_data = $fetch_data->get();
            } else {
                $fetch_data = $fetch_data
                    // ->offset($start)
                    // ->limit($limit)
                    ->get();
            }
            $recordsTotal = sizeof($fetch_data);
            $recordsFiltered = Product::with('category', 'city')
                ->join('sale_details', 'sale_details.product_id', 'products.id')
                ->join('sales', 'sales.id', 'sale_details.sale_id')
                ->where('sales.seller_id', $seller_id)
                ->where('sales.bonus', 0)
                ->select(
                    'products.*',
                    'sales.date',
                    'sales.grand_total',
                    'sale_details.quantity',
                    'sale_details.sub_total',
                    DB::raw(
                        'COALESCE(
                            (
                                SELECT SUM(sd.quantity)
                                FROM sales s
                                JOIN sale_details sd ON s.id = sd.sale_id
                                WHERE s.seller_id = ' . $seller_id . '
                                AND s.bonus = 0
                                AND sd.product_id = products.id
                            ), 0
                        ) - COALESCE(
                            (
                                SELECT SUM(od.quantity)
                                FROM orders o
                                JOIN order_details od ON o.id = od.order_id
                                WHERE o.seller_id = ' . $seller_id . '
                                AND o.sale_type = "Stock"
                                AND od.product_id = products.id
                            ), 0
                        ) AS remaining_product'
                    )
                )
                ->groupBy('products.id')
                ->orderBy('products.id', $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $View = '#';
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['product_name'] = $item->name;
            $sub_array['quantity'] = $item->remaining_product;
            $sub_array['boxes'] = (!empty($item->remaining_product) && !empty($item->pieces_in_box)) ? SiteHelper::makeBoxes($item->remaining_product, $item->pieces_in_box) : 0;
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
    function loadLoan(Request $request)
    {
        $limit = $request->post('length');
        $start = $request->post('start');
        // $searchTerm = $request->post('search')['value'];
        $searchTerm = $request['searchTerm'];
        $seller_id = $request['seller_id'];

        $columnIndex = $request->post('order')[0]['column']; // Column index
        $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;
        if ($searchTerm == '') {
            $fetch_data = Order::join('shops', 'shops.id', 'orders.shop_id')
                ->where('orders.seller_id', $seller_id)
                ->where('orders.status', 'Pending')
                ->where('orders.payment_type', 'Credit')
                ->where('orders.sale_type', 'Stock')
                ->select(
                    'orders.*',
                    'shops.name as shop_name',
                )
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
            $recordsFiltered = Order::join('shops', 'shops.id', 'orders.shop_id')
                ->where('orders.seller_id', $seller_id)
                ->where('orders.status', 'Pending')
                ->where('orders.payment_type', 'Credit')
                ->where('orders.sale_type', 'Stock')
                ->select(
                    'orders.*',
                    'shops.name as shop_name',
                )
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = Order::join('shops', 'shops.id', 'orders.shop_id')
                ->where('orders.seller_id', $seller_id)
                // ->where(function ($query) use ($searchTerm) {
                //     $query->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                //     $query->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
                //     $query->orWhere('phone', 'LIKE', '%' . $searchTerm . '%');
                // })
                ->where('orders.status', 'Pending')
                ->where('orders.payment_type', 'Credit')
                ->where('orders.sale_type', 'Stock')
                ->select(
                    'orders.*',
                    'shops.name as shop_name',
                )
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
            $recordsFiltered = Order::join('shops', 'shops.id', 'orders.shop_id')
                ->where('orders.seller_id', $seller_id)
                // ->where(function ($query) use ($searchTerm) {
                //     $query->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                //     $query->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
                //     $query->orWhere('phone', 'LIKE', '%' . $searchTerm . '%');
                // })
                ->where('orders.status', 'Pending')
                ->where('orders.payment_type', 'Credit')
                ->where('orders.sale_type', 'Stock')
                ->select(
                    'orders.*',
                    'shops.name as shop_name',
                )
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $View = '#';
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['shop_name'] = $item->shop_name;
            $sub_array['date'] = Carbon::parse($item->date)->format('d-m-Y');
            $sub_array['loan'] = SiteHelper::settings()['Currency_Icon'] . $item->grand_total;
            // $sub_array['action'] = '
            //     <a href="' . $View . '" class="text-secondary cursor-pointer fs-6 me-1" data-toggle="tooltip" title="' . __('messages.btns.view') . '">
            //         <i class="fa fa-eye"></i>
            //     </a>';
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

    function loadBonus(Request $request)
    {
        $limit = $request->post('length');
        $start = $request->post('start');
        // $searchTerm = $request->post('search')['value'];
        $searchTerm = $request['searchTerm'];
        $seller_id = $request['seller_id'];

        $columnIndex = $request->post('order')[0]['column']; // Column index
        $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;
        if ($searchTerm == '') {
            $fetch_data = Order::join('shops', 'shops.id', 'orders.shop_id')
                ->where('orders.seller_id', $seller_id)
                ->where('orders.sale_type', 'Bonus')
                ->select(
                    'orders.*',
                    'shops.name as shop_name',
                )
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
            $recordsFiltered = Order::join('shops', 'shops.id', 'orders.shop_id')
                ->where('orders.seller_id', $seller_id)
                ->where('orders.sale_type', 'Bonus')
                ->select(
                    'orders.*',
                    'shops.name as shop_name',
                )
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = Order::join('shops', 'shops.id', 'orders.shop_id')
                ->where('orders.seller_id', $seller_id)
                // ->where(function ($query) use ($searchTerm) {
                //     $query->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                //     $query->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
                //     $query->orWhere('phone', 'LIKE', '%' . $searchTerm . '%');
                // })
                ->where('orders.sale_type', 'Bonus')
                ->select(
                    'orders.*',
                    'shops.name as shop_name',
                )
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
            $recordsFiltered = Order::join('shops', 'shops.id', 'orders.shop_id')
                ->where('orders.seller_id', $seller_id)
                // ->where(function ($query) use ($searchTerm) {
                //     $query->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                //     $query->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
                //     $query->orWhere('phone', 'LIKE', '%' . $searchTerm . '%');
                // })
                ->where('orders.sale_type', 'Bonus')
                ->select(
                    'orders.*',
                    'shops.name as shop_name',
                )
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $View = '#';
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['shop_name'] = $item->shop_name;
            $sub_array['date'] = Carbon::parse($item->date)->format('d-m-Y');
            $sub_array['bonus'] = SiteHelper::settings()['Currency_Icon'] . $item->grand_total;
            // $sub_array['action'] = '
            //     <a href="' . $View . '" class="text-secondary cursor-pointer fs-6 me-1" data-toggle="tooltip" title="' . __('messages.btns.view') . '">
            //         <i class="fa fa-eye"></i>
            //     </a>';
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
}
