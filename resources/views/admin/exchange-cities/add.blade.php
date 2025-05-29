@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">{{__('messages.exchange_city_title')}}</h3>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                <form action="{{ route('exchange_city.store') }}" id="" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="card mb-3">
                        <input type="hidden" id="jsonProducts" name="jsonProducts" class="form-control">
                        <input type="hidden" id="sale_id" name="sale_id" class="form-control">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date">{{__('messages.date')}} <span class="text-danger">*</span></label>
                                    <input type="date" name="date" id="date" class="form-control" required value="{{ now()->format('Y-m-d') }}" onchange="getProductsForExchangeCity()">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name">{{__('messages.seller_name_id')}} <span class="text-danger">*</span></label>
                                    <select name="seller_id" id="seller_id" class="form-control form-select select2" required onchange="getProductsForExchangeCity()">
                                        <option value="">{{__('messages.select')}}</option>
                                        @foreach ($sellers as $seller)
                                            <option value="{{ $seller->id }}">{{ $seller->name . ' / ' . $seller->id }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="from_city_id">{{__('messages.from_city')}} <span class="text-danger">*</span></label>
                                    <select name="from_city_id" id="from_city_id" class="form-control form-select select2" required onchange="getProductsForExchangeCity()">
                                        <option value="">{{__('messages.city_name')}}</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="to_city_id">{{ __('messages.to_city') }} <span class="text-danger">*</span></label>
                                    <select name="to_city_id" id="to_city_id" class="form-control form-select select2" required>
                                        <option value="">{{__('messages.city_name')}}</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <span>{{__('messages.items')}} <span class="text-danger">*</span></span>
                                    <div class="table-responsive">
                                        <table class="table table-stripped w-100">
                                            <thead class="" style="background-color: #f8f9fa !important">
                                                <tr>
                                                    <td>{{__('messages.table_elements.product')}}</td>
                                                    <td>{{__('messages.table_elements.price')}}</td>
                                                    <td>{{__('messages.table_elements.total_giving_stock')}}</td>
                                                    <td>{{__('messages.table_elements.remaining_stock')}}</td>
                                                    <td>{{__('messages.table_elements.boxes')}}</td>
                                                    <td>{{__('messages.table_elements.sub_total')}}</td>
                                                    <td>{{__('messages.table_elements.return')}}</td>
                                                </tr>
                                            </thead>
                                            <tbody id="exchange_city_products_table">
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
                                                {{App\Helpers\SiteHelper::settings()['Currency_Icon']}}<span id="exchangeCityGrandTotal">0</span>
                                                <input type="hidden" id="exchange_city_grand_total" name="exchange_city_grand_total" value="0">
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
