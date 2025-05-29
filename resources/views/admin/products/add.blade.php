@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <form action="{{ route('products.store') }}" id="" enctype="multipart/form-data" method="post">
                @csrf
                <div class="mb-3">
                    <h3 class="mb-0">{{__('messages.add_product_title')}}</h3>
                </div>
                <div class="col-md-12 offset-md-0 grid-margin stretch-card mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <h5 class="mb-3 mb-md-0">{{__('messages.product_information')}}</h5>
                                    </div>
                                </div>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="image">{{__('messages.image')}} <span class="text-danger">*</span></label>
                                    <input type="file" name="image" id="image" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name">{{__('messages.product_name')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>
                                {{-- <div class="col-md-6 mb-3">
                                    <label for="pieces">Pieces <span class="text-danger">*</span></label>
                                    <input type="number" name="pieces" id="pieces" onblur="CalculateStock()" class="form-control" required>
                                </div> --}}
                                <div class="col-md-6 mb-3">
                                    <label for="category_id">{{__('messages.category')}} <span class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id" class="form-control form-select select2" required>
                                        <option value="">{{__('messages.select')}}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="pieces_in_box">Pieces in One Box <span class="text-danger">*</span></label>
                                    <input type="number" name="pieces_in_box" id="pieces_in_box" class="form-control" required>
                                </div>
                                {{-- <div class="col-md-6 mb-3">
                                    <label for="stock">Number of Stock <span class="text-danger">*</span></label>
                                    <input type="number" name="stock" id="stock" class="form-control" required>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 offset-md-0 grid-margin stretch-card mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="retail_price">{{__('messages.retail_price')}} <span class="text-danger">*</span></label>
                                    <input type="number" name="retail_price" id="retail_price" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="wholesale_price">{{__('messages.wholesale_price')}} <span class="text-danger">*</span></label>
                                    <input type="number" name="wholesale_price" id="wholesale_price" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="extra_price">{{__('messages.extra_price')}} <span class="text-danger">*</span></label>
                                    <input type="number" name="extra_price" id="extra_price" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="city_id">{{__('messages.city')}} <span class="text-danger">*</span></label>
                                    <select name="city_id" id="city_id" class="form-control form-select select2" required>
                                        <option value="">{{__('messages.select')}}</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="description">{{__('messages.description')}}:</label>
                                    <textarea name="description" id="description" class="form-control" cols="20" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary submitBtn me-3">
                        <i class="fa-solid fa-floppy-disk"></i> {{__('messages.btns.create_product')}}
                    </button>
                    <button type="button" class="btn btn-secondary ms-1"
                            onclick="window.location.href='{{route('products')}}'">
                        {{__('messages.btns.close')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection