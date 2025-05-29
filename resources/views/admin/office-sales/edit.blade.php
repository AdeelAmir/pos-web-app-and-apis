@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">{{__('messages.edit_office_sales_title')}}</h3>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card" id="sale-edit-page">
                <form action="{{ route('sales.office.update') }}" id="sale-form" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="card mb-3">
                        <div class="card-body">
                            <input type="hidden" name="jsonProducts" id="jsonProducts" class="form-control" value="{{ $jsonProducts }}">
                            <input type="hidden" name="id" id="id" class="form-control" value="{{ $sale->id }}">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date">{{__('messages.date')}} <span class="text-danger">*</span></label>
                                    <input type="date" name="date" id="date" class="form-control" required value="{{ $sale->date }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name">{{__('messages.seller_name_id')}} <span class="text-danger">*</span></label>
                                    <select name="seller_id" id="seller_id" class="form-control form-select select2" required>
                                        <option value="">{{__('messages.select')}}</option>
                                        @foreach ($sellers as $seller)
                                            <option value="{{ $seller->id }}" @if($seller->id == $sale->seller_id) selected @endif>{{ $seller->name . ' / ' . $seller->id }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="city_id">{{__('messages.warehouse_city')}} <span class="text-danger">*</span></label>
                                    <select name="city_id" id="city_id" class="form-control form-select select2" required>
                                        <option value="">{{__('messages.select')}}</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}" @if($city->id == $sale->city_id) selected @endif>{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="selling_city_id">{{__('messages.selling_city')}} <span class="text-danger">*</span></label>
                                    <select name="selling_city_id" id="selling_city_id" class="form-control form-select select2" required>
                                        <option value="">{{__('messages.select')}}</option>
                                        @foreach ($sellingCities as $city)
                                            <option value="{{ $city->id }}" @if($city->id == $sale->selling_city_id) selected @endif>{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="payment_type">{{__('messages.payment_type')}} <span class="text-danger">*</span></label>
                                    <select name="payment_type" id="payment_type" class="form-control form-select select2" required>
                                        <option value="">{{__('messages.select')}}</option>
                                        <option value="retail_price" @if($sale->payment_type == 'retail_price') selected @endif>{{__('messages.retail_price')}}</option>
                                        <option value="wholesale_price" @if($sale->payment_type == 'wholesale_price') selected @endif>{{__('messages.wholesale_price')}}</option>
                                        <option value="extra_price" @if($sale->payment_type == 'extra_price') selected @endif>{{__('messages.extra_price')}}</option>
                                    </select>
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
                                    <span for="product">Items <span class="text-danger">*</span></span>
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
                                                @foreach ($saleDetails as $value)
                                                    <tr id="tr-{{ $value->product_id }}">
                                                        <td>{{ $value->name }}</td>
                                                        <td id="price-{{ $value->product_id }}">{{ $value->retail_price }}</td>
                                                        <td id="stock-{{ $value->product_id }}">{{ $value->total_stock }}</td>
                                                        <td id="boxes-{{ $value->product_id }}">{{ (!empty($value->total_stock) && !empty($value->pieces_in_box)) ? App\Helpers\SiteHelper::makeBoxes($value->total_stock, $value->pieces_in_box) : 0 }}</td>
                                                        <td>
                                                            <div class="d-flex">
                                                                <div>
                                                                    <button type="button" id="minus-btn" class="btn btn-primary quantity-left-minus btn-number px-2" onclick="removeQuantity({{ $value->product_id }});">
                                                                        <span class="fa fa-minus"></span>
                                                                    </button>
                                                                </div>
                                                                <div>
                                                                    <input type="number" id="quantity-{{ $value->product_id }}" name="quantity" class="form-control input-number" value="{{ $value->quantity }}" min="1" onkeyup="incrementSubTotal({{ $value->product_id }})">
                                                                </div>
                                                                <div>
                                                                    <button type="button" id="plus-btn" class="btn btn-primary quantity-right-plus btn-number px-2" onclick="addQuantity({{ $value->product_id }});">
                                                                        <span class="fa fa-plus"></span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td id="sub-total-price-{{ $value->product_id }}">{{ $value->sub_total }}</td>
                                                        <td><span id="" onclick="removeElementFormTable({{ $value->product_id }})" class="text-danger fs-6 cursor-pointer ml-1" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></span></td>
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
                    <div class="col-md-12 mt-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary me-3" onclick="SubmitSaleForm()">
                            <i class="fa-solid fa-floppy-disk"></i> {{__('messages.btns.update_office_sale')}}
                        </button>
                        <button type="button" class="btn btn-secondary px-4 py-2 ms-1"
                                onclick="window.location.href='{{route('sales')}}'">
                            {{__('messages.btns.close')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection