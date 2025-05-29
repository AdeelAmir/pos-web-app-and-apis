@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="col-md-6 offset-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <h5 class="mb-3 mb-md-0">Categories > <span class="text-secondary">Create</span></h5>
                                </div>
                            </div>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('categories.store') }}" id="" enctype="multipart/form-data" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="name">{{__('messages.name')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="icon">{{__('messages.icon')}} <span class="text-danger">*</span></label>
                                    <input type="file" name="icon" id="icon" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12 mt-4 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary submitBtn  me-3">
                                    {{__('messages.btns.create_category')}}
                                </button>
                                <button type="button" class="btn btn-secondary "
                                        onclick="window.location.href='{{route('categories')}}'">
                                    {{__('messages.btns.close')}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection