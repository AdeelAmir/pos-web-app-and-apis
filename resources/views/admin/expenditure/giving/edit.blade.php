@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">{{ __('messages.edit_expenditure_giving_title') }}</h3>
            </div>
            <div class="col-md-6 offset-md-3 grid-margin stretch-card" id="sale-edit-page">
                <form action="{{ route('expenditure.giving.update') }}" id="" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="card mb-3">
                        <div class="card-body">
                            <input type="hidden" name="id" id="id" class="form-control" value="{{ $expenditure->id }}">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="expenditure_name">{{ __('messages.exchange_city_title') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="expenditure_name" id="expenditure_name" class="form-control" value="{{ $expenditure->name }}" 
                                    required  value="">
                                         <span class="text-danger">
                                            @error('expenditure_name')
                                                {{$message}}
                                            @enderror
                                         </span>   
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mt-2 d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary me-3">
                                        <i class="fa-solid fa-floppy-disk"></i> {{ __('messages.btns.save') }}
                                    </button>
                                    <button type="button" class="btn btn-secondary"
                                            onclick="window.location.href='{{route('expenditure.giving')}}'">
                                        {{ __('messages.btns.close') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection