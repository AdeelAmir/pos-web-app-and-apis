@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid" id="report-page">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">{{__('messages.reports')}}</h3>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="datefilter" id="datefilter" class="form-control" @if(isset($dateFilter)) value="{{$dateFilter}}" @endif placeholder="Year-to-date" />
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-primary mb-2 mb-md-0" data-toggle="tooltip" title="Filter" onclick="reportFilter()">
                                <i class="fa fa-filter mr-1"></i> {{ __('messages.btns.filter') }}
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="start_date" id="start_date" @isset($startDate) value="{{$startDate}}" @endisset>
                    <input type="hidden" name="end_date" id="end_date" @isset($endDate) value="{{$endDate}}" @endisset>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <input type="text" name="search" id="search" class="form-control form-control-sm search-bar" placeholder="{{__('messages.search')}}" onkeyup="">
                            </div>
                            <div class="d-flex align-items-center text-nowrap mr-1">
                                <div class="me-3">
                                    <select name="no_of_entries" id="no_of_entries" class="form-control form-select-sm select2 me-3">
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="200">200</option>
                                        <option value="400">400</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="datatable1 table table-stripped w-100" id="">
                                <thead>
                                <tr>
                                    <td>{{ __('messages.table_elements.seller_name') }}</td>
                                    @foreach($products as $product)
                                        <td>{{ $product->name }}</td>
                                    @endforeach
                                    <td>{{ __('messages.table_elements.total_items') }}</td>
                                    <td>{{ __('messages.table_elements.grand_total') }}</td>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $totalArr = array();
                                @endphp
                                @foreach ($sellers as $seller)
                                    @php
                                        $totalQuantity = 0;
                                        $grandTotal = 0;
                                        $sellerStatus = 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $seller->name }}</td>
                                        @foreach ($sellerProducts as $details)
                                            @if($details['seller_id'] == $seller->id)
                                                @php
                                                    $sellerStatus = 1;
                                                @endphp
                                                @foreach ($details['products'] as $item)
                                                    <td>{{ $item['quantity'] }}</td>
                                                    @php
                                                        $totalQuantity += $item['quantity'];
                                                        $grandTotal += $item['price'];
                                                    @endphp
                                                @endforeach
                                            @endif
                                        @endforeach
                                        @if($sellerStatus == 0)
                                            @foreach($products as $product)
                                                <td>0</td>
                                            @endforeach
                                        @endif
                                        <td>{{ $totalQuantity }}</td>
                                        <td>{{ \App\Helpers\SiteHelper::settings()['Currency_Icon'] . $grandTotal }}</td>
                                    </tr>
                                @endforeach
                                @php
                                    $totalProductsSold = 0;
                                    $totalProductsSoldPrice = 0;
                                @endphp
                                <tr>
                                    <td>Total</td>
                                    @foreach($products as $product)
                                        @php
                                            $totalProductQuantity = 0;
                                            $totalProductPrice = 0;
                                        @endphp
                                        @foreach ($sellerProducts as $details)
                                            @foreach ($details['products'] as $item)
                                                @if($product->id == $item['product_id'])
                                                    @php
                                                        $totalProductQuantity += $item['quantity'];
                                                        $totalProductPrice += $item['price'];
                                                    @endphp
                                                @endif
                                            @endforeach
                                        @endforeach
                                        @php
                                            $totalProductsSold += $totalProductQuantity;
                                            $totalProductsSoldPrice += $totalProductPrice;
                                        @endphp
                                        <td>{{ $totalProductQuantity }}</td>
                                    @endforeach
                                    <td>{{ $totalProductsSold }}</td>
                                    <td>{{ \App\Helpers\SiteHelper::settings()['Currency_Icon'] . $totalProductsSoldPrice }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
