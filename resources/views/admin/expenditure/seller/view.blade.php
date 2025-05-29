@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3 d-flex justify-content-between">
                <h3 class="mb-0">View Sale</h3>
                <div class="">
                    <button type="submit" class="btn btn-primary me-3" onclick="openSalePrintModal({{ $sale->id }})">
                        <i class="fa fa-print me-1"></i> Print
                    </button>
                    <button type="button" class="btn btn-secondary px-4 py-2 ms-1" onclick="window.location.href='{{route('sales')}}'">
                        <i class="fa fa-chevron-left me-1"></i> Back
                    </button>
                </div>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" id="date" class="form-control" readonly value="{{ $sale->date }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="name">Seller Name/ID <span class="text-danger">*</span></label>
                                <input type="text" name="seller_id" id="seller_id" class="form-control" readonly value="{{ $seller->name . ' / ' . $seller->id }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="product">Product <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <i class="bx bx-search fs-4 search-icon"></i>
                                    <input type="text" name="searchTerm" id="product" class="form-control ps-5" placeholder="Search product by product name" onkeyup="">
                                    <div id="search-box" class="shadow-sm rounded-bottom z-2 d-none">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <span for="product">Items <span class="text-danger">*</span></span>
                                <div class="table-responsive">
                                    <table class="table table-stripped w-100">
                                        <thead class="" style="background-color: #f8f9fa !important">
                                            <tr>
                                                <td>Product</td>
                                                <td>Price</td>
                                                <td>Quantity</td>
                                                <td>Sub Total</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($saleDetails as $value)
                                                <tr>
                                                    <td>{{ $value->name }}</td>
                                                    <td>{{ $value->retail_price }}</td>
                                                    <td>{{ $value->quantity }}</td>
                                                    <td>{{ $value->sub_total }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row d-flex justify-content-end px-3">
                                    <div class="col-md-3 d-flex justify-content-between border border-secondary text-primary px-3 py-2">
                                        <div>
                                            <span>Grand Total:</span>
                                        </div>
                                        <div>
                                            $<span id="grandTotal">{{ $sale->grand_total }}</span>
                                            <input type="hidden" id="grand_total" name="grand_total" value="{{ $sale->grand_total }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.sales.print')
@endsection