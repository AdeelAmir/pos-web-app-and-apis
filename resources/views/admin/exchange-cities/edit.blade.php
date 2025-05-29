@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">{{__('messages.exchange_city_title')}}</h3>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                <form action="{{ route('exchange_city.update') }}" id="" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="card mb-3">
                        <input type="hidden" id="jsonProducts" name="jsonProducts" class="form-control" value="{{ $jsonProducts }}">
                        <input type="hidden" id="sale_id" name="sale_id" class="form-control" value="{{ $exchangeCity->sale_id }}">
                        <input type="hidden" id="id" name="id" class="form-control" value="{{ $exchangeCity->id }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date">{{__('messages.date')}} <span class="text-danger">*</span></label>
                                    <input type="date" name="date" id="date" class="form-control" required value="{{ $exchangeCity->date }}" onchange="getProductsForExchangeCity()">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name">{{__('messages.seller_name')}} <span class="text-danger">*</span></label>
                                    <select name="seller_id" id="seller_id" class="form-control form-select select2" required onchange="getProductsForExchangeCity()">
                                        <option value="">{{__('messages.select')}}</option>
                                        @foreach ($sellers as $seller)
                                            <option value="{{ $seller->id }}" @if($seller->id == $exchangeCity->seller_id) selected @endif>{{ $seller->name . ' / ' . $seller->id }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="from_city_id">{{__('messages.from_city')}} <span class="text-danger">*</span></label>
                                    <select name="from_city_id" id="from_city_id" class="form-control form-select select2" required onchange="getProductsForExchangeCity()">
                                        <option value="">{{__('messages.city_name')}}</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}" @if($city->id == $exchangeCity->city_id) selected @endif>{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="to_city_id">{{ __('messages.to_city') }} <span class="text-danger">*</span></label>
                                    <select name="to_city_id" id="to_city_id" class="form-control form-select select2" required>
                                        <option value="">{{__('messages.city_name')}}</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}" @if($city->id == $exchangeCity->to_city_id) selected @endif>{{ $city->name }}</option>
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
                                            <tbody id="exchange_city_products_table">
                                                @foreach ($exchangeCityItems as $data)
                                                    <tr>
                                                        <td>{{ $data->name }}</td>
                                                        <td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}<span id="exchange-city-price-{{ $data->id }}">{{ $data->retail_price }}</span></td>
                                                        <td class="text-primary">{{ $data->total_stock }}</td>
                                                        <td id="exchange-city-remaining-product-{{ $data->id }}">{{ $data->remaining_product }}</td>
                                                        <td id="exchange-city-boxes-{{ $data->id }}">{{ $data->pieces }}</td>
                                                        <td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}<span id="exchange-city-sub-total-{{ $data->id }}">{{ $data->sub_total }}</span></td>
                                                        <td><input type="number" class="form-control input-number-return" id="exchange-city-quantity-{{ $data->id }}" name="exchange_city_quantity" value="{{ $data->return_quantity }}" onkeyup="changeExchangeCity({{ $data->id }})"></td>
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
                                                {{App\Helpers\SiteHelper::settings()['Currency_Icon']}}<span id="exchangeCityGrandTotal">{{ $exchangeCity->grand_total }}</span>
                                                <input type="hidden" id="exchange_city_grand_total" name="exchange_city_grand_total" value="{{ $exchangeCity->grand_total }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-3">
                            <i class="fa-solid fa-floppy-disk"></i> {{ __('messages.btns.save') }}
                        </button>
                        <button type="button" class="btn btn-secondary"
                                onclick="window.location.href='{{route('exchange_city')}}'">
                            {{ __('messages.btns.close') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
