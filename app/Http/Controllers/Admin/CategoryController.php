<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.categories.index');
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
            $fetch_data = Category::whereNull('deleted_at')
                ->select('*')
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
            $recordsFiltered = Category::whereNull('deleted_at')
                ->select('*')
                ->orderBy('name', 'asc')
                ->count();
        } else {
            $fetch_data = Category::where(function ($query) {
                $query->where([
                    ['deleted_at', '=', null]
                ]);
            })
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('*')
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
            $recordsFiltered = Category::where(function ($query) use ($searchTerm) {
                $query->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
            })
                ->select('*')
                ->orderBy('name', 'asc')
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $Edit = route('categories.edit', array($item->id));
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['name'] = $item->name;
            $sub_array['icon'] = '<img src="' . $item->icon . '" alt="Image" class="img-fluid rounded_circle" width="100px" height="100px">';
            $sub_array['action'] = '
                <a href="' . $Edit . '" class="text-primary fs-6 mr-1" data-toggle="tooltip" title="' . __('messages.btns.edit') . '">
                    <i class="far fa-edit"></i>
                </a>
                <span id="delete||' . $item->id . '" onclick="DeleteCategory(' . $item->id . ');" class="text-danger fs-6 cursor-pointer ml-1" data-toggle="tooltip" title="' . __('messages.btns.delete') . '">
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
        return view('admin.categories.add');
    }

    function store(Request $request)
    {
        $IconImage = "";
        if ($request->has('icon')) {
            $IconImage = 'icon_' . Carbon::now()->format('Ymd-His') . '.' . $request->file('icon')->extension();
            $request->file('icon')->storeAs('public/categories/', $IconImage);
        }

        $category = new Category();
        $category->name = $request->name;
        $category->icon = $IconImage;
        $Affected = $category->save();

        if ($Affected) {
            return redirect()->route('categories')->with('success-message', 'Category added successfully');
        } else {
            return redirect()->route('categories')->with('error-message', 'An unhandled error occurred');
        }
    }

    function edit($id)
    {
        $category = Category::whereId($id)->first();
        return view('admin.categories.edit', compact('category'));
    }

    function update(Request $request)
    {
        $IconImage = null;
        if ($request->hasFile('icon')) {
            if ($request['oldIcon'] != '') {
                $explodedOldIcon = explode('/', $request->oldIcon);
                $oldIcon = end($explodedOldIcon);
                $path = public_path('storage/categories') . '/' . $oldIcon;
                // Unlink the old file if it exists
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $IconImage = 'icon_' . Carbon::now()->format('Ymd-His') . '.' . $request->file('icon')->extension();
            $request->file('icon')->storeAs('public/categories/', $IconImage);
        } else {
            $explodedOldIcon = explode('/', $request->oldIcon);
            $IconImage = end($explodedOldIcon);
        }
        Category::where('id', $request->id)->update(['name' => $request->name, 'icon' => $IconImage]);
        return redirect()->route('categories')->with('Success message');
    }

    function delete(Request $request)
    {
        $category = Category::whereId($request->id)->first();
        if (!empty($category->icon)) {
            $explodedIcon = explode('/', $category->icon);
            $icon = end($explodedIcon);
            $path = public_path('storage/categories') . '/' . $icon;
            // Unlink the old file if it exists
            if (file_exists($path)) {
                unlink($path);
            }
        }
        Category::whereId($request->id)->delete();
        return response()->json(['success' => true]);
    }
}
