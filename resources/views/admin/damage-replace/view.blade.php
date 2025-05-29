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
                <h3 class="mb-0">Seller View</h3>
                <button type="button" class="btn btn-secondary px-4 py-2 ms-1" onclick="window.location.href='{{route('sellers')}}'">
                    <i class="fa fa-chevron-left me-1" aria-hidden="true"></i> Back
                </button>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <h5 class="mb-3 mb-md-0">General Detail</h5>
                                </div>
                            </div>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-3">
                                <img src="{{ $seller->profile_image }}" alt="Profile Image" class="rounded_circle object-fit-cover">
                            </div>
                            <div class="col-12 col-md-9">
                                <div class="mb-1">Seller ID: {{ $seller->id }}</div>
                                <div class="mb-1">Seller Name: {{ $seller->name }}</div>
                                <div>Phone Number: {{ $seller->phone }}</div>
                                <div class="mb-1">Email: {{ $seller->email }}</div>
                                <div class="mb-1">Status: @if($seller->status == 1) <span class="btn-sm btn-success cursor-pointer">Active</span> @else <span class="btn-sm btn-danger cursor-pointer">Ban</span> @endif</div>
                                <div>Description:</div>
                                <p>{{ $seller->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <style>
                    .bg-white-transparent{
                        background: rgba(255, 255, 255, 0.384);
                        border-radius: 1rem;
                        padding: 1rem 1rem;
                    }
                    
                    .rounded-custom{
                        border-radius: 1rem !important;
                    }
                </style>
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-purple rounded-custom">
                                <div class="card-body text-white d-flex justify-content-between">
                                    <div>
                                        <i class="bg-white-transparent fs-3 fas fa-shopping-cart"></i>                            
                                    </div>
                                    <div class="d-flex flex-column justify-content-center align-items-end">
                                        <span class="fs-4 fw-bold m-0">$ 209</span>
                                        <small class="text-end">Total Sale</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success rounded-custom">
                                <div class="card-body text-white d-flex justify-content-between">
                                    <div>
                                        <i class="bg-white-transparent fs-3 fas fa-wallet"></i>                            
                                    </div>
                                    <div class="d-flex flex-column justify-content-center align-items-end">
                                        <span class="fs-4 fw-bold m-0">$ 209</span>
                                        <small class="text-end">Total Expenditure</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary rounded-custom">
                                <div class="card-body text-white d-flex justify-content-between">
                                    <div>
                                        <i class="bg-white-transparent fs-3 fas fa-box"></i>                            
                                    </div>
                                    <div class="d-flex flex-column justify-content-center align-items-end">
                                        <span class="fs-4 fw-bold m-0">$ 209</span>
                                        <small class="text-end">Total Goods</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning rounded-custom">
                                <div class="card-body text-white d-flex justify-content-between">
                                    <div>
                                        <i class="bg-white-transparent fs-3 fas fa-dollar-sign"></i>                            
                                    </div>
                                    <div class="d-flex flex-column justify-content-center align-items-end">
                                        <span class="fs-4 fw-bold m-0">$ 209</span>
                                        <small class="text-end">Total Sale</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="datatable1 table table-stripped w-100" id="">
                                <thead>
                                    <tr>
                                        <td>#</td>
                                        <td>Shop ID</td>
                                        <td>Shop Name</td>
                                        <td>Loan</td>
                                        <td>Location</td>
                                        <td>Status</td>
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
@endsection