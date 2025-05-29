<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

//Command Routes
Route::get('clear-cache', function () {
    Artisan::call('storage:link');
    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    //Create storage link on hosting
    $exitCode = Artisan::call('storage:link', []);
    echo $exitCode; // 0 exit code for no errors.
});

Auth::routes(["verify" => true]);
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::middleware(['auth'])->group(function () {
    /* Admin Routes */
    // Dashboard
    Route::get('dashboard', 'App\Http\Controllers\Admin\DashboardController@index')->name('dashboard');
    Route::post('dashboard/cards/top', 'App\Http\Controllers\Admin\DashboardController@topCards')->name('dashboard.top.cards');
    Route::post('dashboard/cards/revenue', 'App\Http\Controllers\Admin\DashboardController@sideRevenueCard')->name('dashboard.revenue.card');
    Route::post('dashboard/chart/income', 'App\Http\Controllers\Admin\DashboardController@incomeChart')->name('dashboard.chart.income');
    Route::post('dashboard/chart/expense', 'App\Http\Controllers\Admin\DashboardController@expenseChart')->name('dashboard.chart.expense');
    Route::post('dashboard/chart/bestSellingProducts', 'App\Http\Controllers\Admin\DashboardController@bestSellingProducts')->name('dashboard.chart.bestSellingProducts');
    Route::post('dashboard/chart/productsInStocks', 'App\Http\Controllers\Admin\DashboardController@productsInStocks')->name('dashboard.chart.productsInStocks');
    Route::post('dashboard/chart/replaceProducts', 'App\Http\Controllers\Admin\DashboardController@replaceProducts')->name('dashboard.chart.replaceProducts');
    Route::post('dashboard/chart/bonus', 'App\Http\Controllers\Admin\DashboardController@bonusChart')->name('dashboard.chart.bonus');

    Route::post('dashboard/top/sellers', 'App\Http\Controllers\Admin\DashboardController@topSeller')->name('dashboard.top.seller');
    Route::post('dashboard/top/store/get/most/credit', 'App\Http\Controllers\Admin\DashboardController@topStoreGetMostCredit')->name('dashboard.store.get.most.credit');
    Route::post('dashboard/top/seller/get/maximum/credit', 'App\Http\Controllers\Admin\DashboardController@topSellerGetMaximumCredit')->name('dashboard.seller.get.maximum.credit');
    Route::post('dashboard/orders/all', 'App\Http\Controllers\Admin\DashboardController@allOrdersTable')->name('dashboard.orders');

    Route::post('/checkUserEmail', 'App\Http\Controllers\HomeController@checkUserEmail')->name('validate.email');

    /* --- CityController --- */
    Route::get('/warehouse-cities', 'App\Http\Controllers\Admin\CityController@index')->name('cities.warehouse');
    Route::get('/warehouse-cities/add', 'App\Http\Controllers\Admin\CityController@add')->name('cities.warehouse.add');
    Route::post('/warehouse-cities/store', 'App\Http\Controllers\Admin\CityController@store')->name('cities.warehouse.store');
    Route::post('/warehouse-cities/load', 'App\Http\Controllers\Admin\CityController@load')->name('cities.warehouse.all');
    Route::post('/warehouse-cities/delete', 'App\Http\Controllers\Admin\CityController@delete')->name('cities.warehouse.delete');
    Route::get('/warehouse-cities/edit/{Id}', 'App\Http\Controllers\Admin\CityController@edit')->name('cities.warehouse.edit');
    Route::post('/warehouse-cities/update', 'App\Http\Controllers\Admin\CityController@update')->name('cities.warehouse.update');

    /* --- SellingCityController --- */
    Route::get('/selling-cities', 'App\Http\Controllers\Admin\SellingCityController@index')->name('cities.selling');
    Route::get('/selling-cities/add', 'App\Http\Controllers\Admin\SellingCityController@add')->name('cities.selling.add');
    Route::post('/selling-cities/store', 'App\Http\Controllers\Admin\SellingCityController@store')->name('cities.selling.store');
    Route::post('/selling-cities/load', 'App\Http\Controllers\Admin\SellingCityController@load')->name('cities.selling.all');
    Route::post('/selling-cities/delete', 'App\Http\Controllers\Admin\SellingCityController@delete')->name('cities.selling.delete');
    Route::get('/selling-cities/edit/{Id}', 'App\Http\Controllers\Admin\SellingCityController@edit')->name('cities.selling.edit');
    Route::post('/selling-cities/update', 'App\Http\Controllers\Admin\SellingCityController@update')->name('cities.selling.update');

    /* --- CategoryController --- */
    Route::get('/categories', 'App\Http\Controllers\Admin\CategoryController@index')->name('categories');
    Route::get('/categories/add', 'App\Http\Controllers\Admin\CategoryController@add')->name('categories.add');
    Route::post('/categories/store', 'App\Http\Controllers\Admin\CategoryController@store')->name('categories.store');
    Route::post('/categories/load', 'App\Http\Controllers\Admin\CategoryController@load')->name('categories.all');
    Route::post('/categories/delete', 'App\Http\Controllers\Admin\CategoryController@delete')->name('categories.delete');
    Route::get('/categories/edit/{Id}', 'App\Http\Controllers\Admin\CategoryController@edit')->name('categories.edit');
    Route::post('/categories/update', 'App\Http\Controllers\Admin\CategoryController@update')->name('categories.update');

    /* --- ProductController --- */
    Route::get('/products', 'App\Http\Controllers\Admin\ProductController@index')->name('products');
    Route::get('/products/add', 'App\Http\Controllers\Admin\ProductController@add')->name('products.add');
    Route::post('/products/store', 'App\Http\Controllers\Admin\ProductController@store')->name('products.store');
    Route::post('/products/load', 'App\Http\Controllers\Admin\ProductController@load')->name('products.all');
    Route::post('/products/delete', 'App\Http\Controllers\Admin\ProductController@delete')->name('products.delete');
    Route::get('/products/edit/{Id}', 'App\Http\Controllers\Admin\ProductController@edit')->name('products.edit');
    Route::post('/products/update', 'App\Http\Controllers\Admin\ProductController@update')->name('products.update');
    Route::post('/products/stock/add', 'App\Http\Controllers\Admin\ProductController@addStock')->name('products.stock.add');
    Route::get('/products/stock/view/{Id}', 'App\Http\Controllers\Admin\ProductController@viewStock')->name('products.stock.view');
    Route::post('/products/stock/load', 'App\Http\Controllers\Admin\ProductController@loadStock')->name('products.stock.load');
    Route::post('/products/stock/delete', 'App\Http\Controllers\Admin\ProductController@deleteStock')->name('products.stock.delete');
    Route::post('/products/stock/pieces', 'App\Http\Controllers\Admin\ProductController@getProductPieces')->name('products.stock.pieces');

    /* --- UsersController --- */
    Route::get('/users', 'App\Http\Controllers\Admin\UsersController@index')->name('users');
    Route::post('/users/load', 'App\Http\Controllers\Admin\UsersController@load')->name('users.all');
    Route::get('/users/add', 'App\Http\Controllers\Admin\UsersController@add')->name('users.add');
    Route::post('/users/store', 'App\Http\Controllers\Admin\UsersController@store')->name('users.store');
    Route::get('/users/edit/{Id}', 'App\Http\Controllers\Admin\UsersController@edit')->name('users.edit');
    Route::post('/users/update', 'App\Http\Controllers\Admin\UsersController@update')->name('users.update');
    Route::get('/users/view/{Id}', 'App\Http\Controllers\Admin\UsersController@view')->name('users.view');
    Route::post('/users/statusUpdate', 'App\Http\Controllers\Admin\UsersController@statusUpdate')->name('users.statusUpdate');
    Route::post('/users/delete', 'App\Http\Controllers\Admin\UsersController@delete')->name('users.delete');

    /* --- SellerController --- */
    Route::get('/sellers', 'App\Http\Controllers\Admin\SellerController@index')->name('sellers');
    Route::post('/sellers/load', 'App\Http\Controllers\Admin\SellerController@load')->name('sellers.all');
    Route::get('/sellers/add', 'App\Http\Controllers\Admin\SellerController@add')->name('sellers.add');
    Route::post('/sellers/store', 'App\Http\Controllers\Admin\SellerController@store')->name('sellers.store');
    Route::get('/sellers/edit/{Id}', 'App\Http\Controllers\Admin\SellerController@edit')->name('sellers.edit');
    Route::post('/sellers/update', 'App\Http\Controllers\Admin\SellerController@update')->name('sellers.update');
    Route::get('/sellers/view/{Id}', 'App\Http\Controllers\Admin\SellerController@view')->name('sellers.view');
    Route::post('/sellers/view/sale', 'App\Http\Controllers\Admin\SellerController@loadSale')->name('sellers.view.sale');
    Route::post('/sellers/view/expense', 'App\Http\Controllers\Admin\SellerController@loadExpense')->name('sellers.view.expense');
    Route::post('/sellers/view/items', 'App\Http\Controllers\Admin\SellerController@loadItems')->name('sellers.view.items');
    Route::post('/sellers/view/loan', 'App\Http\Controllers\Admin\SellerController@loadLoan')->name('sellers.view.loan');
    Route::post('/sellers/view/bonus', 'App\Http\Controllers\Admin\SellerController@loadBonus')->name('sellers.view.bonus');
    Route::post('/sellers/statusUpdate', 'App\Http\Controllers\Admin\SellerController@statusUpdate')->name('sellers.statusUpdate');
    Route::post('/sellers/delete', 'App\Http\Controllers\Admin\SellerController@delete')->name('sellers.delete');
    Route::get('/sellers/target/{Id}', 'App\Http\Controllers\Admin\SellerController@target')->name('sellers.target');
    Route::post('/sellers/target/load', 'App\Http\Controllers\Admin\SellerController@targetLoad')->name('sellers.target.all');
    Route::get('/sellers/target/add/{Id}', 'App\Http\Controllers\Admin\SellerController@targetAdd')->name('sellers.target.add');
    Route::post('/sellers/target/store', 'App\Http\Controllers\Admin\SellerController@targetStore')->name('sellers.target.store');
    Route::get('/sellers/target/edit/{SellerId}/{Id}', 'App\Http\Controllers\Admin\SellerController@targetEdit')->name('sellers.target.edit');
    Route::get('/sellers/target/products/all', 'App\Http\Controllers\Admin\SellerController@getProducts')->name('sellers.target.products');
    Route::post('/sellers/target/update', 'App\Http\Controllers\Admin\SellerController@targetUpdate')->name('sellers.target.update');

    /* --- OfficeSellersController --- */
    Route::get('/office-sellers', 'App\Http\Controllers\Admin\OfficeSellersController@index')->name('sellers.office');
    Route::post('/office-sellers/load', 'App\Http\Controllers\Admin\OfficeSellersController@load')->name('sellers.office.all');
    Route::get('/office-sellers/add', 'App\Http\Controllers\Admin\OfficeSellersController@add')->name('sellers.office.add');
    Route::post('/office-sellers/store', 'App\Http\Controllers\Admin\OfficeSellersController@store')->name('sellers.office.store');
    Route::get('/office-sellers/edit/{Id}', 'App\Http\Controllers\Admin\OfficeSellersController@edit')->name('sellers.office.edit');
    Route::post('/office-sellers/update', 'App\Http\Controllers\Admin\OfficeSellersController@update')->name('sellers.office.update');
    Route::get('/office-sellers/view/{Id}', 'App\Http\Controllers\Admin\OfficeSellersController@view')->name('sellers.office.view');
    Route::post('/office-sellers/statusUpdate', 'App\Http\Controllers\Admin\OfficeSellersController@statusUpdate')->name('sellers.office.statusUpdate');
    Route::post('/office-sellers/delete', 'App\Http\Controllers\Admin\OfficeSellersController@delete')->name('sellers.office.delete');

    /* --- ShopController --- */
    Route::get('/shops', 'App\Http\Controllers\Admin\ShopController@index')->name('shops');
    Route::post('/shops/load', 'App\Http\Controllers\Admin\ShopController@load')->name('shops.all');
    Route::get('/shops/add', 'App\Http\Controllers\Admin\ShopController@add')->name('shops.add');
    Route::post('/shops/store', 'App\Http\Controllers\Admin\ShopController@store')->name('shops.store');
    Route::get('/shops/edit/{Id}', 'App\Http\Controllers\Admin\ShopController@edit')->name('shops.edit');
    Route::post('/shops/update', 'App\Http\Controllers\Admin\ShopController@update')->name('shops.update');
    Route::get('/shops/view/{Id}', 'App\Http\Controllers\Admin\ShopController@view')->name('shops.view');
    Route::post('/shops/statusUpdate', 'App\Http\Controllers\Admin\ShopController@statusUpdate')->name('shops.statusUpdate');
    Route::post('/shops/delete', 'App\Http\Controllers\Admin\ShopController@delete')->name('shops.delete');
    Route::post('/shops/name/check', 'App\Http\Controllers\Admin\ShopController@nameCheck')->name('shops.name.check');

    /* --- SaleController --- */
    Route::get('/sales', 'App\Http\Controllers\Admin\SaleController@index')->name('sales');
    Route::post('/sales/load', 'App\Http\Controllers\Admin\SaleController@load')->name('sales.all');
    Route::get('/sales/add', 'App\Http\Controllers\Admin\SaleController@add')->name('sales.add');
    Route::post('/sales/store', 'App\Http\Controllers\Admin\SaleController@store')->name('sales.store');
    Route::get('/sales/edit/{Id}', 'App\Http\Controllers\Admin\SaleController@edit')->name('sales.edit');
    Route::post('/sales/update', 'App\Http\Controllers\Admin\SaleController@update')->name('sales.update');
    Route::get('/sales/view/{Id}', 'App\Http\Controllers\Admin\SaleController@view')->name('sales.view');
    Route::post('/sales/statusUpdate', 'App\Http\Controllers\Admin\SaleController@statusUpdate')->name('sales.statusUpdate');
    Route::post('/sales/delete', 'App\Http\Controllers\Admin\SaleController@delete')->name('sales.delete');
    Route::post('/sales/products/get', 'App\Http\Controllers\Admin\SaleController@getAllProducts')->name('sales.products');

    /* --- OfficeSaleController --- */
    Route::get('/office-sales', 'App\Http\Controllers\Admin\OfficeSaleController@index')->name('sales.office');
    Route::post('/office-sales/load', 'App\Http\Controllers\Admin\OfficeSaleController@load')->name('sales.office.all');
    Route::get('/office-sales/add', 'App\Http\Controllers\Admin\OfficeSaleController@add')->name('sales.office.add');
    Route::post('/office-sales/store', 'App\Http\Controllers\Admin\OfficeSaleController@store')->name('sales.office.store');
    Route::get('/office-sales/edit/{Id}', 'App\Http\Controllers\Admin\OfficeSaleController@edit')->name('sales.office.edit');
    Route::post('/office-sales/update', 'App\Http\Controllers\Admin\OfficeSaleController@update')->name('sales.office.update');
    Route::get('/office-sales/view/{Id}', 'App\Http\Controllers\Admin\OfficeSaleController@view')->name('sales.office.view');
    Route::post('/office-sales/statusUpdate', 'App\Http\Controllers\Admin\OfficeSaleController@statusUpdate')->name('sales.office.statusUpdate');
    Route::post('/office-sales/delete', 'App\Http\Controllers\Admin\OfficeSaleController@delete')->name('sales.office.delete');
    Route::post('/office-sales/products/get', 'App\Http\Controllers\Admin\OfficeSaleController@getAllProducts')->name('sales.office.products');

    /* --- OfficeLoanController --- */
    Route::get('/office-loan', 'App\Http\Controllers\Admin\OfficeLoanController@index')->name('loan.office');
    Route::post('/office-loan/load', 'App\Http\Controllers\Admin\OfficeLoanController@load')->name('loan.office.all');
    Route::get('/office-loan/add', 'App\Http\Controllers\Admin\OfficeLoanController@add')->name('loan.office.add');
    Route::post('/office-loan/store', 'App\Http\Controllers\Admin\OfficeLoanController@store')->name('loan.office.store');
    Route::get('/office-loan/edit/{Id}', 'App\Http\Controllers\Admin\OfficeLoanController@edit')->name('loan.office.edit');
    Route::post('/office-loan/update', 'App\Http\Controllers\Admin\OfficeLoanController@update')->name('loan.office.update');
    Route::get('/office-loan/view/{Id}', 'App\Http\Controllers\Admin\OfficeLoanController@view')->name('loan.office.view');
    Route::post('/office-loan/statusUpdate', 'App\Http\Controllers\Admin\OfficeLoanController@statusUpdate')->name('loan.office.statusUpdate');
    Route::post('/office-loan/delete', 'App\Http\Controllers\Admin\OfficeLoanController@delete')->name('loan.office.delete');
    Route::post('/office-loan/products/get', 'App\Http\Controllers\Admin\OfficeLoanController@getAllProducts')->name('loan.office.products');
    Route::get('/office-loan/partial-payment/{Id}', 'App\Http\Controllers\Admin\OfficeLoanController@partialPayment')->name('loan.office.partial.payment');
    Route::post('/office-loan/partial-payment/store', 'App\Http\Controllers\Admin\OfficeLoanController@partialPaymentStore')->name('loan.office.partial.payment.store');

    /* --- SaleController(goods-to-supplier) --- */
    Route::get('/goods-to-supplier', 'App\Http\Controllers\Admin\SaleController@index')->name('goods-to-supplier');
    Route::post('/goods-to-supplier/load', 'App\Http\Controllers\Admin\SaleController@load')->name('goods-to-supplier.all');
    Route::get('/goods-to-supplier/add', 'App\Http\Controllers\Admin\SaleController@add')->name('goods-to-supplier.add');
    Route::post('/goods-to-supplier/store', 'App\Http\Controllers\Admin\SaleController@store')->name('goods-to-supplier.store');
    Route::get('/goods-to-supplier/edit/{Id}', 'App\Http\Controllers\Admin\SaleController@edit')->name('goods-to-supplier.edit');
    Route::post('/goods-to-supplier/update', 'App\Http\Controllers\Admin\SaleController@update')->name('goods-to-supplier.update');
    Route::get('/goods-to-supplier/view/{Id}', 'App\Http\Controllers\Admin\SaleController@view')->name('goods-to-supplier.view');
    Route::post('/goods-to-supplier/statusUpdate', 'App\Http\Controllers\Admin\SaleController@statusUpdate')->name('goods-to-supplier.statusUpdate');
    Route::post('/goods-to-supplier/delete', 'App\Http\Controllers\Admin\SaleController@delete')->name('goods-to-supplier.delete');

    /* --- ReturnController --- */
    Route::get('/returns', 'App\Http\Controllers\Admin\ReturnController@index')->name('returns');
    Route::post('/returns/load', 'App\Http\Controllers\Admin\ReturnController@load')->name('returns.all');
    Route::get('/returns/add', 'App\Http\Controllers\Admin\ReturnController@add')->name('returns.add');
    Route::post('/returns/store', 'App\Http\Controllers\Admin\ReturnController@store')->name('returns.store');
    Route::get('/returns/edit/{Id}', 'App\Http\Controllers\Admin\ReturnController@edit')->name('returns.edit');
    Route::post('/returns/update', 'App\Http\Controllers\Admin\ReturnController@update')->name('returns.update');
    Route::get('/returns/view/{Id}', 'App\Http\Controllers\Admin\ReturnController@view')->name('returns.view');
    Route::post('/returns/statusUpdate', 'App\Http\Controllers\Admin\ReturnController@statusUpdate')->name('returns.statusUpdate');
    Route::post('/returns/delete', 'App\Http\Controllers\Admin\ReturnController@delete')->name('returns.delete');
    Route::post('/returns/seller/products', 'App\Http\Controllers\Admin\ReturnController@getSellerProducts')->name('returns.seller.products');

    /* --- OrderController --- */
    Route::get('/orders', 'App\Http\Controllers\Admin\OrderController@index')->name('orders');
    Route::post('/orders/load', 'App\Http\Controllers\Admin\OrderController@load')->name('orders.all');
    Route::get('/orders/view/{Id}', 'App\Http\Controllers\Admin\OrderController@view')->name('orders.view');
    Route::post('/orders/delete', 'App\Http\Controllers\Admin\OrderController@delete')->name('orders.delete');

    /* --- DamageReplaceController --- */
    Route::get('/damage-replace', 'App\Http\Controllers\Admin\DamageReplaceController@index')->name('damage_replace');
    Route::post('/damage-replace/load', 'App\Http\Controllers\Admin\DamageReplaceController@load')->name('damage_replace.all');
    Route::get('/damage-replace/add', 'App\Http\Controllers\Admin\DamageReplaceController@add')->name('damage_replace.add');
    Route::post('/damage-replace/store', 'App\Http\Controllers\Admin\DamageReplaceController@store')->name('damage_replace.store');
    Route::get('/damage-replace/edit/{Id}', 'App\Http\Controllers\Admin\DamageReplaceController@edit')->name('damage_replace.edit');
    Route::post('/damage-replace/update', 'App\Http\Controllers\Admin\DamageReplaceController@update')->name('damage_replace.update');
    Route::get('/damage-replace/view/{Id}', 'App\Http\Controllers\Admin\DamageReplaceController@view')->name('damage_replace.view');
    Route::post('/damage-replace/statusUpdate', 'App\Http\Controllers\Admin\DamageReplaceController@statusUpdate')->name('redamage_replaceturns.statusUpdate');
    Route::post('/damage-replace/delete', 'App\Http\Controllers\Admin\DamageReplaceController@delete')->name('damage_replace.delete');
    Route::post('/damage-replace/products/get', 'App\Http\Controllers\Admin\DamageReplaceController@getAllProducts')->name('damage_replace.products');

    /* --- ExchangeCityController --- */
    Route::get('/exchange-city', 'App\Http\Controllers\Admin\ExchangeCityController@index')->name('exchange_city');
    Route::post('/exchange-city/load', 'App\Http\Controllers\Admin\ExchangeCityController@load')->name('exchange_city.all');
    Route::get('/exchange-city/add', 'App\Http\Controllers\Admin\ExchangeCityController@add')->name('exchange_city.add');
    Route::post('/exchange-city/store', 'App\Http\Controllers\Admin\ExchangeCityController@store')->name('exchange_city.store');
    Route::get('/exchange-city/edit/{Id}', 'App\Http\Controllers\Admin\ExchangeCityController@edit')->name('exchange_city.edit');
    Route::post('/exchange-city/update', 'App\Http\Controllers\Admin\ExchangeCityController@update')->name('exchange_city.update');
    Route::get('/exchange-city/view/{Id}', 'App\Http\Controllers\Admin\ExchangeCityController@view')->name('exchange_city.view');
    Route::post('/exchange-city/statusUpdate', 'App\Http\Controllers\Admin\ExchangeCityController@statusUpdate')->name('exchange_city.statusUpdate');
    Route::post('/exchange-city/delete', 'App\Http\Controllers\Admin\ExchangeCityController@delete')->name('exchange_city.delete');
    Route::post('/exchange-city/seller/products', 'App\Http\Controllers\Admin\ExchangeCityController@getSellerProducts')->name('exchange_city.seller.products');

    /* --- LoanController --- */
    Route::get('/loan', 'App\Http\Controllers\Admin\LoanController@index')->name('loan');
    Route::post('/loan/load', 'App\Http\Controllers\Admin\LoanController@load')->name('loan.all');
    Route::get('/loan/add', 'App\Http\Controllers\Admin\LoanController@add')->name('loan.add');
    Route::post('/loan/store', 'App\Http\Controllers\Admin\LoanController@store')->name('loan.store');
    Route::get('/loan/edit/{Id}', 'App\Http\Controllers\Admin\LoanController@edit')->name('loan.edit');
    Route::post('/loan/update', 'App\Http\Controllers\Admin\LoanController@update')->name('loan.update');
    Route::get('/loan/view/{Id}', 'App\Http\Controllers\Admin\LoanController@view')->name('loan.view');
    Route::post('/loan/statusUpdate', 'App\Http\Controllers\Admin\LoanController@statusUpdate')->name('loan.statusUpdate');
    Route::post('/loan/delete', 'App\Http\Controllers\Admin\LoanController@delete')->name('loan.delete');

    /* --- Expenditure --- */
    /* --- Giving Expenditure --- */
    Route::get('/expenditure/giving', 'App\Http\Controllers\Admin\ExpenditureGivingController@index')->name('expenditure.giving');
    Route::post('/expenditure/giving/load', 'App\Http\Controllers\Admin\ExpenditureGivingController@load')->name('expenditure.giving.all');
    Route::get('/expenditure/giving/add', 'App\Http\Controllers\Admin\ExpenditureGivingController@add')->name('expenditure.giving.add');
    Route::post('/expenditure/giving/store', 'App\Http\Controllers\Admin\ExpenditureGivingController@store')->name('expenditure.giving.store');
    Route::get('/expenditure/giving/edit/{Id}', 'App\Http\Controllers\Admin\ExpenditureGivingController@edit')->name('expenditure.giving.edit');
    Route::post('/expenditure/giving/update', 'App\Http\Controllers\Admin\ExpenditureGivingController@update')->name('expenditure.giving.update');
    Route::post('/expenditure/giving/delete', 'App\Http\Controllers\Admin\ExpenditureGivingController@delete')->name('expenditure.giving.delete');

    /* --- Office Expenditure --- */
    Route::get('/expenditure/office', 'App\Http\Controllers\Admin\ExpenditureOfficeController@index')->name('expenditure.office');
    Route::post('/expenditure/office/load', 'App\Http\Controllers\Admin\ExpenditureOfficeController@load')->name('expenditure.office.all');
    Route::get('/expenditure/office/add', 'App\Http\Controllers\Admin\ExpenditureOfficeController@add')->name('expenditure.office.add');
    Route::post('/expenditure/office/store', 'App\Http\Controllers\Admin\ExpenditureOfficeController@store')->name('expenditure.office.store');
    Route::get('/expenditure/office/edit/{Id}', 'App\Http\Controllers\Admin\ExpenditureOfficeController@edit')->name('expenditure.office.edit');
    Route::post('/expenditure/office/update', 'App\Http\Controllers\Admin\ExpenditureOfficeController@update')->name('expenditure.office.update');
    Route::post('/expenditure/office/delete', 'App\Http\Controllers\Admin\ExpenditureOfficeController@delete')->name('expenditure.office.delete');

    /* --- Seller Expenditure --- */
    Route::get('/expenditure/seller', 'App\Http\Controllers\Admin\ExpenditureSellerController@index')->name('expenditure.seller');
    Route::post('/expenditure/seller/load', 'App\Http\Controllers\Admin\ExpenditureSellerController@load')->name('expenditure.seller.all');
    Route::get('/expenditure/seller/add', 'App\Http\Controllers\Admin\ExpenditureSellerController@add')->name('expenditure.seller.add');
    Route::post('/expenditure/seller/store', 'App\Http\Controllers\Admin\ExpenditureSellerController@store')->name('expenditure.seller.store');
    Route::get('/expenditure/seller/edit/{Id}', 'App\Http\Controllers\Admin\ExpenditureSellerController@edit')->name('expenditure.seller.edit');
    Route::post('/expenditure/seller/update', 'App\Http\Controllers\Admin\ExpenditureSellerController@update')->name('expenditure.seller.update');
    Route::post('/expenditure/seller/delete', 'App\Http\Controllers\Admin\ExpenditureSellerController@delete')->name('expenditure.seller.delete');

    /* --- SettingController --- */
    Route::get('/settings', 'App\Http\Controllers\Admin\SettingController@index')->name('settings');
    Route::post('/settings/update', 'App\Http\Controllers\Admin\SettingController@update')->name('settings.update');

    /* --- ReportsController --- */
    Route::get('report', 'App\Http\Controllers\Admin\ReportController@index')->name('report');
    Route::get('report/{startDate}/{endDate}', 'App\Http\Controllers\Admin\ReportController@index')->name('report.filter');

    /* --- DemandController --- */
    Route::get('/demands', 'App\Http\Controllers\Admin\DemandController@index')->name('demands');
    Route::post('/demands/load', 'App\Http\Controllers\Admin\DemandController@load')->name('demands.load');
    Route::get('/demands/add', 'App\Http\Controllers\Admin\DemandController@add')->name('demands.add');
    Route::post('/demands/store', 'App\Http\Controllers\Admin\DemandController@store')->name('demands.store');
    Route::get('/demands/edit/{Id}', 'App\Http\Controllers\Admin\DemandController@edit')->name('demands.edit');
    Route::post('/demands/update', 'App\Http\Controllers\Admin\DemandController@update')->name('demands.update');
    Route::post('/demands/delete', 'App\Http\Controllers\Admin\DemandController@delete')->name('demands.delete');
    Route::get('/demands/view/{Id}', 'App\Http\Controllers\Admin\DemandController@view')->name('demands.view');
});
