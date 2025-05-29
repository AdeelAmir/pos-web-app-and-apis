@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">Stocks of {{$product->name}}</h3>
                <input type="hidden" id="product_id" name="product_id" value="{{ $product->id }}">
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                {{-- <h5 class="mb-3 mb-md-0">Products > <span class="text-secondary">Products List</span></h5> --}}
                                <input type="text" name="search" id="search" class="form-control form-control-sm search-bar" placeholder="{{__('messages.search')}}" onkeyup="MakeStockTable()">
                            </div>
                            <div class="d-flex align-items-center text-nowrap mr-1">
                                <div class="me-3">
                                    <select name="no_of_entries" id="no_of_entries" class="form-control form-select-sm select2 me-3">
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="200">200</option>
                                        <option value="400">400</option>
                                    </select>
                                </div>
                                {{-- <button type="button" class="btn btn-primary mb-2 mb-md-0"
                                        data-toggle="tooltip" title="Add Product"
                                        onclick="window.location.href='{{route('products.add')}}';">
                                    <i class="fa fa-plus mr-1"></i> Add Product
                                </button> --}}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="datatable1 table table-stripped w-100" id="stockTable">
                                <thead>
                                    <tr>
                                        <td>#</td>
                                        <td>{{__('messages.table_elements.pieces')}}</td>
                                        <td>{{__('messages.table_elements.boxes')}}</td>
                                        <td>{{__('messages.table_elements.stock')}}</td>
                                        <td>{{__('messages.table_elements.created_at')}}</td>
                                        <td>{{__('messages.table_elements.action')}}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection