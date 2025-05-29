<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Demand;
use App\Models\DemandDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DemandController extends Controller
{
    public function index()
    {
        return view('admin.demands.index');
    }

    function load(Request $request)
    {
        $limit = $request->post('length');
        $start = $request->post('start');
        // $searchTerm = $request->post('search')['value'];
        $searchTerm = $request['searchTerm'];
        $startDate = $request['start_date'];
        $endDate = $request['end_date'];

        $columnIndex = $request->post('order')[0]['column']; // Column index
        $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;
        if ($searchTerm == '') {
            $fetch_data = Demand::join('users', 'users.id', 'demands.seller_id')
                ->where(function ($query) use ($startDate, $endDate) {
                    if ($startDate != '' && $endDate != '') {
                        $query->whereBetween('demands.demand_date', [$startDate, $endDate]);
                    }
                })
                ->select(
                    'demands.*',
                    'users.name',
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
            $recordsFiltered = Demand::join('users', 'users.id', 'demands.seller_id')
                ->where(function ($query) use ($startDate, $endDate) {
                    if ($startDate != '' && $endDate != '') {
                        $query->whereBetween('demands.demand_date', [$startDate, $endDate]);
                    }
                })
                ->select(
                    'demands.*',
                    'users.name',
                )
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        } else {
            $fetch_data = Demand::join('users', 'users.id', 'demands.seller_id')
                ->where(function ($query) use ($startDate, $endDate) {
                    if ($startDate != '' && $endDate != '') {
                        $query->whereBetween('demands.demand_date', [$startDate, $endDate]);
                    }
                })
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('users.name', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('demands.demand_date', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select(
                    'demands.*',
                    'users.name',
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
            $recordsFiltered = Demand::join('users', 'users.id', 'demands.seller_id')
                ->where(function ($query) use ($startDate, $endDate) {
                    if ($startDate != '' && $endDate != '') {
                        $query->whereBetween('demands.demand_date', [$startDate, $endDate]);
                    }
                })
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('users.name', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('demands.demand_date', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select(
                    'demands.*',
                    'users.name',
                )
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $View = route('demands.view', array($item->id));
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['name'] = $item->name;
            $sub_array['date'] = Carbon::parse($item->demand_date)->format('d-m-Y');
            $sub_array['action'] = '
                <a href="' . $View . '" class="text-secondary fs-6 mr-1" data-toggle="tooltip" title="' . __('messages.btns.view') . '">
                    <i class="far fa-eye"></i>
                </a>';
            // $sub_array['action'] = '
            //     <a href="' . $Edit . '" class="text-primary fs-6 mr-1" data-toggle="tooltip" title="' . __('messages.btns.edit') . '">
            //         <i class="far fa-edit"></i>
            //     </a>
            //     <span id="delete||' . $item->id . '" onclick="DeleteCity(' . $item->id . ');" class="text-danger fs-6 cursor-pointer ml-1" data-toggle="tooltip" title="' . __('messages.btns.delete') . '">
            //         <i class="far fa-trash-alt"></i>
            //     </span>';
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

    public function view($id)
    {
        $demand = Demand::join('users', 'users.id', 'demands.seller_id')
            ->where('demands.id', $id)
            ->select(
                'demands.*',
                'users.name as seller_name',
            )
            ->first();

        $demandDetails = DemandDetail::join('products', 'products.id', 'demand_details.product_id')
            ->where('demand_details.demand_id', $id)
            ->select(
                'demand_details.*',
                'products.name'
            )
            ->get();

        return view('admin.demands.view', compact('demand', 'demandDetails'));
    }
}
