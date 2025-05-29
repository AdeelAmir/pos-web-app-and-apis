@extends('admin.layouts.app')
@section('content')
    <style>
        .fs-icon-30 {
            font-size: 30px;
        }
        .apexcharts-legend {
            display: flex !important;
            justify-content: center !important;
        }
    </style>
    
    <div class="content container-fluid" id="dashboard-page">
        <div class="mb-3 d-flex justify-content-between">
            <h3 class="mb-0">{{__('messages.dashboard')}}</h3>
        </div>
        <form>
            <input type="hidden" name="start_date" id="start_date" value="">
            <input type="hidden" name="end_date" id="end_date" value="">
        </form>
        <div class="row">
            <div class="d-flex align-items-center mb-3">
                <div class="me-3">
                    <input type="text" name="datefilter" id="datefilter" class="form-control" value="" placeholder="Year-to-date" />
                </div>
                <span>2024/1/1 ~ 2024/12/11</span>
            </div>
            <div class="col-12 col-xl-12 stretch-card mb-3">
                <div class="row flex-grow">
                    <div class="col grid-margin mb-3">
                        <div class="card dashboard-card">
                            <div class="card-body dashboard-card-body d-flex flex-column justify-content-center">
                                <div class="mb-2">
                                    <span class="fw-bold">
                                        {{__('messages.dashboard_elements.total_revenue')}}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <h3 class="text-primary m-0" id="total_revenue">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col grid-margin mb-3">
                        <div class="card dashboard-card">
                            <div class="card-body dashboard-card-body d-flex flex-column justify-content-center">
                                <div class="mb-2">
                                    <span class="fw-bold">
                                        {{__('messages.dashboard_elements.total_products')}}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <h3 class="text-primary m-0" id="total_products">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col grid-margin mb-3">
                        <div class="card dashboard-card">
                            <div class="card-body dashboard-card-body d-flex flex-column justify-content-center">
                                <div class="mb-2">
                                    <span class="fw-bold">
                                        {{__('messages.dashboard_elements.total_sellers')}}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <h3 class="text-primary m-0" id="total_sellers">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col grid-margin mb-3">
                        <div class="card dashboard-card">
                            <div class="card-body dashboard-card-body d-flex flex-column justify-content-center">
                                <div class="mb-2">
                                    <span class="fw-bold">
                                        {{__('messages.dashboard_elements.total_shops')}}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <h3 class="text-primary m-0" id="total_shops">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col grid-margin mb-3">
                        <div class="card dashboard-card">
                            <div class="card-body dashboard-card-body d-flex flex-column justify-content-center">
                                <div class="mb-2">
                                    <span class="fw-bold">
                                        {{__('messages.dashboard_elements.total_users')}}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <h3 class="text-primary m-0" id="total_users">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-12 stretch-card mb-3">
                <div class="row">
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-2">
                                    <span class="fw-bold">
                                        {{__('messages.dashboard_elements.today_revenue')}}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <h3 class="text-primary m-0" id="today_revenue">0</h3>
                                </div>
                                <hr class="">
                                <div class="mb-2">
                                    <span class="fw-bold">
                                        {{__('messages.dashboard_elements.weekly_revenue')}}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <h3 class="text-primary m-0" id="weekly_revenue">0</h3>
                                </div>
                                <hr class="">
                                <div class="mb-2">
                                    <span class="fw-bold">
                                        {{__('messages.dashboard_elements.monthly_revenue')}}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <h3 class="text-primary m-0" id="monthly_revenue">0</h3>
                                </div>
                                <hr class="">
                                <div class="mb-2">
                                    <span class="fw-bold">
                                        {{__('messages.dashboard_elements.annual_revenue')}}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <h3 class="text-primary m-0" id="annual_revenue">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <span class="fw-bold">{{__('messages.dashboard_elements.total_revenue')}}</span>
                                <div class="d-none d-md-flex align-items-center">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Basic radio toggle button group">
                                        <input type="radio" class="btn-check" name="rev_radio_desktop" id="rev_daily_desktop" autocomplete="off" onclick="loadIncomeChart(this.value)" value="Daily">
                                        <label class="btn btn-outline-primary" for="rev_daily_desktop">{{ __('messages.dashboard_elements.filter.daily') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="rev_radio_desktop" id="rev_weekly_desktop" autocomplete="off" onclick="loadIncomeChart(this.value)" value="Weekly">
                                        <label class="btn btn-outline-primary" for="rev_weekly_desktop">{{ __('messages.dashboard_elements.filter.weekly') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="rev_radio_desktop" id="rev_monthly_desktop" autocomplete="off" onclick="loadIncomeChart(this.value)" value="Monthly" checked>
                                        <label class="btn btn-outline-primary" for="rev_monthly_desktop">{{ __('messages.dashboard_elements.filter.monthly') }}</label>
                                    </div>
                                </div>
                                <div class="d-md-none d-flex align-items-center">
                                    <div class="btn-group-sm btn-group-vertical" role="group" aria-label="Basic radio toggle button group">
                                        <input type="radio" class="btn-check" name="rev_radio_mobile" id="rev_daily_mobile" autocomplete="off" onclick="loadIncomeChart(this.value)" checked value="Daily">
                                        <label class="btn btn-outline-primary" for="rev_daily_mobile">{{ __('messages.dashboard_elements.filter.daily') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="rev_radio_mobile" id="rev_weekly_mobile" autocomplete="off" onclick="loadIncomeChart(this.value)" value="Weekly">
                                        <label class="btn btn-outline-primary" for="rev_weekly_mobile">{{ __('messages.dashboard_elements.filter.weekly') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="rev_radio_mobile" id="rev_monthly_mobile" autocomplete="off" onclick="loadIncomeChart(this.value)" value="Monthly" checked>
                                        <label class="btn btn-outline-primary" for="rev_monthly_mobile">{{ __('messages.dashboard_elements.filter.monthly') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body" id="chart1"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-12 stretch-card mb-3">
                <div class="row">
                    {{-- Expenditure --}}
                    <div class="col-md-7 mb-3 mb-md-0">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="fw-bold">{{__('messages.dashboard_elements.expenditure')}}</span>
                                <div class="d-none d-md-flex align-items-center">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Basic radio toggle button group">
                                        <input type="radio" class="btn-check" name="exp_radio_desktop" id="exp_daily_desktop" autocomplete="off" onclick="loadExpenseChart(this.value)" checked value="Daily">
                                        <label class="btn btn-outline-primary" for="exp_daily_desktop">{{ __('messages.dashboard_elements.filter.daily') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="exp_radio_desktop" id="exp_weekly_desktop" autocomplete="off" onclick="loadExpenseChart(this.value)" value="Weekly">
                                        <label class="btn btn-outline-primary" for="exp_weekly_desktop">{{ __('messages.dashboard_elements.filter.weekly') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="exp_radio_desktop" id="exp_monthly_desktop" autocomplete="off" onclick="loadExpenseChart(this.value)" value="Monthly">
                                        <label class="btn btn-outline-primary" for="exp_monthly_desktop">{{ __('messages.dashboard_elements.filter.monthly') }}</label>
                                    </div>
                                </div>
                                <div class="d-md-none d-flex align-items-center">
                                    <div class="btn-group-sm btn-group-vertical" role="group" aria-label="Basic radio toggle button group">
                                        <input type="radio" class="btn-check" name="exp_radio_mobile" id="exp_daily_mobile" autocomplete="off" onclick="loadExpenseChart(this.value)" checked value="Daily">
                                        <label class="btn btn-outline-primary" for="exp_daily_mobile">{{ __('messages.dashboard_elements.filter.daily') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="exp_radio_mobile" id="exp_weekly_mobile" autocomplete="off" onclick="loadExpenseChart(this.value)" value="Weekly">
                                        <label class="btn btn-outline-primary" for="exp_weekly_mobile">{{ __('messages.dashboard_elements.filter.weekly') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="exp_radio_mobile" id="exp_monthly_mobile" autocomplete="off" onclick="loadExpenseChart(this.value)" value="Monthly">
                                        <label class="btn btn-outline-primary" for="exp_monthly_mobile">{{ __('messages.dashboard_elements.filter.monthly') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 d-flex justify-content-center mb-md-0 mb-3">
                                        <div id="chart2"></div>
                                    </div>
                                    <div class="col-md-6 col-sm-12 d-flex justify-content-center">
                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                            <div class="mb-3">
                                                {{__('messages.dashboard_elements.total_expenditure')}}
                                            </div>
                                            <div class="text-primary fw-bold fs-3" id="total_expenditure">
                                                0
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Best Selling Products --}}
                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="fw-bold">{{__('messages.dashboard_elements.best_selling_products')}}</span>
                                <div class="d-none d-md-flex align-items-center">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Basic radio toggle button group">
                                        <input type="radio" class="btn-check" name="bsp_radio_desktop" id="bsp_daily_desktop" autocomplete="off" onclick="loadSellingProductsChart(this.value)" checked value="Daily">
                                        <label class="btn btn-outline-primary" for="bsp_daily_desktop">{{ __('messages.dashboard_elements.filter.daily') }}</label>
                                
                                        <input type="radio" class="btn-check" name="bsp_radio_desktop" id="bsp_weekly_desktop" autocomplete="off" onclick="loadSellingProductsChart(this.value)" value="Weekly">
                                        <label class="btn btn-outline-primary" for="bsp_weekly_desktop">{{ __('messages.dashboard_elements.filter.weekly') }}</label>
                                
                                        <input type="radio" class="btn-check" name="bsp_radio_desktop" id="bsp_monthly_desktop" autocomplete="off" onclick="loadSellingProductsChart(this.value)" value="Monthly">
                                        <label class="btn btn-outline-primary" for="bsp_monthly_desktop">{{ __('messages.dashboard_elements.filter.monthly') }}</label>
                                    </div>
                                </div>
                                <div class="d-md-none d-flex align-items-center">
                                    <div class="btn-group-sm btn-group-vertical" role="group" aria-label="Basic radio toggle button group">
                                        <input type="radio" class="btn-check" name="bsp_radio_mobile" id="bsp_daily_mobile" autocomplete="off" onclick="loadSellingProductsChart(this.value)" checked value="Daily">
                                        <label class="btn btn-outline-primary" for="bsp_daily_mobile">{{ __('messages.dashboard_elements.filter.daily') }}</label>
                                
                                        <input type="radio" class="btn-check" name="bsp_radio_mobile" id="bsp_weekly_mobile" autocomplete="off" onclick="loadSellingProductsChart(this.value)" value="Weekly">
                                        <label class="btn btn-outline-primary" for="bsp_weekly_mobile">{{ __('messages.dashboard_elements.filter.weekly') }}</label>
                                
                                        <input type="radio" class="btn-check" name="bsp_radio_mobile" id="bsp_monthly_mobile" autocomplete="off" onclick="loadSellingProductsChart(this.value)" value="Monthly">
                                        <label class="btn btn-outline-primary" for="bsp_monthly_mobile">{{ __('messages.dashboard_elements.filter.monthly') }}</label>
                                    </div>
                                </div>                                
                            </div>
                            <div class="card-body">
                                <div id="chart3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-12 stretch-card mb-3">
                <div class="row">
                    {{-- Goods Left --}}
                    <div class="col-md-5 mb-3 mb-md-0">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <span class="fw-bold">{{__('messages.dashboard_elements.goods_left')}}</span>
                                <div class="d-inline-flex">
                                    <select name="chart4_city_select" id="chart4_city_select" class="form-select-sm select2" onchange="loadProductsInStocksChart(this.value)">
                                        <option value="" selected>{{ __('messages.city') }}</option>
                                        @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                    {{-- <select name="chart4_select" id="chart4_select" class="form-select-sm select2" onchange="loadProductsInStocksChart(this.value)">
                                        <option value="Daily" selected>Daily</option>
                                        <option value="Weekly">Weekly</option>
                                        <option value="Monthly">Monthly</option>
                                    </select> --}}
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center align-items-center" id="chart4"></div>
                            </div>
                        </div>
                    </div>
                    {{-- Top Sellers --}}
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="fw-bold">{{__('messages.dashboard_elements.top_sellers')}}</span>
                                <div class="d-none d-md-flex align-items-center">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Basic radio toggle button group">
                                        <input type="radio" class="btn-check" name="tslr_radio_desktop" id="tslr_daily_desktop" autocomplete="off" onclick="MakeTopSellerTable(this.value)" checked value="Daily">
                                        <label class="btn btn-outline-primary" for="tslr_daily_desktop">{{ __('messages.dashboard_elements.filter.daily') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="tslr_radio_desktop" id="tslr_weekly_desktop" autocomplete="off" onclick="MakeTopSellerTable(this.value)" value="Weekly">
                                        <label class="btn btn-outline-primary" for="tslr_weekly_desktop">{{ __('messages.dashboard_elements.filter.weekly') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="tslr_radio_desktop" id="tslr_monthly_desktop" autocomplete="off" onclick="MakeTopSellerTable(this.value)" value="Monthly">
                                        <label class="btn btn-outline-primary" for="tslr_monthly_desktop">{{ __('messages.dashboard_elements.filter.monthly') }}</label>
                                    </div>
                                </div>
                                <div class="d-md-none d-flex align-items-center">
                                    <div class="btn-group-sm btn-group-vertical" role="group" aria-label="Basic radio toggle button group">
                                        <input type="radio" class="btn-check" name="tslr_radio_mobile" id="tslr_daily_mobile" autocomplete="off" onclick="MakeTopSellerTable(this.value)" checked value="Daily">
                                        <label class="btn btn-outline-primary" for="tslr_daily_mobile">{{ __('messages.dashboard_elements.filter.daily') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="tslr_radio_mobile" id="tslr_weekly_mobile" autocomplete="off" onclick="MakeTopSellerTable(this.value)" value="Weekly">
                                        <label class="btn btn-outline-primary" for="tslr_weekly_mobile">{{ __('messages.dashboard_elements.filter.weekly') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="tslr_radio_mobile" id="tslr_monthly_mobile" autocomplete="off" onclick="MakeTopSellerTable(this.value)" value="Monthly">
                                        <label class="btn btn-outline-primary" for="tslr_monthly_mobile">{{ __('messages.dashboard_elements.filter.monthly') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table datatable1" id="topSellerTable">
                                        <thead>
                                            <tr>
                                                <td>{{__('messages.table_elements.rank')}}</td>
                                                <td>{{__('messages.table_elements.seller_name')}}</td>
                                                <td>{{__('messages.table_elements.products_sell')}}</td>
                                                <td>{{__('messages.table_elements.sale')}}</td>
                                                <td>{{__('messages.table_elements.sold')}}</td>
                                            </tr>
                                        </thead>
                                        <tbody id="topSellerTableBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-12 stretch-card mb-3">
                <div class="row">
                    {{-- Replace/Damage Goods --}}
                    <div class="col-md-5 mb-3 mb-md-0">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="fw-bold">{{__('messages.dashboard_elements.replace_damage_goods')}}</span>
                                <select name="chart5_select" id="chart5_select" class="form-select-sm select2" onchange="loadDamageReplaceProductsChart(this.value)">
                                    <option value="Daily" selected>{{ __('messages.dashboard_elements.filter.daily') }}</option>
                                    <option value="Weekly">{{ __('messages.dashboard_elements.filter.weekly') }}</option>
                                    <option value="Monthly">{{ __('messages.dashboard_elements.filter.monthly') }}</option>
                                </select>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center align-items-center" id="chart5"></div>
                            </div>
                        </div>
                    </div>
                    {{-- Shops get maximum loan --}}
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="fw-bold">{{__('messages.dashboard_elements.shop_max_loan')}}</span>
                                <div class="d-none d-md-flex align-items-center">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Basic radio toggle button group">
                                        <input type="radio" class="btn-check" name="shml_radio_desktop" id="shml_daily_desktop" autocomplete="off" onclick="MakeStoresGetMostCredit(this.value)" checked value="Daily">
                                        <label class="btn btn-outline-primary" for="shml_daily_desktop">{{ __('messages.dashboard_elements.filter.daily') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="shml_radio_desktop" id="shml_weekly_desktop" autocomplete="off" onclick="MakeStoresGetMostCredit(this.value)" value="Weekly">
                                        <label class="btn btn-outline-primary" for="shml_weekly_desktop">{{ __('messages.dashboard_elements.filter.weekly') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="shml_radio_desktop" id="shml_monthly_desktop" autocomplete="off" onclick="MakeStoresGetMostCredit(this.value)" value="Monthly">
                                        <label class="btn btn-outline-primary" for="shml_monthly_desktop">{{ __('messages.dashboard_elements.filter.monthly') }}</label>
                                    </div>
                                </div>
                                <div class="d-md-none d-flex align-items-center">
                                    <div class="btn-group-sm btn-group-vertical" role="group" aria-label="Basic radio toggle button group">
                                        <input type="radio" class="btn-check" name="shml_radio_mobile" id="shml_daily_mobile" autocomplete="off" onclick="MakeStoresGetMostCredit(this.value)" checked value="Daily">
                                        <label class="btn btn-outline-primary" for="shml_daily_mobile">{{ __('messages.dashboard_elements.filter.daily') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="shml_radio_mobile" id="shml_weekly_mobile" autocomplete="off" onclick="MakeStoresGetMostCredit(this.value)" value="Weekly">
                                        <label class="btn btn-outline-primary" for="shml_weekly_mobile">{{ __('messages.dashboard_elements.filter.weekly') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="shml_radio_mobile" id="shml_monthly_mobile" autocomplete="off" onclick="MakeStoresGetMostCredit(this.value)" value="Monthly">
                                        <label class="btn btn-outline-primary" for="shml_monthly_mobile">{{ __('messages.dashboard_elements.filter.monthly') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <td>{{__('messages.table_elements.rank')}}</td>
                                                <td>{{__('messages.table_elements.shop_id')}}</td>
                                                <td>{{__('messages.table_elements.shop_name')}}</td>
                                                <td>{{__('messages.table_elements.total_loan')}}</td>
                                            </tr>
                                        </thead>
                                        <tbody id="topStoreGetMostCredit">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-12 stretch-card mb-3">
                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="fw-bold">{{__('messages.dashboard_elements.seller_max_loan')}}</span>
                                <div class="d-none d-md-flex align-items-center">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Basic radio toggle button group">
                                        <input type="radio" class="btn-check" name="sml_radio_desktop" id="sml_daily_desktop" autocomplete="off" onclick="MakeSellerReceiveMaximumCredit(this.value)" checked value="Daily">
                                        <label class="btn btn-outline-primary" for="sml_daily_desktop">{{ __('messages.dashboard_elements.filter.daily') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="sml_radio_desktop" id="sml_weekly_desktop" autocomplete="off" onclick="MakeSellerReceiveMaximumCredit(this.value)" value="Weekly">
                                        <label class="btn btn-outline-primary" for="sml_weekly_desktop">{{ __('messages.dashboard_elements.filter.weekly') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="sml_radio_desktop" id="sml_monthly_desktop" autocomplete="off" onclick="MakeSellerReceiveMaximumCredit(this.value)" value="Monthly">
                                        <label class="btn btn-outline-primary" for="sml_monthly_desktop">{{ __('messages.dashboard_elements.filter.monthly') }}</label>
                                    </div>
                                </div>
                                <div class="d-md-none d-flex align-items-center">
                                    <div class="btn-group-sm btn-group-vertical" role="group" aria-label="Basic radio toggle button group">
                                        <input type="radio" class="btn-check" name="sml_radio_mobile" id="sml_daily_mobile" autocomplete="off" onclick="MakeSellerReceiveMaximumCredit(this.value)" checked value="Daily">
                                        <label class="btn btn-outline-primary" for="sml_daily_mobile">{{ __('messages.dashboard_elements.filter.daily') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="sml_radio_mobile" id="sml_weekly_mobile" autocomplete="off" onclick="MakeSellerReceiveMaximumCredit(this.value)" value="Weekly">
                                        <label class="btn btn-outline-primary" for="sml_weekly_mobile">{{ __('messages.dashboard_elements.filter.weekly') }}</label>
                                      
                                        <input type="radio" class="btn-check" name="sml_radio_mobile" id="sml_monthly_mobile" autocomplete="off" onclick="MakeSellerReceiveMaximumCredit(this.value)" value="Monthly">
                                        <label class="btn btn-outline-primary" for="sml_monthly_mobile">{{ __('messages.dashboard_elements.filter.monthly') }}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <td>{{__('messages.table_elements.rank')}}</td>
                                                <td>{{__('messages.table_elements.shop_id')}}</td>
                                                <td>{{__('messages.table_elements.shop_name')}}</td>
                                                <td>{{__('messages.table_elements.total_loan')}}</td>
                                            </tr>
                                        </thead>
                                        <tbody id="topSellerGetmaximumCredit">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Bonus Chart --}}
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <span class="fw-bold">{{__('messages.dashboard_elements.bonus')}}</span>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center align-items-center" id="chart6"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-12 stretch-card">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="fw-bold">{{__('messages.orders')}}</span>
                        {{-- <div class="d-none d-md-flex align-items-center">
                            <div class="btn-group btn-group-sm" role="group" aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="sml_radio_desktop" id="sml_daily_desktop" autocomplete="off" onclick="MakeSellerReceiveMaximumCredit(this.value)" checked value="Daily">
                                <label class="btn btn-outline-primary" for="sml_daily_desktop">{{ __('messages.dashboard_elements.filter.daily') }}</label>
                                
                                <input type="radio" class="btn-check" name="sml_radio_desktop" id="sml_weekly_desktop" autocomplete="off" onclick="MakeSellerReceiveMaximumCredit(this.value)" value="Weekly">
                                <label class="btn btn-outline-primary" for="sml_weekly_desktop">{{ __('messages.dashboard_elements.filter.weekly') }}</label>
                                
                                <input type="radio" class="btn-check" name="sml_radio_desktop" id="sml_monthly_desktop" autocomplete="off" onclick="MakeSellerReceiveMaximumCredit(this.value)" value="Monthly">
                                <label class="btn btn-outline-primary" for="sml_monthly_desktop">{{ __('messages.dashboard_elements.filter.monthly') }}</label>
                            </div>
                        </div>
                        <div class="d-md-none d-flex align-items-center">
                            <div class="btn-group-sm btn-group-vertical" role="group" aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="sml_radio_mobile" id="sml_daily_mobile" autocomplete="off" onclick="MakeSellerReceiveMaximumCredit(this.value)" checked value="Daily">
                                <label class="btn btn-outline-primary" for="sml_daily_mobile">{{ __('messages.dashboard_elements.filter.daily') }}</label>
                                
                                <input type="radio" class="btn-check" name="sml_radio_mobile" id="sml_weekly_mobile" autocomplete="off" onclick="MakeSellerReceiveMaximumCredit(this.value)" value="Weekly">
                                <label class="btn btn-outline-primary" for="sml_weekly_mobile">{{ __('messages.dashboard_elements.filter.weekly') }}</label>
                                
                                <input type="radio" class="btn-check" name="sml_radio_mobile" id="sml_monthly_mobile" autocomplete="off" onclick="MakeSellerReceiveMaximumCredit(this.value)" value="Monthly">
                                <label class="btn btn-outline-primary" for="sml_monthly_mobile">{{ __('messages.dashboard_elements.filter.monthly') }}</label>
                            </div>
                        </div> --}}
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <td>{{__('messages.table_elements.date')}}</td>
                                        <td>{{__('messages.table_elements.seller_name_id')}}</td>
                                        <td>{{__('messages.table_elements.shop_name_id')}}</td>
                                        <td>{{__('messages.table_elements.price_type')}}</td>
                                        <td>{{__('messages.table_elements.payment_type')}}</td>
                                        <td>{{__('messages.table_elements.grand_total')}}</td>
                                    </tr>   
                                </thead>
                                <tbody id="allOrdersTable">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
