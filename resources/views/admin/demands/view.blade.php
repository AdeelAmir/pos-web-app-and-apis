@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3 d-flex justify-content-between">
                <h3 class="mb-0">View Demands of {{ $demand->seller_name }}</h3>
                <div class="">
                    <button type="submit" class="btn btn-primary me-3" onclick="openDemandPrintModal('demands-card')">
                        <i class="fa fa-print me-1"></i> Print
                    </button>
                    <button type="button" class="btn btn-secondary px-4 py-2 ms-1" onclick="window.location.href='{{route('demands')}}'">
                        <i class="fa fa-chevron-left me-1"></i> Back
                    </button>
                </div>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                <div class="card mb-3">
                    {{-- <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <input type="text" name="search" id="search" class="form-control form-control-sm search-bar" placeholder="{{__('messages.search')}}" onkeyup="">
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
                            </div>
                        </div>
                    </div> --}}
                    <div class="card-body" id="demands-card">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="table-responsive">
                                    <table class="table table-stripped w-100">
                                        <thead class="" style="background-color: #f8f9fa !important">
                                            <tr>
                                                <td>#</td>
                                                <td>Product</td>
                                                <td>Quantity</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($demandDetails as $key => $value)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $value->name }}</td>
                                                    <td>{{ $value->quantity }}</td>
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
        </div>
    </div>
    @include('admin.sales.print')
@endsection