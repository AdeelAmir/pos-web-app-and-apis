@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">{{ __('messages.edit_expenditure_seller_title') }}</h3>
            </div>
            <div class="col-md-12 grid-margin stretch-card">
                <form action="{{ route('expenditure.seller.update') }}" id="" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="card mb-3">
                        <div class="card-body">
                            <input type="hidden" name="id" id="id" class="form-control" required value="{{ $officeExpenditure->id }}">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date">{{ __('messages.date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="expenditure_date" id="date" class="form-control" required value="{{ $officeExpenditure->expenditure_date }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="seller_id">{{ __('messages.seller_name_id') }}</label>
                                    <select name="seller_id" class="form-control form-select select2" required>
                                        <option value="">{{ __('messages.select') }}</option>
                                        @foreach ($sellers as $index => $seller)
                                            <option value="{{ $seller->id }}" @if($seller->id == $officeExpenditure->seller_id) selected @endif>{{ $seller->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="repeater-custom-show-hide">
                                        <div data-repeater-list="seller_expense_repeater">
                                            @foreach($officeExpenditureDetails as $data)
                                                <div data-repeater-item="" style="box-shadow: 0 5px 16px rgba(8, 15, 52, 0.06);" class="p-3 mb-3">
                                                    <div class="row mb-0">
                                                        <div class="col-6">
                                                            <label class="font-weight-bold text-black">{{ __('messages.expenditure') }}</label>
                                                            <select name="expenditure_id" class="form-control form-select select2" required>
                                                                <option value="">{{ __('messages.select') }}</option>
                                                                @foreach ($expenditures as $index => $expenditure)
                                                                    <option value="{{ $expenditure->id }}" @if($expenditure->id == $data->expenditure_id) selected @endif>{{ $expenditure->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="font-weight-bold text-black">{{ __('messages.amount') }}</label>
                                                            <input type="number" class="form-control" name="amount" min="1" value="{{ $data->amount }}">
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
                                            @endforeach
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
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-2 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-3">
                                <i class="fa-solid fa-floppy-disk"></i> {{ __('messages.btns.save') }}
                            </button>
                            <button type="button" class="btn btn-secondary"
                                    onclick="window.location.href='{{route('expenditure.office')}}'">
                                {{ __('messages.btns.close') }}
                            </button>
                        </div>
                    </div>  
                </form>
            </div>
        </div>
    </div>
@endsection