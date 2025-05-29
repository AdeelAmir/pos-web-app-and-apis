@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3 d-flex justify-content-between">
                <h3 class="mb-0">{{ __('messages.office_loan') }}</h3>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex align-items-center">
                                {{-- <h5 class="mb-3 mb-md-0">Users > <span class="text-secondary">Users List</span></h5> --}}
                                <input type="text" name="search" id="search" class="form-control form-control-sm search-bar me-2" placeholder="Search" onkeyup="MakeOfficeLoanTable()">
                                <button class="btn btn-sm btn-filter-light"><i class="text-dark font-bold fs-6 fa fa-filter" aria-hidden="true"></i></button>
                            </div>
                            <div class="d-flex align-items-center text-nowrap">
                                <div class="me-3">
                                    <select name="no_of_entries" id="no_of_entries" class="form-control form-select-sm select2 me-2">
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="200">200</option>
                                        <option value="400">400</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="datatable1 table table-stripped w-100" id="officeLoanTable">
                                    <thead>
                                        <tr>
                                            <td>{{__('messages.table_elements.date')}}</td>
                                            <td>{{__('messages.table_elements.seller_name_id')}}</td>
                                            <td>{{__('messages.table_elements.total_items')}}</td>
                                            <td>{{__('messages.table_elements.grand_total')}}</td>
                                            <td>{{__('messages.table_elements.payment_status')}}</td>
                                            <td>{{__('messages.table_elements.cash')}}</td>
                                            <td>{{__('messages.table_elements.loan')}}</td>
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
    </div>

    @include('admin.office-loan.status')
    @include('admin.office-loan.add_partial_payment')
@endsection