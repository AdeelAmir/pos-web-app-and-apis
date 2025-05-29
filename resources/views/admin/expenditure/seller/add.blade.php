@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">{{ __('messages.add_expenditure_seller_title') }}</h3>
            </div>
            <div class="col-md-12 grid-margin stretch-card">
                <form action="{{ route('expenditure.seller.store') }}" id="" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date">={{ __('messages.date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="expenditure_date" id="date" class="form-control" 
                                           required value="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="date">{{ __('messages.seller_name_id') }} <span class="text-danger">*</span></label>
                                    <select name="seller_id" class="form-control form-select select2" required>
                                        <option value="">{{ __('messages.select') }}</option>
                                        @foreach ($sellers as $index => $value)
                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="repeater-custom-show-hide">
                                    <div data-repeater-list="seller_expense_repeater">
                                        <div data-repeater-item="" style="box-shadow: 0 5px 16px rgba(8, 15, 52, 0.06);" class="p-3 mb-3">
                                            <div class="row mb-0">
                                                <div class="col-6">
                                                    <label class="font-weight-bold text-black">{{ __('messages.expenditure') }}</label>
                                                    <select name="expenditure_id" class="form-control form-select select2" required>
                                                        <option value="">{{ __('messages.select') }}</option>
                                                        @foreach ($expenditures as $index => $expenditure)
                                                            <option value="{{ $expenditure->id }}">{{ $expenditure->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="font-weight-bold text-black">{{ __('messages.amount') }}</label>
                                                    <input type="number" class="form-control" name="amount" min="1">
                                                </div>
                                                <div class="col-md-12 mt-2 mb-0">
                                                    <div class="">
                                                        <span data-repeater-delete="" class="btn btn-outline-danger btn-sm">
                                                            <span class="far fa-trash-alt mr-1"></span>&nbsp; {{ __('messages.btns.delete') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-0">
                                        <div class="col-sm-12">
                                            <span data-repeater-create="" class="btn btn-outline-primary btn-sm float-right">
                                                <span class="fa fa-plus"></span>&nbsp; {{ __('messages.btns.add') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-2 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-3">
                                <i class="fa-solid fa-floppy-disk"></i> {{ __('messages.btns.save') }}
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='{{route('expenditure.seller')}}'">
                                {{ __('messages.btns.close') }}
                            </button>
                        </div>
                    </div>  
                </form>
            </div>
        </div>
    </div>
@endsection