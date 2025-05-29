@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="col-md-6 offset-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <h5 class="mb-3 mb-md-0">{{ __('messages.selling_city') }} > <span class="text-secondary">{{__('messages.edit')}}</span></h5>
                                </div>
                            </div>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('cities.selling.update') }}" id="" enctype="multipart/form-data" method="post">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="id" id="id" value="{{ $city->id }}">
                                <div class="col-md-12 mb-3">
                                    <label for="name">{{__('messages.name')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" required value="{{ $city->name }}">
                                </div>
                            </div>
                            <div class="col-md-12 mt-4 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary submitBtn me-3">
                                    {{ __('messages.btns.update_selling_city') }}
                                </button>
                                <button type="button" class="btn btn-secondary"
                                        onclick="window.location.href='{{route('cities.selling')}}'">
                                    {{__('messages.btns.close')}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection