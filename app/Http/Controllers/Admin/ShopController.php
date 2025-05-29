<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    public function index()
    {
        return view('admin.shop.index');
    }

    function load(Request $request)
    {
        $limit = $request->post('length');
        $start = $request->post('start');
        // $searchTerm = $request->post('search')['value'];
        $searchTerm = $request['searchTerm'];

        $columnIndex = $request->post('order')[0]['column']; // Column index
        $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

        $seller_id = $request['seller_id'];

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;
        if ($searchTerm == '') {
            $fetch_data = Shop::select(
                'shops.*',
                DB::raw('COALESCE((SELECT SUM(orders.grand_total) FROM orders WHERE orders.shop_id = shops.id), 0) as total_sale')
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
            $recordsFiltered = Shop::select(
                'shops.*',
                DB::raw('COALESCE((SELECT SUM(orders.grand_total) FROM orders WHERE orders.shop_id = shops.id), 0) as total_sale')
            )
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = Shop::where(function ($query) {
                $query->where([
                    ['deleted_at', '=', null]
                ]);
            })
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('shop_id', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('location', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select(
                    'shops.*',
                    DB::raw('COALESCE((SELECT SUM(orders.grand_total) FROM orders WHERE orders.shop_id = shops.id), 0) as total_sale')
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
            $recordsFiltered = Shop::where(function ($query) use ($searchTerm) {
                $query->orWhere('shop_id', 'LIKE', '%' . $searchTerm . '%');
                $query->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                $query->orWhere('location', 'LIKE', '%' . $searchTerm . '%');
            })
                ->select(
                    'shops.*',
                    DB::raw('COALESCE((SELECT SUM(orders.grand_total) FROM orders WHERE orders.shop_id = shops.id), 0) as total_sale')
                )
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $Edit = route('shops.edit', array($item->id));
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['shop_id'] = $item->shop_id;
            $sub_array['name'] = $item->name;
            $sub_array['total_sale'] = SiteHelper::settings()['Currency_Icon'] . $item->total_sale;
            $sub_array['location'] = $item->location;
            if ($item->status == 'ban') {
                $sub_array['status'] = '<span id="' . $item->status . '" onclick="changeCustomerStatus(this.id)" class="btn-sm btn-danger cursor-pointer">Ban</span>';
            } else if ($item->status == 'active') {
                $sub_array['status'] = '<span id="' . $item->status . '||' . $item->id . '" onclick="changeCustomerStatus(this.id)" class="btn-sm btn-success cursor-pointer">Active</span>';
            }
            $sub_array['action'] = '
                <a href="' . $Edit . '" class="text-primary fs-6 me-1" data-toggle="tooltip" title="' . __('messages.btns.edit') . '">
                    <i class="far fa-edit"></i>
                </a>
                <span id="delete||' . $item->id . '" onclick="DeleteShop(' . $item->id . ');" class="text-danger fs-6 cursor-pointer ml-1" data-toggle="tooltip" title="' . __('messages.btns.delete') . '">
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
        $latestId = Shop::max('id');
        $incrementedId = $latestId + 1;
        $cities = City::where('type', 'Warehouse')->get();
        return view('admin.shop.add', compact('incrementedId', 'cities'));
    }

    function store(Request $request)
    {
        $affected = null;

        $firstTwoLetters = substr($request->name, 0, 2);
        $generated_id = Str::upper($firstTwoLetters) . rand(00000, 99999);

        $shop = new Shop();
        $shop->shop_id = $generated_id;
        $shop->user_id = $request->user()->id;
        $shop->name = $request->name;
        $shop->city_id = $request->city_id;
        $shop->location = $request->location;
        $shop->address = $request->address;
        $shop->micro_district = $request->micro_district;
        $shop->latitude = $request->latitude;
        $shop->longitude = $request->longitude;
        $shop->description = $request->description;
        $shop->status = 'active';
        $affected = $shop->save();

        if ($affected) {
            return redirect()->route('shops')->with('success-message', 'Shop added successfully');
        } else {
            return redirect()->route('shops')->with('error-message', 'An unhandled error occurred');
        }
    }

    function edit($id)
    {
        $cities = City::where('type', 'Warehouse')->get();
        $shop = Shop::whereId($id)->first();
        return view('admin.shop.edit', compact('cities', 'shop'));
    }

    function update(Request $request)
    {
        $affected = null;

        $shop = Shop::whereId($request->id)->first();
        $shop->name = $request->name;
        $shop->city_id = $request->city_id;
        $shop->location = $request->location;
        $shop->address = $request->address;
        $shop->micro_district = $request->micro_district;
        $shop->latitude = $request->latitude;
        $shop->longitude = $request->longitude;
        $shop->description = $request->description;
        $affected = $shop->save();

        if ($affected) {
            return redirect()->route('shops')->with('success-message', 'Shop updated successfully');
        } else {
            return redirect()->route('shops')->with('error-message', 'An unhandled error occurred');
        }
    }

    function view($id)
    {
        $seller = User::whereId($id)->first();
        return view('admin.shop.view', compact('seller'));
    }

    function delete(Request $request)
    {
        Shop::whereId($request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function nameCheck(Request $request)
    {
        $name = Shop::where('name', $request->name)->first();
        if ($name) {
            return response()->json(false);
        } else {
            return response()->json(true);
        }
    }
}
