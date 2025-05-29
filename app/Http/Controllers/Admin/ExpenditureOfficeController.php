<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\Expenditure;
use App\Models\OfficeExpenditure;
use App\Models\OfficeExpenditureDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenditureOfficeController extends Controller
{
    public function index()
    {
        return view('admin.expenditure.office.index');
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
            $fetch_data = OfficeExpenditure::join('office_expenditure_details', 'office_expenditures.id', 'office_expenditure_details.office_expenditure_id')
                ->select('office_expenditures.id', 'office_expenditures.expenditure_date', DB::raw('SUM(office_expenditure_details.amount) as total_amount'))
                ->where('office_expenditures.type', 'Office')
                ->groupBy('office_expenditures.id', 'office_expenditures.expenditure_date')
                ->orderBy('office_expenditures.id', 'asc');
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
            $recordsFiltered = OfficeExpenditure::join('office_expenditure_details', 'office_expenditures.id', 'office_expenditure_details.office_expenditure_id')
                ->select('office_expenditures.id', 'office_expenditures.expenditure_date', DB::raw('SUM(office_expenditure_details.amount) as total_amount'))
                ->where('office_expenditures.type', 'Office')
                ->groupBy('office_expenditures.id', 'office_expenditures.expenditure_date')
                ->orderBy('office_expenditures.id', 'asc')
                ->count();
        } else {
            $fetch_data = OfficeExpenditure::join('office_expenditure_details', 'office_expenditures.id', 'office_expenditure_details.office_expenditure_id')
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('office_expenditures.expenditure_date', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('office_expenditures.id', 'office_expenditures.expenditure_date', DB::raw('SUM(office_expenditure_details.amount) as total_amount'))
                ->groupBy('office_expenditures.id')
                ->orderBy('office_expenditures.id', 'asc');
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
            $recordsFiltered = OfficeExpenditure::join('office_expenditure_details', 'office_expenditures.id', 'office_expenditure_details.office_expenditure_id')
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('office_expenditures.expenditure_date', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('office_expenditures.id', 'office_expenditures.expenditure_date', DB::raw('SUM(office_expenditure_details.amount) as total_amount'))
                ->groupBy('office_expenditures.id')
                ->orderBy('office_expenditures.id', 'asc')
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $Edit = route('expenditure.office.edit', array($item->id));
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['date'] = Carbon::parse($item->expenditure_date)->format('d-m-Y');
            $sub_array['expenditure_amount'] = SiteHelper::settings()['Currency_Icon'] . $item->total_amount;
            $sub_array['action'] = '
                <a href="' . $Edit . '" class="text-primary fs-6 mr-1" data-toggle="tooltip" title="' . __('messages.btns.edit') . '">
                    <i class="far fa-edit"></i>
                </a>
                <span id="delete||' . $item->id . '" onclick="DeleteExpenditureOffice(' . $item->id . ');" class="text-danger fs-6 cursor-pointer ml-1" data-toggle="tooltip" title="' . __('messages.btns.delete') . '">
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
        $expenditures = Expenditure::get();
        return view('admin.expenditure.office.add', compact('expenditures'));
    }

    function store(Request $request)
    {
        DB::beginTransaction();
        $affected = null;
        $officeExpenditure = new OfficeExpenditure();
        $officeExpenditure->expenditure_date = $request['expenditure_date'];
        $officeExpenditure->type = 'Office';
        $affected = $officeExpenditure->save();
        if ($affected) {
            foreach ($request['expense_repeater'] as $index => $value) {
                $officeExpenditureDetail = new OfficeExpenditureDetail();
                $officeExpenditureDetail->office_expenditure_id = $officeExpenditure->id;
                $officeExpenditureDetail->expenditure_id = $value['expenditure_id'];
                $officeExpenditureDetail->amount = $value['amount'];
                $officeExpenditureDetail->save();
            }
            DB::commit();
            return redirect()->route('expenditure.office')->with('success-message', 'Office expenditure added successfully');
        } else {
            DB::rollBack();
            return redirect()->route('expenditure.office')->with('error-message', 'An unhandled error occurred');
        }
    }

    function edit($id)
    {
        $expenditures = Expenditure::get();
        $officeExpenditure = OfficeExpenditure::whereId($id)->where('type', 'Office')->first();
        $officeExpenditureDetails = OfficeExpenditureDetail::where('office_expenditure_id', $id)->get();
        return view('admin.expenditure.office.edit', compact('expenditures', 'officeExpenditure', 'officeExpenditureDetails'));
    }

    function update(Request $request)
    {
        DB::beginTransaction();
        $affected = null;
        $officeExpenditure = OfficeExpenditure::whereId($request->id)->first();
        $officeExpenditure->expenditure_date = $request['expenditure_date'];
        // $officeExpenditure->type = 'Office';
        $affected = $officeExpenditure->save();
        if ($affected) {
            $officeExpenditureDetail = OfficeExpenditureDetail::where('office_expenditure_id', $request->id)->delete();
            foreach ($request['expense_repeater'] as $index => $value) {
                $officeExpenditureDetail = new OfficeExpenditureDetail();
                $officeExpenditureDetail->office_expenditure_id = $officeExpenditure->id;
                $officeExpenditureDetail->expenditure_id = $value['expenditure_id'];
                $officeExpenditureDetail->amount = $value['amount'];
                $officeExpenditureDetail->save();
            }
            DB::commit();
            return redirect()->route('expenditure.office')->with('success-message', 'Office expenditure added successfully');
        } else {
            DB::rollBack();
            return redirect()->route('expenditure.office')->with('error-message', 'An unhandled error occurred');
        }
    }

    function delete(Request $request)
    {
        OfficeExpenditure::whereId($request->id)->delete();
        OfficeExpenditureDetail::where('office_expenditure_id', $request->id)->delete();
        return response()->json(['success' => true]);
    }
}
