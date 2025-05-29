@extends('admin.layouts.app')
@section('content')
    <style>
        .rounded_circle{
            height: 150px !important;
            width: 150px !important;
            border-radius: 50% !important;
        }
    </style>
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3 d-flex justify-content-between">
                <h3 class="mb-0">{{__('messages.view_user_title')}}</h3>
                <button type="button" class="btn btn-secondary px-4 py-2 ms-1" onclick="window.location.href='{{route('users')}}'">
                    <i class="fa fa-chevron-left me-1" aria-hidden="true"></i> {{__('messages.btns.back')}}
                </button>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <h5 class="mb-3 mb-md-0">{{__('messages.general_detail')}}</h5>
                                </div>
                            </div>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-3">
                                <img src="{{ $user->profile_image }}" alt="Profile Image" class="rounded_circle object-fit-cover">
                            </div>
                            <div class="col-12 col-md-9">
                                <div class="mb-1">{{__('messages.table_elements.seller_id')}}: {{ $user->id }}</div>
                                <div class="mb-1">{{__('messages.table_elements.seller_name')}}: {{ $user->name }}</div>
                                {{-- <div>Phone Number: {{ $user->phone }}</div> --}}
                                <div class="mb-1">{{__('messages.table_elements.email')}}: {{ $user->email }}</div>
                                <div class="mb-1">{{__('messages.table_elements.status')}}: @if($user->status == 1) <span class="btn-sm btn-success cursor-pointer">Active</span> @else <span class="btn-sm btn-danger cursor-pointer">Ban</span> @endif</div>
                                {{-- <div>Description:</div>
                                <p>{{ $user->description }}</p> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection