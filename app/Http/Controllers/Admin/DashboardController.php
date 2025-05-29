<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\DamageReplace;
use App\Models\DamageReplaceItem;
use App\Models\Expenditure;
use App\Models\Order;
use App\Models\Product;
use App\Models\Returns;
use App\Models\Sale;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;

class DashboardController extends Controller
{
    function index()
    {
        $cities = City::where('type', 'Warehouse')->get();
        return view('admin.index', compact('cities'));
    }

    function topCards(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if ($startDate != '' && $endDate != '') {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        }

        $totalRevenue = Sale::where('status', 'Completed')
            ->where(function ($query) use ($request, $startDate, $endDate) {
                if ($startDate != '' && $endDate != '') {
                    $query->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                }
            })
            ->sum('grand_total');

        $totalProducts = Product::select(
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
                    ) AS total_stock'
            )
        )
            ->get();

        $totalStocks = 0;
        foreach ($totalProducts as $index => $value) {
            $totalStocks += $value->total_stock;
        }

        $totalSellers = User::where('role', 'seller')
            ->where(function ($query) use ($request, $startDate, $endDate) {
                if ($startDate != '' && $endDate != '') {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            })
            ->count();

        $totalUsers = User::where('role', 'user')
            ->where(function ($query) use ($request, $startDate, $endDate) {
                if ($startDate != '' && $endDate != '') {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            })
            ->count();

        $totalShops = Shop::where(function ($query) use ($request, $startDate, $endDate) {
            if ($startDate != '' && $endDate != '') {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        })
            ->count();

        $array = [
            'total_revenue' => !empty($totalRevenue) ? $totalRevenue : 0,
            'total_stocks' => !empty($totalStocks) ? $totalStocks : 0,
            'total_sellers' => !empty($totalSellers) ? $totalSellers : 0,
            'total_users' => !empty($totalUsers) ? $totalUsers : 0,
            'total_shops' => !empty($totalShops) ? $totalShops : 0,
        ];

        return response()->json($array);
    }

    public function sideRevenueCard(Request $request)
    {
        $todayRevenue = Sale::where('status', 'Completed')
            ->where('date', Carbon::now()->format('Y-m-d'))
            ->sum('grand_total');

        $weeklyRevenue = Sale::where('status', 'Completed')
            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('grand_total');

        $monthlyRevenue = Sale::where('status', 'Completed')
            ->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('grand_total');

        $annualRevenue = Sale::where('status', 'Completed')
            ->whereBetween('date', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])
            ->sum('grand_total');

        $array = [
            'total_revenue' => !empty($todayRevenue) ? $todayRevenue : 0,
            'weekly_revenue' => !empty($weeklyRevenue) ? $weeklyRevenue : 0,
            'monthly_revenue' => !empty($monthlyRevenue) ? $monthlyRevenue : 0,
            'annual_revenue' => !empty($annualRevenue) ? $annualRevenue : 0,
        ];

        return response()->json($array);
    }

    public function incomeChart(Request $request)
    {

        $startDate = Carbon::now();
        $endDate = Carbon::now();
        $daysArray = [];

        // Get the current year and month
        $year = date('Y');
        $month = date('m');

        if ($request->type != '' && $request->type == 'Weekly') {
            $startDate->startOfWeek();
            $endDate->endOfWeek();
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $daysArray[] = $date->format('d-m-Y');
            }
        } else if ($request->type != '' && $request->type == 'Daily') {
            $startDate->startOfDay();
            $endDate->endOfDay();
            $daysArray[] = Carbon::now()->format('d-m-Y');
        } else {
            $startDate->startOfMonth();
            $endDate->endOfMonth();

            // Get the number of days in the current month
            $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            // Create an array of days from 1 to the end of the month
            $daysArray = range(1, $numDays);
        }

        $incomeArray = array();

        $saleDetails = Sale::where('status', 'Completed')
            ->whereBetween('date', [$startDate, $endDate])
            ->select('date', 'grand_total')
            ->get();

        foreach ($daysArray as $d_index => $d_value) {
            $total_sale = 0;
            if ($request->type != '' && $request->type != 'Monthly') {
                $date_parse = Carbon::parse($d_value)->format('Y-m-d');
            } else {
                $month_date = $d_value . '-' . $month . '-' . $year;
                $date_parse = Carbon::parse($month_date)->format('Y-m-d');
            }
            foreach ($saleDetails as $index => $value) {
                $sale_date = Carbon::parse($value->date)->format('Y-m-d');
                if ($sale_date == $date_parse) {
                    $total_sale += $value->grand_total;
                }
            }
            array_push($incomeArray, $total_sale);
        }

        $array = array();
        $array['days'] = $daysArray;
        $array['day_wise_income'] = $incomeArray;

        return response()->json($array);
    }

    public function expenseChart(Request $request)
    {
        $startDate = Carbon::now();
        $endDate = Carbon::now();
        if ($request->type != '' && $request->type == 'Weekly') {
            $startDate->startOfWeek();
            $endDate->endOfWeek();
        } else if ($request->type != '' && $request->type == 'Monthly') {
            $startDate->startOfMonth();
            $endDate->endOfMonth();
        } else {
            $startDate->startOfDay();
            $endDate->endOfDay();
        }

        $expenses = Expenditure::join('office_expenditure_details', 'office_expenditure_details.expenditure_id', 'expenditures.id')
            ->leftJoin('office_expenditures', 'office_expenditures.id', 'office_expenditure_details.office_expenditure_id')
            ->select(
                'expenditures.id',
                'expenditures.name',
                DB::raw('SUM(office_expenditure_details.amount) as total_amount')
            )
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('office_expenditures.expenditure_date', [$startDate, $endDate]);
            })
            ->groupBy('expenditures.id')
            ->get();

        $array = array();
        foreach ($expenses as $index => $value) {
            $subArray = [];
            $subArray['id'] = $value->id;
            $subArray['name'] = $value->name;
            $subArray['total_amount'] = (float) $value->total_amount;
            array_push($array, $subArray);
        }

        return response()->json($array);
    }

    public function bestSellingProducts(Request $request)
    {
        $startDate = Carbon::now();
        $endDate = Carbon::now();
        if ($request->type != '' && $request->type == 'Weekly') {
            $startDate->startOfWeek();
            $endDate->endOfWeek();
        } else if ($request->type != '' && $request->type == 'Monthly') {
            $startDate->startOfMonth();
            $endDate->endOfMonth();
        } else {
            $startDate->startOfDay();
            $endDate->endOfDay();
        }

        $bestSellingProducts = Sale::join('sale_details', 'sales.id', '=', 'sale_details.sale_id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('stocks', 'stocks.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name as product_name',
                DB::raw('SUM(sale_details.quantity) as total_product_sold'),
                DB::raw('SUM(sale_details.sub_total) as total_sale'),
                DB::raw('SUM(stocks.stock) as total_stock')
            )
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('sales.created_at', [$startDate, $endDate]);
            })
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sale', 'desc')
            ->limit(3)
            ->get();

        $topSellingProductsArray = array();
        foreach ($bestSellingProducts as $index => $value) {
            $subArray = [];
            $subArray['id'] = $value->id;
            $subArray['product_name'] = $value->product_name;
            $subArray['total_product_sold'] = $value->total_product_sold;
            $subArray['total_sale'] = $value->total_sale;
            $subArray['total_stock'] = $value->total_stock;
            array_push($topSellingProductsArray, $value);
        }

        return response()->json($topSellingProductsArray);
    }

    public function productsInStocks(Request $request)
    {
        // $startDate = Carbon::now();
        // $endDate = Carbon::now();
        // if ($request->type != '' && $request->type == 'Weekly') {
        //     $startDate->startOfWeek();
        //     $endDate->endOfWeek();
        // } else if ($request->type != '' && $request->type == 'Monthly') {
        //     $startDate->startOfMonth();
        //     $endDate->endOfMonth();
        // } else {
        //     $startDate->startOfDay();
        //     $endDate->endOfDay();
        // }

        $products = Product::where(function ($query) use ($request) {
            if ($request->type != "") {
                $query->where('city_id', $request->type);
            }
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

        $exchangeCityProducts = Returns::join('return_details', 'return_details.return_id', 'returns.id')
            ->join('products', 'products.id', 'return_details.product_id')
            ->where(function ($query) use ($request) {
                if ($request->type != "") {
                    $query->where('returns.to_city_id', $request->type);
                }
            })
            ->select(
                'products.*',
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
            ->get()
            ->toArray();

        $mergedProducts = [];

        // Index exchangeCityProducts by product ID for easy lookup
        $exchangeCityProductsByName = [];
        foreach ($exchangeCityProducts as $product) {
            $exchangeCityProductsByName[$product['name']] = $product;
        }

        // Loop through $products
        foreach ($products as $product) {
            $productName = $product['name'];

            // If the product exists in $exchangeCityProducts, sum the total_stock
            if (isset($exchangeCityProductsByName[$productName])) {
                $product['total_stock'] += $exchangeCityProductsByName[$productName]['total_stock'];
                unset($exchangeCityProductsByName[$productName]); // Remove it after merging
            }

            // Add the product to the merged list
            $mergedProducts[] = $product;
        }

        // Add any remaining exchangeCityProducts that weren't in $products
        foreach ($exchangeCityProductsByName as $remainingProduct) {
            $mergedProducts[] = $remainingProduct;
        }

        $details = 0;
        $remaining = 0;
        $totalProductsStock = 0;
        $productsInStockArray = [];
        foreach ($mergedProducts as $product) {
            // $details =  (int) $product['total_stock'] / (int) $product['pieces_in_box'];
            // $roundedDetails = floor($details);
            // $partialPiece = $details - $roundedDetails;
            // $remaining = $partialPiece;
            // if (is_float($partialPiece)) {
            //     $remaining = round($partialPiece * $product['pieces_in_box']);
            // }

            $boxes = (!empty($product['total_stock']) && !empty($product['pieces_in_box'])) ? SiteHelper::makeBoxes($product['total_stock'], $product['pieces_in_box']) : 0;

            $totalProductsStock += $product['total_stock'];
            $productsInStockArray[] = [
                'id' => $product['id'],
                'product_name' => "{$product['name']} {$boxes}",
                'total_stock' => $product['total_stock'],
            ];
        }
        $response = [
            'products' => $productsInStockArray,
            'total_products_stock' => $totalProductsStock,
        ];

        return response()->json($response);
    }

    public function replaceProducts(Request $request)
    {
        $startDate = Carbon::now();
        $endDate = Carbon::now();
        if ($request->type != '' && $request->type == 'Weekly') {
            $startDate->startOfWeek();
            $endDate->endOfWeek();
        } else if ($request->type != '' && $request->type == 'Monthly') {
            $startDate->startOfMonth();
            $endDate->endOfMonth();
        } else {
            $startDate->startOfDay();
            $endDate->endOfDay();
        }

        $productsReplace = DamageReplaceItem::join('damage_replaces', 'damage_replaces.id', 'damage_replace_items.damage_id')
            ->join('products', 'products.id', 'damage_replace_items.product_id')
            ->whereNull('products.deleted_at')
            ->whereNull('damage_replaces.deleted_at')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(damage_replace_items.quantity) as total_quantity')
            )
            ->whereBetween('damage_replaces.date', [$startDate, $endDate])
            ->groupBy('damage_replace_items.product_id', 'products.id', 'products.name')
            ->get();

        $totalProductsDamage = 0;
        $productsReplaceArray = [];
        foreach ($productsReplace as $product) {
            $totalProductsDamage += $product->total_quantity;
            $productsReplaceArray[] = [
                'id' => $product->id,
                'product_name' => $product->name,
                'total_quantity' => $product->total_quantity,
            ];
        }
        $response = [
            'products' => $productsReplaceArray,
            'total_damage_products' => $totalProductsDamage,
        ];

        return response()->json($response);
    }

    public function topSeller(Request $request)
    {
        $startDate = Carbon::now();
        $endDate = Carbon::now();
        if ($request->type != '' && $request->type == 'Weekly') {
            $startDate->startOfWeek();
            $endDate->endOfWeek();
        } else if ($request->type != '' && $request->type == 'Monthly') {
            $startDate->startOfMonth();
            $endDate->endOfMonth();
        } else {
            $startDate->startOfDay();
            $endDate->endOfDay();
        }

        $topSellers = Sale::join('users', 'sales.seller_id', '=', 'users.id')
            ->join('sale_details', 'sales.id', '=', 'sale_details.sale_id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                'users.profile_image as user_profile_image',
                'sale_details.product_id',
                'products.name as product_name',
                DB::raw('SUM(sale_details.quantity) as total_product_sold'),
                DB::raw('SUM(sale_details.sub_total) as total_sale')
            )
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('sales.created_at', [$startDate, $endDate]);
            })
            ->groupBy('users.id', 'sale_details.product_id', 'products.name')
            ->orderBy('total_sale', 'desc')
            ->limit(4)
            ->get();

        $topSellersArray = array();
        $sellers = array();
        foreach ($topSellers as $index => $value) {
            if (!in_array($value->user_id, $sellers)) {
                array_push($topSellersArray, $value);
                array_push($sellers, $value->user_id);
            }
            if (count($topSellersArray) == 4) {
                break;
            }
        }

        echo json_encode($topSellersArray);
    }

    public function topStoreGetMostCredit(Request $request)
    {
        $startDate = Carbon::now();
        $endDate = Carbon::now();
        if ($request->type != '' && $request->type == 'Weekly') {
            $startDate->startOfWeek();
            $endDate->endOfWeek();
        } else if ($request->type != '' && $request->type == 'Monthly') {
            $startDate->startOfMonth();
            $endDate->endOfMonth();
        } else {
            $startDate->startOfDay();
            $endDate->endOfDay();
        }

        $storeGetMostCredit = Order::join('shops', 'shops.id', '=', 'orders.shop_id')
            ->where('orders.payment_type', 'Credit')
            ->select(
                'shops.shop_id',
                'shops.name as shop_name',
                DB::raw('SUM(orders.grand_total) as total_credit')
            )
            ->groupBy('shops.id', 'shops.name')
            ->orderBy('total_credit', 'desc')
            ->limit(4)
            ->get();

        return response()->json($storeGetMostCredit);
    }

    public function topSellerGetMaximumCredit(Request $request)
    {
        $startDate = Carbon::now();
        $endDate = Carbon::now();
        if ($request->type != '' && $request->type == 'Weekly') {
            $startDate->startOfWeek();
            $endDate->endOfWeek();
        } else if ($request->type != '' && $request->type == 'Monthly') {
            $startDate->startOfMonth();
            $endDate->endOfMonth();
        } else {
            $startDate->startOfDay();
            $endDate->endOfDay();
        }

        $storeGetMostCredit = Order::join('users', 'users.id', '=', 'orders.seller_id')
            ->where('orders.payment_type', 'Credit')
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                DB::raw('SUM(orders.grand_total) as total_credit')
            )
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_credit', 'desc')
            ->limit(4)
            ->get();

        return response()->json($storeGetMostCredit);
    }

    public function bonusChart(Request $request)
    {
        $bonus = Sale::join('sale_details', 'sales.id', 'sale_details.sale_id')
            ->join('products', 'sale_details.product_id', 'products.id')
            ->select(
                'products.name as product_name',
                DB::raw('SUM(sale_details.quantity) as product_quantity'),
                DB::raw('SUM(sale_details.retail_price) as product_price')
            )
            ->where('sales.bonus', 1)
            ->groupBy('products.id')
            ->orderBy('product_quantity', 'DESC')
            ->get();

        $array = array();
        foreach ($bonus as $index => $value) {
            $subArray = [];
            $subArray['product_name'] = $value->product_name;
            $subArray['product_quantity'] = (int) $value->product_quantity;
            $subArray['product_price'] = $value->product_price;
            array_push($array, $subArray);
        }

        return response()->json($array);
    }

    public function allOrdersTable()
    {
        $orders = Order::join('users AS sellers', 'sellers.id', 'orders.seller_id')
            ->join('shops', 'shops.id', 'orders.shop_id')
            // ->where(function ($query) use ($searchTerm) {
            //     $query->orWhere('sellers.name', 'LIKE', '%' . $searchTerm . '%');
            //     $query->orWhere('shops.name', 'LIKE', '%' . $searchTerm . '%');
            // })
            // ->where('orders.status', 'Completed')
            ->select(
                'orders.*',
                'sellers.name as seller_name',
                'shops.name as shop_name'
            )
            ->latest()
            ->take(10)
            ->get();

        foreach ($orders as $item) {
            $item->date = Carbon::parse($item->date)->format('d-m-Y');

            if ($item->price_type == 'retail_price') {
                $item->price_type = 'Retail Price';
            } elseif ($item->price_type == 'wholesale_price') {
                $item->price_type = 'WholeSale Price';
            } elseif ($item->price_type == 'extra_price') {
                $item->price_type = 'Extra Price';
            }
        }

        return response()->json($orders);
    }
}
