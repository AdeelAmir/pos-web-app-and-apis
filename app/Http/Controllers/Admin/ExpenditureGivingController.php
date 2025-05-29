<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expenditure;
use Illuminate\Http\Request;

class ExpenditureGivingController extends Controller
{
    public function index()
    {
        return view('admin.expenditure.giving.index');
    }

    public function load(Request $request)
    {
        $limit = $request->post('length');
        $start = $request->post('start');
        // $searchTerm = $request->post('searchTerm');
        $searchTerm = $request['searchTerm'];
        $seller_id = $request['seller_id'];

        $columnIndex = $request->post('order')[0]['column']; // Column index
        $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc


        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;

        if ($searchTerm == '') {
            // Fetch data without search filter
            $fetch_data = Expenditure::orderBy($columnName, $columnSortOrder);
            if ($limit == -1) {
                $fetch_data = $fetch_data->get();
            } else {
                $fetch_data = $fetch_data
                    ->offset($start)
                    ->limit($limit)
                    ->get();
            }

            $recordsTotal = Expenditure::orderBy($columnName, $columnSortOrder)
                ->count();

            $recordsFiltered = $recordsTotal;
        } else {
            // Fetch data with search filter
            $fetch_data = Expenditure::where(function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', '%' . $searchTerm . '%');
            })
                ->orderBy($columnName, $columnSortOrder);

            if ($limit == -1) {
                $fetch_data = $fetch_data->get();
            } else {
                $fetch_data = $fetch_data
                    ->offset($start)
                    ->limit($limit)
                    ->get();
            }

            $recordsTotal = Expenditure::where(function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', '%' . $searchTerm . '%');
            })
                ->orderBy($columnName, $columnSortOrder)
                ->count();

            $recordsFiltered = $recordsTotal;
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $Edit = route('expenditure.giving.edit', array($item->id));
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['name'] = $item->name;
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

    function add()
    {
        return view('admin.expenditure.giving.add');
    }

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'expenditure_name' => 'required',
        ]);

        $affected = null;

        $expenditure = new Expenditure();
        $expenditure->name = $request->expenditure_name;
        $affected = $expenditure->save();

        if ($affected) {
            return redirect()->route('expenditure.giving')->with('success-message', 'Expenditure added successfully');
        } else {
            return redirect()->route('expenditure.giving')->with('error-message', 'An unhandled error occurred');
        }
    }

    public function edit($id)
    {
        // Fetch the expenditure record by ID
        $expenditure = Expenditure::findOrFail($id);

        // Return the view with the expenditure data
        return view('admin.expenditure.giving.edit', compact('expenditure'));
    }

    public function update(Request $request)
    {

        $validatedData = $request->validate([
            'expenditure_name' => 'required',
        ]);

        $expenditure = Expenditure::whereId($request->id)->first();
        $expenditure->name = $request->expenditure_name;
        $Affected = $expenditure->save();

        if ($Affected) {
            return redirect()->route('expenditure.giving')->with('success-message', 'Expenditure updated successfully');
        } else {
            return redirect()->route('expenditure.giving')->with('error-message', 'An unhandled error occurred');
        }
    }
}
