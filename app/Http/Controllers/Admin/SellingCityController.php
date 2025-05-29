<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellingCityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.selling-cities.index');
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

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;
        if ($searchTerm == '') {
            $fetch_data = City::where('type', 'Selling')
                ->orderBy('name', 'asc');
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
            $recordsFiltered = City::where('type', 'Selling')
                ->orderBy('name', 'asc')
                ->count();
        } else {
            $fetch_data = City::where('type', 'Selling')
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                })
                ->orderBy('name', 'asc');
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
            $recordsFiltered = City::where('type', 'Selling')
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                })
                ->orderBy('name', 'asc')
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $Edit = route('cities.selling.edit', array($item->id));
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['name'] = $item->name;
            $sub_array['action'] = '
                <a href="' . $Edit . '" class="text-primary fs-6 mr-1" data-toggle="tooltip" title="' . __('messages.btns.edit') . '">
                    <i class="far fa-edit"></i>
                </a>
                <span id="delete||' . $item->id . '" onclick="DeleteSellingCity(' . $item->id . ');" class="text-danger fs-6 cursor-pointer ml-1" data-toggle="tooltip" title="' . __('messages.btns.delete') . '">
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
        return view('admin.selling-cities.add');
    }

    function store(Request $request)
    {
        $Affected = null;

        $city = new City();
        $city->name = $request->name;
        $city->type = 'Selling';
        $Affected = $city->save();

        if ($Affected) {
            return redirect()->route('cities.selling')->with('success-message', 'City added successfully');
        } else {
            return redirect()->route('cities.selling')->with('error-message', 'An unhandled error occurred');
        }
    }

    function edit($id)
    {
        $city = City::whereId($id)->first();
        return view('admin.selling-cities.edit', compact('city'));
    }

    function update(Request $request)
    {
        City::whereId($request->id)->update(['name' => $request->name]);
        return redirect()->route('cities.selling')->with('success-message', 'City updated successfully');
    }

    function delete(Request $request)
    {
        City::whereId($request->id)->delete();
        return response()->json(['success' => true]);
    }
}
