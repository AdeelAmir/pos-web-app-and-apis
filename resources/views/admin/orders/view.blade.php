@extends('admin.layouts.app')
@section('content')
    <style>
        .rounded_circle{
            height: 150px !important;
            width: 150px !important;
            border-radius: 50% !important;
        }
    </style>
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3 d-flex justify-content-between">
                <h3 class="mb-0">{{__('messages.view_order_title')}}</h3>
                <button type="button" class="btn btn-secondary px-4 py-2 ms-1" onclick="window.location.href='{{route('orders')}}'">
                    <i class="fa fa-chevron-left me-1" aria-hidden="true"></i> {{__('messages.btns.back')}}
                </button>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date">{{__('messages.date')}} <span class="text-danger">*</span></label>
                                <input type="date" name="date" id="date" class="form-control" readonly value="{{ $order->date }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="name">{{__('messages.seller_name_id')}} <span class="text-danger">*</span></label>
                                <input type="text" name="seller_id" id="seller_id" class="form-control" readonly value="{{ $seller->name . ' / ' . $seller->id }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="name">{{__('messages.shop_name_id')}} <span class="text-danger">*</span></label>
                                <input type="text" name="shop_id" id="shop_id" class="form-control" readonly value="{{ $shop->name . ' / ' . $shop->id }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="price_type">{{__('messages.price_type')}} <span class="text-danger">*</span></label>
                                <input type="text" name="price_type" id="price_type" class="form-control" readonly value="@if($order->price_type == 'wholesale_price') Wholesale @elseif($order->price_type == 'extra_price') Extra @else Retail @endif">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="payment_type">{{__('messages.payment_type')}} <span class="text-danger">*</span></label>
                                <input type="text" name="payment_type" id="payment_type" class="form-control" readonly value="@if($order->payment_type == 'credit') Credit @else Cash @endif">
                            </div>
                            {{-- <div class="col-md-12 mb-3">
                                <label for="product">Product <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <i class="bx bx-search fs-4 search-icon"></i>
                                    <input type="text" name="searchTerm" id="product" class="form-control ps-5" placeholder="Search product by product name" onkeyup="">
                                    <div id="search-box" class="shadow-sm rounded-bottom z-2 d-none">
                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-md-12 mb-3">
                                <span for="product">{{__('messages.items')}} <span class="text-danger">*</span></span>
                                <div class="table-responsive">
                                    <table class="table table-stripped w-100">
                                        <thead class="" style="background-color: #f8f9fa !important">
                                            <tr>
                                                <td>{{__('messages.table_elements.product')}}</td>
                                                <td>{{__('messages.table_elements.price')}}</td>
                                                <td>{{__('messages.table_elements.quantity')}}</td>
                                                <td>{{__('messages.table_elements.boxes')}}</td>
                                                <td>{{__('messages.table_elements.sub_total')}}</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orderDetails as $value)
                                                <tr>
                                                    <td>{{ $value->product_name }}</td>
                                                    <td>{{App\Helpers\SiteHelper::settings()['Currency_Icon']}}{{ $value->price }}</td>
                                                    <td>{{ $value->quantity }}</td>
                                                    <td>{{ (!empty($value->quantity) && !empty($value->pieces_in_box)) ? App\Helpers\SiteHelper::makeBoxes($value->quantity, $value->pieces_in_box) : 0 }}</td>
                                                    <td>{{App\Helpers\SiteHelper::settings()['Currency_Icon']}}{{ $value->price * $value->quantity }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row d-flex justify-content-end px-3">
                                    <div class="col-md-3 d-flex justify-content-between border border-secondary text-primary px-3 py-2">
                                        <div>
                                            <span>{{__('messages.grand_total')}}:</span>
                                        </div>
                                        <div>
                                            {{ \App\Helpers\SiteHelper::settings()['Currency_Icon'] }}<span>{{ $order->grand_total }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection