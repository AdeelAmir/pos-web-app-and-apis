@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid" id="carsPage">
        <div class="row">
            <div class="col-md-12">
                @if(session()->has('success-message'))
                    <div class="alert alert-success">
                        {{ session('success-message') }}
                    </div>
                @elseif(session()->has('error-message'))
                    <div class="alert alert-danger">
                        {{ session('error-message') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <h5 class="mb-3 mb-md-0">Users > <span class="text-secondary">Vendors List</span></h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="datatable1 table table-stripped w-100" id="VendorsTable">
                                <thead>
                                <tr>
                                    <td>Sr. No.</td>
                                    <td>First Name</td>
                                    <td>Last Name</td>
                                    <td>Email</td>
                                    <td>Phone</td>
                                    <td>Level</td>
                                    <td>Status</td>
                                    <td>Approve Status</td>
                                    <td>Action</td>
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
    @include('admin.vendors.status')
    @include('admin.vendors.approve-status')
    @include('admin.vendors.level')
@endsection
{{-- @push('module-scripts')
    @include('admin.vendors.js')
@endpush --}}
