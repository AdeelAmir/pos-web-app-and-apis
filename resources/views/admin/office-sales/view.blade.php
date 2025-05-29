@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3 d-flex justify-content-between">
                <h3 class="mb-0">{{__('messages.view_sale_title')}}</h3>
                <div class="">
                    <button type="submit" class="btn btn-primary me-3" onclick="openSalePrintModal({{ $sale->id }})">
                        <i class="fa fa-print me-1"></i> {{__('messages.btns.print')}}
                    </button>
                    <button type="button" class="btn btn-secondary px-4 py-2 ms-1" onclick="window.location.href='{{route('sales.office')}}'">
                        <i class="fa fa-chevron-left me-1"></i> {{__('messages.btns.back')}}
                    </button>
                </div>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date">{{__('messages.date')}} <span class="text-danger">*</span></label>
                                <input type="date" name="date" id="date" class="form-control" readonly value="{{ $sale->date }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="name">{{__('messages.seller_name_id')}} <span class="text-danger">*</span></label>
                                <input type="text" name="seller_id" id="seller_id" class="form-control" readonly value="{{ $seller->name . ' / ' . $seller->id }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="city">Warehouse City <span class="text-danger">*</span></label>
                                <input type="text" name="city" id="city" class="form-control" readonly value="{{ $sale->warehouse_city }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="city">Selling City <span class="text-danger">*</span></label>
                                <input type="text" name="city" id="city" class="form-control" readonly value="{{ $sale->selling_city }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="city">Price Type <span class="text-danger">*</span></label>
                                <input type="text" name="city" id="city" class="form-control" readonly value="{{ $sale->payment_type == 'retail_price' ? 'Retail Price' : '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="city">Payment Type <span class="text-danger">*</span></label>
                                <input type="text" name="city" id="city" class="form-control" readonly value="{{ $sale->office_payment_type }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="product">{{__('messages.product')}} <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <i class="bx bx-search fs-4 search-icon"></i>
                                    <input type="text" name="searchTerm" id="product" class="form-control ps-5" placeholder="{{__('messages.search_product_by_name')}}" onkeyup="">
                                    <div id="search-box" class="shadow-sm rounded-bottom z-2 d-none">
                                    </div>
                                </div>
                            </div>
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
                                            @foreach ($saleDetails as $value)
                                                <tr>
                                                    <td>{{ $value->name }}</td>
                                                    <td>{{App\Helpers\SiteHelper::settings()['Currency_Icon']}}{{ $value->retail_price }}</td>
                                                    <td>{{ $value->quantity }}</td>
                                                    <td>{{ (!empty($value->quantity) && !empty($value->pieces_in_box)) ? App\Helpers\SiteHelper::makeBoxes($value->quantity, $value->pieces_in_box) : 0 }}</td>
                                                    <td>{{App\Helpers\SiteHelper::settings()['Currency_Icon']}}{{ $value->sub_total }}</td>
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
                                            {{App\Helpers\SiteHelper::settings()['Currency_Icon']}}<span id="grandTotal">{{ $sale->grand_total }}</span>
                                            <input type="hidden" id="grand_total" name="grand_total" value="{{ $sale->grand_total }}">
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
    @include('admin.sales.print')
@endsection