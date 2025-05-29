@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid" id="demand-page">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">{{__('messages.reports')}}</h3>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="datefilter" id="datefilter" class="form-control" @if(isset($dateFilter)) value="{{$dateFilter}}" @endif placeholder="Year-to-date" />
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-primary mb-2 mb-md-0" data-toggle="tooltip" title="Filter" onclick="MakeDemandTable()">
                                <i class="fa fa-filter mr-1"></i> {{ __('messages.btns.filter') }}
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="start_date" id="start_date" @isset($startDate) value="{{$startDate}}" @endisset>
                    <input type="hidden" name="end_date" id="end_date" @isset($endDate) value="{{$endDate}}" @endisset>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <input type="text" name="search" id="search" class="form-control form-control-sm search-bar" placeholder="{{__('messages.search')}}" onkeyup="MakeDemandTable()">
                            </div>
                            <div class="d-flex align-items-center text-nowrap mr-1">
                                <div class="me-3">
                                    <select name="no_of_entries" id="no_of_entries" class="form-control form-select-sm select2 me-3" onchange="MakeDemandTable()">
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="200">200</option>
                                        <option value="400">400</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="datatable1 table table-stripped w-100" id="demandsTable">
                                <thead>
                                <tr>
                                    <td>#</td>
                                    <td>{{ __('messages.table_elements.seller_name') }}</td>
                                    <td>{{ __('messages.table_elements.date') }}</td>
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
@endsection
