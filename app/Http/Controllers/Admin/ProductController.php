<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\City;
use App\Models\Product;
use App\Models\Returns;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cities = City::where('type', 'Warehouse')->get();
        return view('admin.products.index', compact('cities'));
    }

    public function load(Request $request)
    {
        $limit = $request->post('length');
        $start = $request->post('start');
        // $searchTerm = $request->post('search')['value'];
        $searchTerm = $request['searchTerm'];
        $cities = $request['city'];

        $columnIndex = $request->post('order')[0]['column']; // Column index
        $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;
        if ($searchTerm == '') {
            $fetch_data = Product::join('categories', 'products.category_id', 'categories.id')
                ->join('cities', 'cities.id', 'products.city_id')
                ->where(function ($query) use ($cities) {
                    if ($cities != '') {
                        $query->whereIn('products.city_id', $cities);
                    }
                })
                ->whereNull('products.deleted_at')
                ->select(
                    'products.*',
                    'categories.name AS category_name',
                    'cities.name AS city_name',
                    DB::raw(
                        '(
                            COALESCE(
                                (
                                    SELECT SUM(stocks.stock)
                                    FROM stocks
                                    WHERE stocks.product_id = products.id
                                ), 
                                0
                            ) 
                            - COALESCE(
                                (
                                    SELECT SUM(sale_details.quantity)
                                    FROM sale_details
                                    INNER JOIN sales ON sales.id = sale_details.sale_id
                                    WHERE sale_details.product_id = products.id
                                    AND (sales.bonus = 0 OR (sales.bonus = 1 AND sales.status = "Completed"))
                                ), 
                                0
                            ) 
                            - COALESCE(
                                (
                                    SELECT SUM(damage_replace_items.quantity)
                                    FROM damage_replace_items
                                    INNER JOIN damage_replaces ON damage_replaces.id = damage_replace_items.damage_id
                                    WHERE damage_replace_items.product_id = products.id
                                ),
                                0
                            ) 
                            - COALESCE(
                                (
                                    SELECT SUM(return_details.return_quantity)
                                    FROM returns
                                    INNER JOIN return_details ON returns.id = return_details.return_id
                                    WHERE return_details.product_id = products.id
                                    AND returns.to_city_id IS NOT NULL
                                ), 
                                0
                            )
                        ) AS total_stock'
                    )
                )
                ->orderBy('products.name', 'asc');
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
            $recordsFiltered = Product::join('categories', 'products.category_id', 'categories.id')
                ->join('cities', 'cities.id', 'products.city_id')
                ->where(function ($query) use ($cities) {
                    if ($cities != '') {
                        $query->whereIn('products.city_id', $cities);
                    }
                })
                ->whereNull('products.deleted_at')
                ->select(
                    'products.*',
                    'categories.name AS category_name',
                    'cities.name AS city_name',
                    DB::raw(
                        '(
                            COALESCE(
                                (
                                    SELECT SUM(stocks.stock)
                                    FROM stocks
                                    WHERE stocks.product_id = products.id
                                ), 
                                0
                            ) 
                            - COALESCE(
                                (
                                    SELECT SUM(sale_details.quantity)
                                    FROM sale_details
                                    INNER JOIN sales ON sales.id = sale_details.sale_id
                                    WHERE sale_details.product_id = products.id
                                    AND (sales.bonus = 0 OR (sales.bonus = 1 AND sales.status = "Completed"))
                                ), 
                                0
                            ) 
                            - COALESCE(
                                (
                                    SELECT SUM(damage_replace_items.quantity)
                                    FROM damage_replace_items
                                    INNER JOIN damage_replaces ON damage_replaces.id = damage_replace_items.damage_id
                                    WHERE damage_replace_items.product_id = products.id
                                ),
                                0
                            ) 
                            - COALESCE(
                                (
                                    SELECT SUM(return_details.return_quantity)
                                    FROM returns
                                    INNER JOIN return_details ON returns.id = return_details.return_id
                                    WHERE return_details.product_id = products.id
                                    AND returns.to_city_id IS NOT NULL
                                ), 
                                0
                            )
                        ) AS total_stock'
                    )
                )
                ->orderBy('products.name', 'asc')
                ->count();
        } else {
            $fetch_data = Product::join('categories', 'products.category_id', 'categories.id')
                ->join('cities', 'cities.id', 'products.city_id')
                ->where(function ($query) use ($cities) {
                    if ($cities != '') {
                        $query->whereIn('products.city_id', $cities);
                    }
                })
                ->where(function ($query) {
                    $query->where([
                        ['products.deleted_at', '=', null]
                    ]);
                })
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('products.name', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('products.retail_price', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('products.wholesale_price', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select(
                    'products.*',
                    'categories.name AS category_name',
                    'cities.name AS city_name',
                    DB::raw(
                        '(
                            COALESCE(
                                (
                                    SELECT SUM(stocks.stock)
                                    FROM stocks
                                    WHERE stocks.product_id = products.id
                                ), 
                                0
                            ) 
                            - COALESCE(
                                (
                                    SELECT SUM(sale_details.quantity)
                                    FROM sale_details
                                    INNER JOIN sales ON sales.id = sale_details.sale_id
                                    WHERE sale_details.product_id = products.id
                                    AND (sales.bonus = 0 OR (sales.bonus = 1 AND sales.status = "Completed"))
                                ), 
                                0
                            ) 
                            - COALESCE(
                                (
                                    SELECT SUM(damage_replace_items.quantity)
                                    FROM damage_replace_items
                                    INNER JOIN damage_replaces ON damage_replaces.id = damage_replace_items.damage_id
                                    WHERE damage_replace_items.product_id = products.id
                                ),
                                0
                            ) 
                            - COALESCE(
                                (
                                    SELECT SUM(return_details.return_quantity)
                                    FROM returns
                                    INNER JOIN return_details ON returns.id = return_details.return_id
                                    WHERE return_details.product_id = products.id
                                    AND returns.to_city_id IS NOT NULL
                                ), 
                                0
                            )
                        ) AS total_stock'
                    )
                )
                ->orderBy('products.name', 'asc');
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
            $recordsFiltered = Product::join('categories', 'products.category_id', 'categories.id')
                ->join('cities', 'cities.id', 'products.city_id')
                ->where(function ($query) use ($cities) {
                    if ($cities != '') {
                        $query->whereIn('products.city_id', $cities);
                    }
                })
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('products.name', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('products.retail_price', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('products.wholesale_price', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select(
                    'products.*',
                    'categories.name AS category_name',
                    'cities.name AS city_name',
                    DB::raw(
                        '(
                            COALESCE(
                                (
                                    SELECT SUM(stocks.stock)
                                    FROM stocks
                                    WHERE stocks.product_id = products.id
                                ), 
                                0
                            ) 
                            - COALESCE(
                                (
                                    SELECT SUM(sale_details.quantity)
                                    FROM sale_details
                                    INNER JOIN sales ON sales.id = sale_details.sale_id
                                    WHERE sale_details.product_id = products.id
                                    AND (sales.bonus = 0 OR (sales.bonus = 1 AND sales.status = "Completed"))
                                ), 
                                0
                            ) 
                            - COALESCE(
                                (
                                    SELECT SUM(damage_replace_items.quantity)
                                    FROM damage_replace_items
                                    INNER JOIN damage_replaces ON damage_replaces.id = damage_replace_items.damage_id
                                    WHERE damage_replace_items.product_id = products.id
                                ),
                                0
                            ) 
                            - COALESCE(
                                (
                                    SELECT SUM(return_details.return_quantity)
                                    FROM returns
                                    INNER JOIN return_details ON returns.id = return_details.return_id
                                    WHERE return_details.product_id = products.id
                                    AND returns.to_city_id IS NOT NULL
                                ), 
                                0
                            )
                        ) AS total_stock'
                    )
                )
                ->orderBy('products.name', 'asc')
                ->count();
        }

        $exchangeCityProducts = Returns::join('return_details', 'return_details.return_id', 'returns.id')
            ->join('products', 'products.id', 'return_details.product_id')
            ->join('cities as A', 'A.id', 'products.city_id')
            ->join('cities as B', 'B.id', 'returns.to_city_id')
            ->select(
                'products.*',
                'A.name AS city_name',
                'B.name AS to_city_name',
                DB::raw(
                    '(
                            COALESCE(
                                (
                                    SUM(return_details.return_quantity)
                                ), 
                                0
                            ) 
                            -
                            COALESCE(
                                (
                                    SELECT SUM(sale_details.quantity)
                                    FROM sales
                                    INNER JOIN sale_details ON sales.id = sale_details.sale_id
                                    WHERE sale_details.product_id = products.id
                                    AND sales.city_id = returns.to_city_id
                                    AND (sales.bonus = 0 OR (sales.bonus = 1 AND sales.status = "Completed"))
                                ), 
                                0
                            )
                        ) AS total_stock'
                )
            )
            ->groupBy('return_details.product_id')
            ->get();

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $totalStock = $item->total_stock;
            foreach ($exchangeCityProducts as $exProduct) {
                if ($item->city_name == $exProduct->to_city_name && $item->name == $exProduct->name) {
                    $totalStock = (int) $item->total_stock + (int) $exProduct->total_stock;
                }
            }

            $Edit = route('products.edit', array($item->id));
            $StockView = route('products.stock.view', array($item->id));
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['name'] = $item->name;
            $sub_array['category'] = $item->category_name;
            $sub_array['city'] = $item->city_name;
            $sub_array['stock'] = $totalStock;
            $sub_array['boxes'] = (!empty($totalStock) && !empty($item->pieces_in_box)) ? SiteHelper::makeBoxes($totalStock, $item->pieces_in_box) : 0;
            $sub_array['gen_price'] = SiteHelper::settings()['Currency_Icon'] . $item->retail_price;
            $sub_array['whole_price'] = SiteHelper::settings()['Currency_Icon'] . $item->wholesale_price;
            $sub_array['action'] = '
                <span onclick="ViewProduct(' . $item->id . ', ' . htmlspecialchars(json_encode($item->image), ENT_QUOTES, 'UTF-8') . ', \'' . addslashes($item->name) . '\', \'' . addslashes($item->category_name) . '\', ' . $item->retail_price . ', ' . $item->wholesale_price . ', ' . $item->extra_price . ', \'' . addslashes($item->description) . '\')" class="text-secondary cursor-pointer fs-6 me-1" data-toggle="tooltip" title="' . __('messages.btns.view') . '">
                    <i class="far fa-eye"></i>
                </span>
                <span onclick="OpenProductStock(' . $item->id . ')" class="text-secondary cursor-pointer fs-6 me-1" data-toggle="tooltip" title="' . __('messages.btns.add_stock') . '">
                    <i class="fa fa-box"></i>
                </span>
                <a href="' . $StockView . '" class="text-secondary cursor-pointer fs-6 me-1" data-toggle="tooltip" title="' . __('messages.btns.view_stock') . '">
                    <i class="fa fa-boxes"></i>
                </a>
                <a href="' . $Edit . '" class="text-primary fs-6 mr-1" data-toggle="tooltip" title="' . __('messages.btns.edit') . '">
                    <i class="fas fa-edit"></i>
                </a>
                <span id="delete||' . $item->id . '" onclick="DeleteProduct(' . $item->id . ');" class="text-danger fs-6 cursor-pointer ml-1" data-toggle="tooltip" title="' . __('messages.btns.delete') . '">
                    <i class="fas fa-trash-alt"></i>
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
        $categories = Category::get();
        $cities = City::where('type', 'Warehouse')->get();
        return view('admin.products.add', compact('categories', 'cities'));
    }

    public function store(Request $request)
    {
        $Image = "";
        if ($request->has('image')) {
            $Image = 'product-image_' . Carbon::now()->format('Ymd-His') . '.' . $request->file('image')->extension();
            $request->file('image')->storeAs('public/products/', $Image);
        }

        $Affected = null;

        $product = new Product();
        $product->category_id = $request->category_id;
        $product->city_id = $request->city_id;
        $product->name = $request->name;
        $product->image = $Image;
        $product->pieces_in_box = $request->pieces_in_box;
        $product->retail_price = $request->retail_price;
        $product->wholesale_price = $request->wholesale_price;
        $product->extra_price = $request->extra_price;
        $product->description = $request->description;
        $Affected = $product->save();

        if ($Affected) {
            return redirect()->route('products')->with('success-message', 'Product added successfully');
        } else {
            return redirect()->route('products')->with('error-message', 'An unhandled error occurred');
        }
    }

    public function edit($id)
    {
        $product = Product::whereId($id)->first();
        $cities = City::where('type', 'Warehouse')->get();
        $categories = Category::get();
        return view('admin.products.edit', compact('product', 'categories', 'cities'));
    }

    public function update(Request $request)
    {
        $Image = $request['oldImage'];
        if ($request->hasFile('image')) {
            if ($request['oldImage'] != '') {
                $explodedOldImage = explode('/', $request->oldImage);
                $oldImage = end($explodedOldImage);
                $path = public_path('storage/products') . '/' . $oldImage;
                // Unlink the old file if it exists
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $Image = 'product-image_' . Carbon::now()->format('Ymd-His') . '.' . $request->file('image')->extension();
            $request->file('image')->storeAs('public/products/', $Image);
        } else {
            $explodedOldImage = explode('/', $request->oldImage);
            $Image = end($explodedOldImage);
        }

        $Affected = null;

        $product = Product::whereId($request->product_id)->first();
        $product->category_id = $request->category_id;
        $product->city_id = $request->city_id;
        $product->name = $request->name;
        $product->image = $Image;
        $product->pieces_in_box = $request->pieces_in_box;
        $product->retail_price = $request->retail_price;
        $product->wholesale_price = $request->wholesale_price;
        $product->extra_price = $request->extra_price;
        $product->description = $request->description;
        $Affected = $product->save();

        if ($Affected) {
            return redirect()->route('products')->with('success-message', 'Product updated successfully');
        } else {
            return redirect()->route('products')->with('error-message', 'An unhandled error occurred');
        }
    }

    public function delete(Request $request)
    {
        $product = Product::whereId($request->id)->first();
        if (!empty($product->image)) {
            $explodedImage = explode('/', $product->image);
            $image = end($explodedImage);
            $path = public_path('storage/products') . '/' . $image;
            // Unlink the old file if it exists
            if (file_exists($path)) {
                unlink($path);
            }
        }
        Product::whereId($request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function addStock(Request $request)
    {
        $affected = null;

        $product = Product::whereId($request->product_id)->first();

        $stock = new Stock();
        $stock->product_id = $request->product_id;
        $stock->pieces = $product->pieces_in_box;
        $stock->box = $request->box;
        $stock->stock = $request->stock;
        $affected = $stock->save();

        if ($affected) {
            return redirect()->route('products')->with('success-message', 'Stocks added successfully');
        } else {
            return redirect()->route('products')->with('error-message', 'An unhandled error occurred');
        }
    }

    public function viewStock($id)
    {
        $product = Product::whereId($id)->first();
        return view('admin.products.view-stock', compact('product'));
    }

    public function loadStock(Request $request)
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
            $fetch_data = Stock::join('products', 'products.id', 'stocks.id')
                ->where('stocks.product_id', $request->product_id)
                ->select('stocks.*')
                ->orderBy('stocks.id', 'desc');
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
            $recordsFiltered = Stock::join('products', 'products.id', 'stocks.id')
                ->where('stocks.product_id', $request->product_id)
                ->select('stocks.*')
                ->orderBy('stocks.id', 'desc')
                ->count();
        } else {
            $fetch_data = Stock::join('products', 'products.id', 'stocks.id')
                // ->where(function ($query) {
                //     $query->where([
                //         ['products.deleted_at', '=', null]
                //     ]);
                // })
                // ->where(function ($query) use ($searchTerm) {
                //     $query->orWhere('products.name', 'LIKE', '%' . $searchTerm . '%');
                // })
                ->where('stocks.product_id', $request->product_id)
                ->select('stocks.*')
                ->orderBy('stocks.id', 'desc');
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
            $recordsFiltered = Stock::join('products', 'products.id', 'stocks.id')
                // ->where(function ($query) use ($searchTerm) {
                //     $query->orWhere('products.name', 'LIKE', '%' . $searchTerm . '%');
                // })
                ->where('stocks.product_id', $request->product_id)
                ->select('stocks.*')
                ->orderBy('stocks.id', 'desc')
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $sub_array = array();
            $sub_array['id'] = $SrNo;
            $sub_array['pieces'] = $item->pieces;
            $sub_array['box'] = $item->box;
            $sub_array['stock'] = $item->stock;
            $sub_array['created_at'] = Carbon::parse($item->created_at)->format('d-m-Y');
            $sub_array['action'] = '
                <span id="delete||' . $item->id . '" onclick="DeleteStock(' . $item->id . ');" class="text-danger fs-6 cursor-pointer ml-1" data-toggle="tooltip" title="Delete">
                    <i class="fas fa-trash-alt"></i>
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

    public function deleteStock(Request $request)
    {
        $stock = Stock::whereId($request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function getProductPieces(Request $request)
    {
        $product = Product::whereId($request->id)->first();
        if ($product != '') {
            return response()->json(['success' => true, 'pieces' => $product->pieces_in_box]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
