@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">Damage/Replace</h3>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card" id="damage-edit-page">
                <form action="{{ route('damage_replace.update') }}" id="" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="card mb-3">
                        <div class="card-body">
                            <input type="hidden" name="jsonProducts" id="jsonProducts" class="form-control" value="{{ $jsonData }}">
                            <input type="hidden" name="id" id="id" class="form-control" value="{{ $damage->id }}">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date">{{__('messages.date')}} <span class="text-danger">*</span></label>
                                    <input type="date" name="date" id="date" class="form-control" required value="{{ $damage->date }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name">Seller Name/ID <span class="text-danger">*</span></label>
                                    <select name="seller_id" id="seller_id" class="form-control form-select select2" required>
                                        <option value="">Select</option>
                                        @foreach ($sellers as $seller)
                                            <option value="{{ $seller->id }}" @if($seller->id == $damage->seller_id) selected @endif>{{ $seller->name . ' / ' . $seller->id }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="city_id">City <span class="text-danger">*</span></label>
                                    <select name="city_id" id="city_id" class="form-control form-select select2" required>
                                        <option value="">City Name</option>
                                        @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" @if($city->id == $damage->city_id) selected @endif>{{ $city->name }}</option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="product">{{__('messages.product')}} <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <i class="bx bx-search fs-4 search-icon"></i>
                                        <input type="text" name="searchTerm" id="product" class="form-control ps-5" placeholder="{{__('messages.search_product_by_name')}}" onkeyup="DamageSearchBox()">
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
                                                    <td>{{__('messages.table_elements.stock')}}</td>
                                                    <td>{{__('messages.table_elements.boxes')}}</td>
                                                    <td>{{__('messages.table_elements.sub_total')}}</td>
                                                    <td>{{__('messages.table_elements.replace')}}</td>
                                                    <td>{{__('messages.table_elements.action')}}</td>
                                                </tr>
                                            </thead>
                                            <tbody id="damage-product-table-body">
                                                @foreach ($damageItems as $data)
                                                    <tr id="tr-{{ $data->product_id }}">
                                                        <td>{{ $data->name }}</td>
                                                        <td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}<span id="damage-price-{{ $data->product_id }}">{{ $data->retail_price }}</span></td>
                                                        <td id="damage-stock-{{ $data->product_id }}">{{ $data->total_stock }}</td>
                                                        <td id="damage-boxes-{{ $data->product_id }}">{{ $data->pieces }}</td>
                                                        <td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}<span id="damage-sub-total-{{ $data->product_id }}">{{ $data->sub_total }}</span></td>
                                                        <td><input type="number" class="form-control input-number-damage" id="damage-quantity-{{ $data->product_id }}" name="damage_quantity" value="{{ $data->quantity }}" min="1" required onkeyup="changeDamage({{ $data->product_id }})"></td>
                                                        <td><span id="" onclick="removeDamageElementFromTable({{ $data->product_id }})" class="text-danger fs-6 cursor-pointer ml-1" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></span></td>
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
                                                {{App\Helpers\SiteHelper::settings()['Currency_Icon']}}<span id="damageGrandTotal">{{ $damage->grand_total }}</span>
                                                <input type="hidden" id="damage_grand_total" name="grand_total" value="{{ $damage->grand_total }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-3">
                            <i class="fa-solid fa-floppy-disk"></i> Save
                        </button>
                        <button type="button" class="btn btn-secondary"
                                onclick="window.location.href='{{route('damage_replace')}}'">
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection