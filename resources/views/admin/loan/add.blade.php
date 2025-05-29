@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">Exchange city</h3>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                <form action="{{ route('exchange_city.store') }}" id="" enctype="multipart/form-data" method="post">
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
                                    <label for="from_city_id">Form City <span class="text-danger">*</span></label>
                                    <select name="from_city_id" id="from_city_id" class="form-control form-select select2" required>
                                        <option value="">City Name</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="to_city_id">To City <span class="text-danger">*</span></label>
                                    <select name="to_city_id" id="to_city_id" class="form-control form-select select2" required>
                                        <option value="">City Name</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="product_id">Product Name/ID <span class="text-danger">*</span></label>
                                    <select name="product_id" id="product_id" class="form-control form-select select2" required>
                                        <option value="">Product Name</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="boxes">Boxes <span class="text-danger">*</span></label>
                                    <input type="number" name="boxes" id="boxes" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="pieces">Pieces <span class="text-danger">*</span></label>
                                    <input type="number" name="pieces" id="pieces" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-3">
                            <i class="fa-solid fa-floppy-disk"></i> Save
                        </button>
                        <button type="button" class="btn btn-secondary"
                                onclick="window.location.href='{{route('exchange_city')}}'">
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection