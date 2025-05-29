@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">{{__('messages.edit_return_title')}}</h3>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                <form action="{{ route('returns.update') }}" id="" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="card mb-3">
                        <input type="hidden" id="jsonReturnData" name="jsonReturnData" class="form-control" value="{{ $jsonReturnData }}">
                        <input type="hidden" id="sale_id" name="sale_id" class="form-control" value="{{ $return->sale_id }}">
                        <input type="hidden" id="id" name="id" class="form-control" value="{{ $return->id }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date">{{__('messages.date')}} <span class="text-danger">*</span></label>
                                    <input type="date" name="date" id="date" class="form-control" required value="{{ $return->date }}" onchange="getProductsForReturn()">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name">{{__('messages.seller_name')}} <span class="text-danger">*</span></label>
                                    <select name="seller_id" id="seller_id" class="form-control form-select select2" required onchange="getProductsForReturn()">
                                        <option value="">{{__('messages.select')}}</option>
                                        @foreach ($sellers as $seller)
                                            <option value="{{ $seller->id }}" @if($seller->id == $return->seller_id) selected @endif>{{ $seller->name . ' / ' . $seller->id }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="city_id">{{__('messages.city')}} <span class="text-danger">*</span></label>
                                    <select name="city_id" id="city_id" class="form-control form-select select2" required onchange="getProductsForReturn()">
                                        <option value="">{{__('messages.city_name')}}</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}" @if($city->id == $return->city_id) selected @endif>{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <span for="product">{{__('messages.items')}} <span class="text-danger">*</span></span>
                                    <div class="table-responsive">
                                        <table class="table table-stripped w-100">
                                            <thead class="" style="background-color: #f8f9fa !important">
                                                <tr>
                                                    <td>{{__('messages.table_elements.product')}}</td>
                                                    <td>{{__('messages.table_elements.price')}}</td>
                                                    <td>{{__('messages.table_elements.stock')}}</td>
                                                    <td>{{__('messages.table_elements.remaining_stock')}}</td>
                                                    <td>{{__('messages.table_elements.boxes')}}</td>
                                                    <td>{{__('messages.table_elements.sub_total')}}</td>
                                                    <td>{{__('messages.table_elements.return')}}</td>
                                                </tr>
                                            </thead>
                                            <tbody id="return_products_table">
                                                @foreach ($returnDetails as $data)
                                                    <tr>
                                                        <td>{{ $data->name }}</td>
                                                        <td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}<span id="return-price-{{ $data->id }}">{{ $data->retail_price }}</span></td>
                                                        <td class="text-primary">{{ $data->total_stock }}</td>
                                                        <td id="return-remaining-product-{{ $data->id }}">{{ $data->remaining_product }}</td>
                                                        <td id="return-boxes-{{ $data->id }}">{{ $data->pieces }}</td>
                                                        <td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}<span id="return-sub-total-{{ $data->id }}">{{ $data->sub_total }}</span></td>
                                                        <td><input type="number" class="form-control input-number-return" id="return-quantity-{{ $data->id }}" name="return_quantity" value="{{ $data->return_quantity }}" min="1" onkeyup="changeReturn({{ $data->id }})"></td>
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
                                                <span>Grand Total:</span>
                                            </div>
                                            <div>
                                                {{App\Helpers\SiteHelper::settings()['Currency_Icon']}}<span id="returnGrandTotal">{{ $return->grand_total }}</span>
                                                <input type="hidden" id="return_grand_total" name="return_grand_total" value="{{ $return->grand_total }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-3">
                            <i class="fa-solid fa-floppy-disk"></i> Update
                        </button>
                        <button type="button" class="btn btn-secondary px-4 py-2 ms-1"
                                onclick="window.location.href='{{route('sellers')}}'">
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection