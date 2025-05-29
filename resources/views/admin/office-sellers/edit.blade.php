@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">{{ __('messages.edit_office_sellers_title') }}</h3>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                <form action="{{ route('sellers.office.update') }}" id="" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="card mb-3">
                        <div class="card-header">
                            <h4 class="card-title">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <h5 class="mb-3 mb-md-0">{{ __('messages.edit_office_sellers_title') }}</h5>
                                    </div>
                                </div>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="id" id="id" class="form-control" value="{{ $seller->id }}">
                                <input type="hidden" name="oldProfile" id="oldProfile" class="form-control" value="{{ $seller->profile_image }}">
                                <div class="col-md-6 mb-3">
                                    <label for="seller_id">{{__('messages.seller_id')}} <span class="text-danger">*</span></label>
                                    <input type="number" name="seller_id" id="seller_id" class="form-control" disabled required value="{{ $seller->id }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profile" class="d-flex justify-content-between">{{ __('messages.profile') }} <a href="{{ $seller->profile_image }}" download="{{ $seller->profile_image }}"><i class="text-primary fa fa-download" aria-hidden="true"></i></a></label>
                                    <input type="file" name="profile" id="profile" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name">{{__('messages.name')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" required value="{{ $seller->name }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email">{{__('messages.email')}} <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control" required value="{{ $seller->email }}" onblur="ValidateEmail()">
                                    <span id="email_error" class="text-danger fs-7" style="display: none"></span>
                                </div>
                                {{-- <div class="col-md-6 mb-3">
                                    <label for="phone">{{__('messages.phone_number')}} <span class="text-danger">*</span></label>
                                    <input type="number" name="phone" id="phone" class="form-control" required value="{{ $seller->phone }}">
                                </div> --}}
                                {{-- <div class="col-md-6 mb-3">
                                    <label for="password">{{__('messages.password')}} <span class="text-danger">*</span></label>
                                    <input type="password" name="password" id="password" class="form-control">
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="description">{{__('messages.description')}}:</label>
                                    <textarea name="description" id="description" class="form-control" cols="20" rows="5">{{ $seller->description }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4 d-flex justify-content-end">
                        <button type="submit" id="submit-btn" class="btn btn-primary me-3">
                            <i class="fa-solid fa-floppy-disk"></i> {{__('messages.btns.update_office_seller')}}
                        </button>
                        <button type="button" class="btn btn-secondary"
                                onclick="window.location.href='{{route('sellers')}}'">
                            {{__('messages.btns.close')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection