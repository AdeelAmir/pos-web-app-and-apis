<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\SellerTarget;
use App\Models\SellerTargetDetails;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OfficeSellersController extends Controller
{
    public function index()
    {
        return view('admin.office-sellers.index');
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
            $fetch_data = User::where('deleted_at', '=', null)
                ->where('role', 'office seller')
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
                ->where('role', 'office seller')
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
                ->where('role', 'office seller')
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
                ->where('role', 'office seller')
                ->select('users.*')
                ->orderBy($columnName, $columnSortOrder)
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $View = route('sellers.office.view', array($item->id));
            $Edit = route('sellers.office.edit', array($item->id));
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['seller_id'] = $item->id;
            $sub_array['profile'] = $item->profile_image != '' ? '<img src="' . $item->profile_image . '" alt="Image" class="img-fluid rounded_circle" width="100px" height="100px">' : '';
            $sub_array['name'] = $item->name;
            $sub_array['email'] = $item->email;
            $sub_array['phone'] = $item->phone;
            if ($item->status == 0) {
                $sub_array['status'] = '<span id="' . $item->status . '||' . $item->id . '" onclick="changeOfficeSellerStatus(this.id)" class="btn-sm btn-danger cursor-pointer">Ban</span>';
            } else if ($item->status == 1) {
                $sub_array['status'] = '<span id="' . $item->status . '||' . $item->id . '" onclick="changeOfficeSellerStatus(this.id)" class="btn-sm btn-success cursor-pointer">Active</span>';
            }
            $sub_array['action'] = '
                <a href="' . $View . '" class="text-secondary fs-6 me-1" data-toggle="tooltip" title="' . __('messages.btns.view') . '">
                    <i class="far fa-eye"></i>
                </a>
                <a href="' . $Edit . '" class="text-primary fs-6 me-1" data-toggle="tooltip" title="' . __('messages.btns.edit') . '">
                    <i class="far fa-edit"></i>
                </a>
                <span id="delete||' . $item->id . '" onclick="DeleteOfficeSellers(' . $item->id . ');" class="text-danger fs-6 cursor-pointer ml-1" data-toggle="tooltip" title="' . __('messages.btns.delete') . '">
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
        return view('admin.office-sellers.add', compact('incrementedId'));
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
        // $user->password = Hash::make($request->password);
        // $user->phone = $request->phone;
        $user->profile_image = $profileImage;
        $user->description = $request->description;
        $user->role = 'office seller';
        $affected = $user->save();

        if ($affected) {
            return redirect()->route('sellers.office')->with('success-message', 'Office seller added successfully');
        } else {
            return redirect()->route('sellers.office')->with('error-message', 'An unhandled error occurred');
        }
    }

    function edit($id)
    {
        $seller = User::whereId($id)->first();
        return view('admin.office-sellers.edit', compact('seller'));
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
        // if ($request->password != '') {
        //     $user->password = Hash::make($request->password);
        // }
        $user->profile_image = $profileImage;
        $affected = $user->save();

        if ($affected) {
            return redirect()->route('sellers.office')->with('success-message', 'Office seller updated successfully');
        } else {
            return redirect()->route('sellers.office')->with('error-message', 'An unhandled error occurred');
        }
    }

    function view($id)
    {
        $seller = User::whereId($id)->first();
        return view('admin.office-sellers.view', compact('seller'));
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
}
