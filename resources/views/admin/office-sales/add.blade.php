@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">{{__('messages.add_sale_title')}}</h3>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                <form action="{{ route('sales.office.store') }}" id="office-sale-form" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="card mb-3">
                        <div class="card-body">
                            <input type="hidden" name="jsonProducts" id="jsonProducts" class="form-control">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date">{{__('messages.date')}} <span class="text-danger">*</span></label>
                                    <input type="date" name="date" id="date" class="form-control" required value="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name">{{__('messages.seller_name_id')}} <span class="text-danger">*</span></label>
                                    <select name="seller_id" id="seller_id" class="form-control form-select select2" required>
                                        <option value="">{{__('messages.select')}}</option>
                                        @foreach ($sellers as $seller)
                                            <option value="{{ $seller->id }}">{{ $seller->name . ' / ' . $seller->id }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="city_id">{{ __('messages.warehouse_city') }} <span class="text-danger">*</span></label>
                                    <select name="city_id" id="city_id" class="form-control form-select select2" required>
                                        <option value="">{{__('messages.select')}}</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="selling_city_id">{{ __('messages.selling_city') }} <span class="text-danger">*</span></label>
                                    <select name="selling_city_id" id="selling_city_id" class="form-control form-select select2" required>
                                        <option value="">{{__('messages.select')}}</option>
                                        @foreach ($sellingCities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="price_type">{{ __('messages.price_type') }} <span class="text-danger">*</span></label>
                                    <select name="payment_type" id="price_type" class="form-control form-select select2" onchange="PriceTypeSelected()" required>
                                        <option value="">{{__('messages.select')}}</option>
                                        <option value="retail_price" selected>{{__('messages.retail_price')}}</option>
                                        <option value="wholesale_price">{{__('messages.wholesale_price')}}</option>
                                        <option value="extra_price">{{__('messages.extra_price')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="payment_type">{{__('messages.payment_type')}} <span class="text-danger">*</span></label>
                                    <div class="mt-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="office_payment_type" id="office_payment_type_cash" value="Cash" required>
                                            <label class="form-check-label" for="office_payment_type_cash">{{ __('messages.cash') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="office_payment_type" id="office_payment_type_credit" value="Credit" required>
                                            <label class="form-check-label" for="office_payment_type_credit">{{ __('messages.credit') }}</label>
                                        </div>
                                    </div>
                                    <span id="bonus_error" class="text-danger fs-7" style="display: none"></span>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="product">{{__('messages.product')}} <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <i class="bx bx-search fs-4 search-icon"></i>
                                        <input type="text" name="searchTerm" id="product" class="form-control ps-5" placeholder="{{__('messages.search_product_by_name')}}" onkeyup="SearchBox()">
                                        <div id="search-box" class="shadow-sm rounded-bottom z-2 d-none">
                                        </div>
                                        <span id="product_error" class="text-danger fs-7" style="display: none"></span>
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
                                                    <td>{{__('messages.table_elements.total_storage')}}</td>
                                                    <td>{{__('messages.table_elements.boxes')}}</td>
                                                    <td>{{__('messages.table_elements.quantity')}}</td>
                                                    <td>{{__('messages.table_elements.sub_total')}}</td>
                                                    <td>{{__('messages.table_elements.action')}}</td>
                                                </tr>
                                            </thead>
                                            <tbody id="sale-product-table-body">
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
                                                {{App\Helpers\SiteHelper::settings()['Currency_Icon']}}<span id="grandTotal">0</span>
                                                <input type="hidden" id="grand_total" name="grand_total" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary me-3" onclick="SubmitOfficeSaleForm()">
                            <i class="fa-solid fa-floppy-disk"></i> {{__('messages.btns.create_office_sale')}}
                        </button>
                        <button type="button" class="btn btn-secondary px-4 py-2 ms-1"
                                onclick="window.location.href='{{route('sales.office')}}'">
                            {{__('messages.btns.close')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection