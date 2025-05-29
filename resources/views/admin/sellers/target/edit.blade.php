@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">{{ __('messages.edit_seller_target_title') }}</h3>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                <form action="{{ route('sellers.target.update') }}" id="" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="card mb-3">
                        <div class="card-header">
                            <h4 class="card-title">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <h5 class="mb-3 mb-md-0">{{ __('messages.edit_seller_target_title') }}</h5>
                                    </div>
                                </div>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" id="target_id" name="id" value="{{ $id }}">
                                <input type="hidden" id="seller_id" name="seller_id" value="{{ $seller_id }}">
                                <div class="col-md-12 mb-3">
                                    <label for="month">{{ __('messages.month') }} <span class="text-danger">*</span></label>
                                    <select name="month" id="month" class="form-select select2" required>
                                        <option value="">{{ __('messages.select') }}</option>
                                        <option value="January" @if($target->month == 'January') selected @endif>January</option>
                                        <option value="February" @if($target->month == 'February') selected @endif>February</option>
                                        <option value="March" @if($target->month == 'March') selected @endif>March</option>
                                        <option value="April" @if($target->month == 'April') selected @endif>April</option>
                                        <option value="May" @if($target->month == 'May') selected @endif>May</option>
                                        <option value="June" @if($target->month == 'June') selected @endif>June</option>
                                        <option value="July" @if($target->month == 'July') selected @endif>July</option>
                                        <option value="August" @if($target->month == 'August') selected @endif>August</option>
                                        <option value="September" @if($target->month == 'September') selected @endif>September</option>
                                        <option value="October" @if($target->month == 'October') selected @endif>October</option>
                                        <option value="November" @if($target->month == 'November') selected @endif>November</option>
                                        <option value="December" @if($target->month == 'December') selected @endif>December</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="year">{{ __('messages.year') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="year" id="edit_year" class="form-control" min="0" value="{{ $target->year }}" required>
                                </div>
                                <div class="repeater-custom-show-hide">
                                    <div data-repeater-list="target_repeater">
                                        @foreach($sellerTargetDetails as $data)
                                            <div data-repeater-item=""
                                                style="box-shadow: 0 5px 16px rgba(8, 15, 52, 0.06);"
                                                class="p-3 mb-3">
                                                <div class="row mb-0">
                                                    <div class="col-6">
                                                        <label for="product_id">{{ __('messages.product') }} <span class="text-danger">*</span></label>
                                                        <select name="product_id" id="product_id" class="form-select select2" required>
                                                            <option value="">{{ __('messages.select') }}</option>
                                                            @foreach ($products as $product)
                                                                <option value="{{ $product->id }}" @if($product->id == $data->product_id) selected @endif>{{ $product->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <label for="quantity">{{ __('messages.quantity') }} ({{ __('messages.pieces') }}) <span class="text-danger">*</span></label>
                                                        <input type="number" name="quantity" id="quantity" class="form-control" min="0" value="{{ $data->quantity }}" required>
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
                    <div class="col-md-12 mt-4 d-flex justify-content-end">
                        <button type="submit" id="submit-btn" class="btn btn-primary me-3">
                            <i class="fa-solid fa-floppy-disk"></i> {{ __('messages.btns.update_seller_target') }}
                        </button>
                        <button type="button" class="btn btn-secondary"
                                onclick="window.location.href='{{route('sellers.target', ['Id' => $id])}}'">
                            {{__('messages.btns.close')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection