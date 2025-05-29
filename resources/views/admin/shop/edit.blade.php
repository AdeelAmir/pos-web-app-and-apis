@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">{{__('messages.edit_shop_title')}}</h3>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                <form action="{{ route('shops.update') }}" id="" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="id" id="id" class="form-control" value="{{ $shop->id }}">
                                {{-- <div class="col-md-6 mb-3">
                                    <label for="shop_id">Shop Id <span class="text-danger">*</span></label>
                                    <input type="number" name="shop_id" id="shop_id" class="form-control" value="{{ $shop->id }}" disabled required>
                                </div> --}}
                                <div class="col-md-6 mb-3">
                                    <label for="name">{{__('messages.shop_name')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" required value="{{ $shop->name }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="city_id">{{__('messages.city')}} <span class="text-danger">*</span></label>
                                    <select name="city_id" id="city_id" class="form-control form-select select2" required>
                                        <option value="">{{__('messages.select')}}</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}" @if($city->id == $shop->city_id) selected @endif>{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="location">{{__('messages.location')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="location" id="location" class="form-control addressField" required value="{{ $shop->location }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="address">{{__('messages.address')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="address" id="address" class="form-control addressField" required value="{{ $shop->address }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="micro_district">{{ __('messages.micro_district') }}</label>
                                    <input type="text" name="micro_district" id="micro_district" class="form-control" value="{{ $shop->micro_district }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="latitude">{{ __('messages.latitude') }}</label>
                                    <input type="text" name="latitude" id="latitude" class="form-control" value="{{ $shop->latitude }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitude">{{ __('messages.longitude') }}</label>
                                    <input type="text" name="longitude" id="longitude" class="form-control" value="{{ $shop->longitude }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="description">{{__('messages.description')}}:</label>
                                    <textarea name="description" id="description" class="form-control" cols="20" rows="5">{{ $shop->description }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-3">
                            <i class="fa-solid fa-floppy-disk"></i> {{__('messages.btns.update_shop')}}
                        </button>
                        <button type="button" class="btn btn-secondary"
                                onclick="window.location.href='{{route('shops')}}'">
                            {{__('messages.btns.close')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection