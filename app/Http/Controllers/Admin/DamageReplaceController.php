<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\DamageReplace;
use App\Models\DamageReplaceItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DamageReplaceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.damage-replace.index');
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
            $fetch_data = DamageReplace::join('users', 'users.id', 'damage_replaces.seller_id')
                ->select(
                    'damage_replaces.*',
                    'users.id AS user_id',
                    'users.name',
                    DB::raw('(SELECT SUM(damage_replace_items.quantity) FROM damage_replace_items WHERE damage_replace_items.damage_id = damage_replaces.id) as total_quantity')
                )
                ->orderBy($columnName, 'asc');
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
            $recordsFiltered = DamageReplace::join('users', 'users.id', 'damage_replaces.seller_id')
                ->select(
                    'damage_replaces.*',
                    'users.id AS user_id',
                    'users.name',
                    DB::raw('(SELECT SUM(damage_replace_items.quantity) FROM damage_replace_items WHERE damage_replace_items.damage_id = damage_replaces.id) as total_quantity')
                )
                ->orderBy($columnName, 'asc')
                ->count();
        } else {
            $fetch_data = DamageReplace::join('users', 'users.id', 'damage_replaces.seller_id')
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('users.name', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('users.id', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select(
                    'damage_replaces.*',
                    'users.id AS user_id',
                    'users.name',
                    DB::raw('(SELECT SUM(damage_replace_items.quantity) FROM damage_replace_items WHERE damage_replace_items.damage_id = damage_replaces.id) as total_quantity')
                )
                ->orderBy($columnName, 'asc');

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
            $recordsFiltered = DamageReplace::join('users', 'users.id', 'damage_replaces.seller_id')
                ->where(function ($query) use ($searchTerm) {
                    $query->orWhere('users.name', 'LIKE', '%' . $searchTerm . '%');
                    $query->orWhere('users.id', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select(
                    'damage_replaces.*',
                    'users.id AS user_id',
                    'users.name',
                    DB::raw('(SELECT SUM(damage_replace_items.quantity) FROM damage_replace_items WHERE damage_replace_items.damage_id = damage_replaces.id) as total_quantity')
                )
                ->orderBy($columnName, 'asc')
                ->count();
        }

        $data = array();
        $SrNo = $start + 1;
        foreach ($fetch_data as $row => $item) {
            $Edit = route('damage_replace.edit', array($item->id));
            $sub_array = array();
            $sub_array['date'] = Carbon::parse($item->date)->format('d/m/Y');
            $sub_array['seller_id_name'] = $item->name . '/' . $item->user_id;
            $sub_array['quantity'] = $item->total_quantity;
            $sub_array['boxes'] = $item->total_quantity;
            $sub_array['grand_total'] = SiteHelper::settings()['Currency_Icon'] . $item->grand_total;
            // $sub_array['action'] = '
            //     <span onclick="ViewProduct(' . $item->id . ',' . htmlspecialchars(json_encode($item->image), ENT_QUOTES, 'UTF-8') . ',' . htmlspecialchars(json_encode($item->name), ENT_QUOTES, 'UTF-8') . ',' . htmlspecialchars(json_encode($item->category_name), ENT_QUOTES, 'UTF-8') . ',' . $item->stock . ',' . $item->retail_price . ',' . $item->wholesale_price . ',' . $item->extra_price . ',' . $item->box . ',' . htmlspecialchars(json_encode($item->description), ENT_QUOTES, 'UTF-8') . ')" class="text-secondary cursor-pointer fs-6 me-1" data-toggle="tooltip" title="View">
            //         <i class="far fa-eye"></i>
            //     </span>
            //     <a href="' . $Edit . '" class="text-primary fs-6 mr-1" data-toggle="tooltip" title="Edit">
            //         <i class="fas fa-edit"></i>
            //     </a>';
            $sub_array['action'] = '
                <a href="' . $Edit . '" class="text-primary fs-6 mr-1" data-toggle="tooltip" title="Edit">
                    <i class="fas fa-edit"></i>
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

    public function add()
    {
        $sellers = User::where('role', 'seller')->get();
        $cities = City::where('type', 'Warehouse')->get();
        return view('admin.damage-replace.add', compact('sellers', 'cities'));
    }

    function store(Request $request)
    {
        $affected = null;

        if ($request->grand_total != 0) {
            $damage = new DamageReplace();
            $damage->date = Carbon::parse($request->date)->format('Y-m-d');
            $damage->seller_id = $request->seller_id;
            $damage->city_id = $request->city_id;
            $damage->grand_total = $request->grand_total;
            $damage->status = 'Completed';
            $affected = $damage->save();
        }

        if ($request->jsonProducts != '') {
            $products = json_decode($request->jsonProducts, true);
            foreach ($products as $product) {
                if ($product['quantity'] != 0) {
                    $damageItem = new DamageReplaceItem();
                    $damageItem->damage_id = $damage->id;
                    $damageItem->product_id = $product['id'];
                    $damageItem->retail_price = $product['price'];
                    $damageItem->quantity = $product['quantity'];
                    $damageItem->sub_total = $product['sub_total'];
                    $damageItem->save();
                }
            }
        }

        if ($affected) {
            return redirect()->route('damage_replace')->with('success-message', 'Damage/Replace added successfully');
        } else {
            return redirect()->route('damage_replace')->with('error-message', 'An unhandled error occurred');
        }
    }

    function edit($id)
    {
        $sellers = User::where('role', 'seller')->get();
        $cities = City::where('type', 'Warehouse')->get();
        $damage = DamageReplace::whereId($id)->first();
        $damageItems = DamageReplaceItem::join('products', 'products.id', 'damage_replace_items.product_id')
            ->select(
                'damage_replace_items.*',
                'products.id as product_id',
                'products.name',
                'products.retail_price',
                'products.wholesale_price',
                'products.extra_price',
                'products.pieces_in_box',
                DB::raw(
                    '(
                        SELECT 
                            COALESCE(SUM(stocks.stock), 0) - COALESCE(
                                (
                                    SELECT SUM(sale_details.quantity)
                                    FROM sale_details
                                    INNER JOIN sales ON sales.id = sale_details.sale_id
                                    WHERE sale_details.product_id = products.id
                                    AND (sales.bonus = 0 OR (sales.bonus = 1 AND sales.status = "Completed"))
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
            ->where('damage_id', $id)
            ->distinct()
            ->get();

        $array = [];
        foreach ($damageItems as $value) {
            $value->pieces = (!empty($value->total_stock) && !empty($value->pieces_in_box)) ? SiteHelper::makeBoxes($value->total_stock, $value->pieces_in_box) : 0;
            $array[] = [
                'id' => $value->product_id,
                'name' => $value->name,
                'price' => $value->retail_price,
                'wholesale_price' => $value->wholesale_price,
                'extra_price' => $value->extra_price,
                'stock' => $value->total_stock,
                'pieces' => (!empty($value->total_stock) && !empty($value->pieces_in_box)) ? SiteHelper::makeBoxes($value->total_stock, $value->pieces_in_box) : 0,
                'quantity' => $value->quantity,
                'sub_total' => $value->sub_total
            ];
        }

        $jsonData = json_encode($array);

        return view('admin.damage-replace.edit', compact('sellers', 'cities', 'damage', 'damageItems', 'jsonData'));
    }

    function update(Request $request)
    {
        $affected = null;

        $damage = DamageReplace::whereId($request->id)->first();
        $damage->date = Carbon::parse($request->date)->format('Y-m-d');
        $damage->seller_id = $request->seller_id;
        $damage->city_id = $request->city_id;
        $damage->grand_total = $request->grand_total;
        $damage->status = 'Completed';
        $affected = $damage->save();

        if ($request->jsonProducts != '') {
            $damageItem = DamageReplaceItem::where('damage_id', $request->id)->delete();
            $products = json_decode($request->jsonProducts, true);
            foreach ($products as $product) {
                if ($product['quantity'] != 0) {
                    $damageItem = new DamageReplaceItem();
                    $damageItem->damage_id = $damage->id;
                    $damageItem->product_id = $product['id'];
                    $damageItem->retail_price = $product['price'];
                    $damageItem->quantity = $product['quantity'];
                    $damageItem->sub_total = $product['sub_total'];
                    $damageItem->save();
                }
            }
        }

        if ($affected) {
            return redirect()->route('damage_replace')->with('success-message', 'Damage/Replace updated successfully');
        } else {
            return redirect()->route('damage_replace')->with('error-message', 'An unhandled error occurred');
        }
    }

    function delete(Request $request)
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
                                    AND (sales.bonus = 0 OR (sales.bonus = 1 AND sales.status = "Completed"))
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

        foreach ($products as &$product) {
            $product['pieces'] = (!empty($product['total_stock']) && !empty($product['pieces_in_box'])) ? SiteHelper::makeBoxes($product['total_stock'], $product['pieces_in_box']) : 0;
        }
        return json_encode($products);
    }
}
