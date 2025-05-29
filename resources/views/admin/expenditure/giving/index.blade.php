@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3 d-flex justify-content-between">
                <h3 class="mb-0">{{ __('messages.expenditure_giving_title') }}</h3>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex align-items-center">
                                <input type="text" name="search" id="search" class="form-control form-control-sm search-bar me-2" placeholder="{{ __('messages.search') }}" onkeyup="MakeExpenditureGivingTable()">
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
                                <button type="button" class="btn btn-primary mb-2 mb-md-0" data-toggle="tooltip" 
                                        title="{{ __('messages.btns.add_expenditure_giving') }}" onclick="window.location.href='{{route('expenditure.giving.add')}}';">
                                    <i class="fa fa-plus me-1"></i> {{ __('messages.btns.expenditure_giving') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="datatable1 table table-stripped w-100" id="expenditureGivingTable">
                                    <thead>
                                        <tr>
                                            <td>#</td>
                                            <td>{{ __('messages.table_elements.expenditure_name') }}</td>
                                            <td>{{ __('messages.table_elements.action') }}</td>
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
    @include('admin.expenditure.giving.delete')
@endsection