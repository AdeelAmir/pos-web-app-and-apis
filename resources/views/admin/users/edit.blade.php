@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">{{__('messages.edit_users_title')}}</h3>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                <form action="{{ route('users.update') }}" id="" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <h5 class="mb-3 mb-md-0">{{__('messages.edit_user_title')}}</h5>
                                    </div>
                                </div>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="user_id" id="id" class="form-control" value="{{ $user->id }}">
                                <input type="hidden" name="oldProfile" id="oldProfile" class="form-control" value="{{ $user->profile_image }}">
                                <div class="col-md-6 mb-3">
                                    <label for="profile" class="d-flex justify-content-between">{{ __('messages.profile') }} <a href="{{ $user->profile_image }}" download="{{ $user->profile_image }}"><i class="text-primary fa fa-download" aria-hidden="true"></i></a></label>
                                    <input type="file" name="profile" id="profile" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name">{{__('messages.name')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" required value="{{ $user->name }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email">{{__('messages.email')}} <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control" required value="{{ $seller->email }}" onblur="ValidateEmail()">
                                    <span id="email_error" class="text-danger fs-7" style="display: none"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password">{{__('messages.password')}} <span class="text-danger">*</span></label>
                                    <input type="password" name="password" id="password" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-3">
                            {{__('messages.btns.update_user')}}
                        </button>
                        <button type="button" class="btn btn-secondary"
                                onclick="window.location.href='{{route('users')}}'">
                            {{__('messages.btns.close')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection