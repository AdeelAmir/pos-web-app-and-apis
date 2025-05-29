@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">Orders</h3>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                <form action="{{ route('orders.store') }}" id="" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date">Date <span class="text-danger">*</span></label>
                                    <input type="date" name="date" id="date" class="form-control" required value="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name">Seller Name/ID <span class="text-danger">*</span></label>
                                    <select name="seller_id" id="seller_id" class="form-control form-select select2" required>
                                        <option value="">Select</option>
                                        @foreach ($sellers as $seller)
                                            <option value="{{ $seller->id }}">{{ $seller->name . ' / ' . $seller->id }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="shop_id">Shop Name/ID <span class="text-danger">*</span></label>
                                    <select name="shop_id" id="shop_id" class="form-control form-select select2" required>
                                        <option value="">Shop Name</option>
                                        @foreach ($shops as $shop)
                                        <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="payment_type">Payment Type <span class="text-danger">*</span></label>
                                    <select name="payment_type" id="payment_type" class="form-control form-select select2" required>
                                        <option value="">Shop Name</option>
                                        <option value="cash">Cash</option>
                                        <option value="credit">Credit</option>
                                    </select>
                                </div>
                                {{-- <div class="col-md-12 mb-3">
                                    <label for="product">Product <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <i class="bx bx-search fs-4 search-icon"></i>
                                        <input type="text" name="product" id="product" class="form-control ps-5" placeholder="Search product by product name">
                                    </div>
                                </div> --}}
                                <div class="col-md-12 mb-3">
                                    <span for="product">Items <span class="text-danger">*</span></span>
                                    <div class="table-responsive">
                                        <table class="table table-stripped w-100">
                                            <thead class="" style="background-color: #f8f9fa !important">
                                                <tr>
                                                    <td>Product</td>
                                                    <td>Price</td>
                                                    <td>Stock</td>
                                                    <td>Quantity</td>
                                                    <td>Sub Total</td>
                                                    <td>Return</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Black Tea</td>
                                                    <td>$20</td>
                                                    <td class="text-primary">25</td>
                                                    <td>
                                                        2
                                                    </td>
                                                    <td>$40</td>
                                                    <td>
                                                        <input type="number" class="form-control input-number" id="return_quantity" name="return_quantity" value="1">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Black Tea</td>
                                                    <td>$20</td>
                                                    <td class="text-primary">25</td>
                                                    <td>
                                                        2
                                                    </td>
                                                    <td>$40</td>
                                                    <td>
                                                        <input type="number" class="form-control input-number" id="return_quantity" name="return_quantity" value="1">
                                                    </td>
                                                </tr>
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
                                                $<span id="grandTotal">80</span>
                                                <input type="hidden" id="grand_total" name="grand_total" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-3 px-5">
                            <i class="fa-solid fa-floppy-disk"></i> Save
                        </button>
                        <button type="button" class="btn btn-secondary px-5"
                                onclick="window.location.href='{{route('orders')}}'">
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection