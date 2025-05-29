@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3">
                <h3 class="mb-0">{{__('messages.damage_replace')}}</h3>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                {{-- <h5 class="mb-3 mb-md-0">Users > <span class="text-secondary">Users List</span></h5> --}}
                                <input type="text" name="search" id="search" class="form-control form-control-sm search-bar" placeholder="Search" onkeyup="MakeDamageTable()">
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
                                <button type="button" class="btn btn-primary mb-2 mb-md-0" data-toggle="tooltip" title="{{__('messages.btns.add_damage_replace')}}" onclick="window.location.href='{{route('damage_replace.add')}}';">
                                    <i class="fa fa-plus mx-2"></i> {{__('messages.damage_replace')}}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="datatable1 table table-stripped w-100" id="damageReplaceTable">
                                <thead>
                                    <tr>
                                        <td>{{__('messages.table_elements.date')}}</td>
                                        <td>{{__('messages.table_elements.seller_name_id')}}</td>
                                        <td>{{__('messages.table_elements.quantity')}}</td>
                                        <td>{{__('messages.table_elements.boxes')}}</td>
                                        <td>{{__('messages.table_elements.grand_total')}}</td>
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

    @include('admin.returns.status')
@endsection