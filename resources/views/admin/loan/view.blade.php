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
                <h3 class="mb-0">Loan View</h3>
                <button type="button" class="btn btn-secondary px-4 py-2 ms-1" onclick="window.location.href='{{route('loan')}}'">
                    <i class="fa fa-chevron-left me-1" aria-hidden="true"></i> {{ __('messages.btns.back') }}
                </button>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <h5 class="mb-3 mb-md-0">{{ __('messages.general_detail') }}</h5>
                                </div>
                            </div>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date">{{ __('messages.date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="date" id="date" class="form-control" readonly value="{{ $loan->date }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="price_type">{{ __('messages.price_type') }} <span class="text-danger">*</span></label>
                                <input type="text" name="price_type" id="price_type" class="form-control" readonly value="{{ $loan->price_type }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="payment_type">{{ __('messages.payment_type') }} <span class="text-danger">*</span></label>
                                <input type="text" name="payment_type" id="payment_type" class="form-control" readonly value="{{ $loan->payment_type }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="price_type">{{ __('messages.loan') }} <span class="text-danger">*</span></label>
                                <input type="text" name="price_type" id="price_type" class="form-control" readonly value="{{ $loan->grand_total }}">
                            </div>

                            <div class="col-md-12 mb-3">
                                <h5 class="my-4">{{ __('messages.seller_info') }}</h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="seller_name">{{ __('messages.profile') }} <span class="text-danger">*</span></label>
                                <img src="{{ $loan->profile_image }}" alt="Profile" class="" width="65px" height="65px">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="seller_name">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="seller_name" id="seller_name" class="form-control" readonly value="{{ $loan->seller_name  }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email">{{ __('messages.email') }} <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control" readonly value="{{ $loan->email }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="price_type">{{ __('messages.loan') }} <span class="text-danger">*</span></label>
                                <input type="text" name="price_type" id="price_type" class="form-control" readonly value="{{ $loan->grand_total }}">
                            </div>

                            <div class="col-md-12 mb-3">
                                <h5 class="my-4">{{ __('messages.shop_info') }}</h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shop_id">{{ __('messages.shop_id') }} <span class="text-danger">*</span></label>
                                <input type="text" name="shop_id" id="shop_id" class="form-control" readonly value="{{ $loan->shops_id  }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shop_name_id">{{ __('messages.shop_name_id') }} <span class="text-danger">*</span></label>
                                <input type="text" name="shop_name_id" id="shop_name_id" class="form-control" readonly value="{{ $loan->shop_name }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="location">{{ __('messages.location') }} <span class="text-danger">*</span></label>
                                <input type="text" name="location" id="location" class="form-control" readonly value="{{ $loan->location }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address">{{ __('messages.address') }} <span class="text-danger">*</span></label>
                                <input type="text" name="address" id="address" class="form-control" readonly value="{{ $loan->address }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <h5 class="mb-3 mb-md-0">Products</h5>
                                </div>
                            </div>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-stripped w-100">
                                <thead>
                                    <tr>
                                        <td>{{ __('messages.table_elements.product') }}</td>
                                        <td>{{ __('messages.table_elements.image') }}</td>
                                        <td>{{ __('messages.table_elements.quantity') }}</td>
                                        <td>{{ __('messages.table_elements.sub_total') }}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($loanDetails as $detail)
                                        <tr>
                                            <td>{{ $detail->product->name }}</td>
                                            <td><img src="{{ $detail->product->image }}" alt="Product Image" width="50px" height="50px"></td>
                                            <td>{{ $detail->quantity }}</td>
                                            <td>{{ $detail->price }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <h5 class="mb-3 mb-md-0">{{ __('messages.partial_payment_history') }}</h5>
                                </div>
                            </div>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-stripped w-100">
                                <thead>
                                    <tr>
                                        <td>#</td>
                                        <td>{{ __('messages.table_elements.amount') }}</td>
                                        <td>{{ __('messages.table_elements.created_at') }}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($partialPayments as $key => $payment)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] . $payment->amount }}</td>
                                            <td>{{ $payment->created_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection