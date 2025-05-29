@extends('admin.layouts.app')
@section('content')
    <div class="page-content">
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
            <div class="col-md-6 offset-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <h5 class="mb-3 mb-md-0">Settings > <span class="text-secondary">List</span></h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{route('settings.update')}}" enctype="multipart/form-data" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mt-2 mb-3">
                                    <label for="tax" class="font-weight-bold">Tax (%) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="tax" id="tax" value="{{$setting->tax}}" required step="any" min="0">
                                </div>
                                <div class="col-md-12 mt-4 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary submitBtn" name="submit">
                                        <i class="fa-solid fa-floppy-disk"></i> Submit
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
