@extends('admin.layouts.app')
@section('content')
    <div class="content container-fluid">
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
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">
                                Details
                            </h4>
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='{{route('vendors')}}'" data-toggle="tooltip" title="Back">
                                Back
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" id="" enctype="multipart/form-data"method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mt-2">
                                    <label for="profile_image" class="font-weight-bold">Profile Image</label>
                                    <div>
                                        <img class="iamge-fluid" height="200px" width="auto" src="{{$vendor->profile_image}}">
                                    </div>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="first_name" class="font-weight-bold">First Name</label>
                                    <input type="text" class="form-control" name="first_name" id="first_name" value="{{$vendor->first_name}}" readonly>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="last_name" class="font-weight-bold">Last Name</label>
                                    <input type="text" class="form-control" name="last_name" id="last_name" value="{{$vendor->last_name}}" readonly>
                                </div>
                                <div class="col-12 col-md-6 mt-2">
                                    <label for="email" class="font-weight-bold">Email</label>
                                    <input type="text" class="form-control" name="email" id="email" value="{{$vendor->email}}" readonly>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="phone" class="font-weight-bold">Phone</label>
                                    <input type="text" class="form-control" name="phone" id="phone" value="{{$vendor->phone}}" readonly>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="gender" class="font-weight-bold">Gender</label>
                                    <input type="text" class="form-control" name="gender" id="gender" value="{{$vendor->gender}}" readonly>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="parish" class="font-weight-bold">Parish</label>
                                    <input type="text" class="form-control" name="parish" id="parish" value="{{$vendor->parish}}" readonly>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="category_name" class="font-weight-bold">Category Name</label>
                                    <input type="text" class="form-control" name="category_name" id="category_name" value="{{$vendor->categories_name}}" readonly>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="sub_category_name" class="font-weight-bold">Sub Category Name</label>
                                    <input type="text" class="form-control" name="sub_category_name" id="sub_category_name" value="{{$vendor->sub_categories_name}}" readonly>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="address" class="font-weight-bold">Address</label>
                                    <input type="text" class="form-control" name="address" id="address" value="{{$vendor->address}}" readonly>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="city" class="font-weight-bold">City</label>
                                    <input type="text" class="form-control" name="city" id="city" value="{{$vendor->city}}" readonly>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="district" class="font-weight-bold">District</label>
                                    <input type="text" class="form-control" name="district" id="district" value="{{$vendor->district}}" readonly>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="level" class="font-weight-bold">Level</label>
                                    <input type="text" class="form-control" name="level" id="level" value="{{$vendor->level}}" readonly>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="experience" class="font-weight-bold">Years of Experience</label>
                                    <input type="text" class="form-control" name="experience" id="experience" value="{{$vendor->experience}}" readonly>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="months_of_experience" class="font-weight-bold">Months of Experience</label>
                                    <input type="text" class="form-control" name="months_of_experience" id="months_of_experience" value="{{$vendor->months_of_experience}}" readonly>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="service_description" class="font-weight-bold">Service Description</label>
                                    <textarea class="form-control" name="service_description" id="service_description" cols="30" rows="5" readonly>{{$vendor->service_description}}</textarea>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="about" class="font-weight-bold">About</label>
                                    <textarea class="form-control" name="about" id="about" cols="30" rows="5" readonly>{{$vendor->about}}</textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">Identification</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="identification_type" class="font-weight-bold">Identification Type</label>
                                <input type="text" class="form-control" name="identification_type" id="identification_type" value="@if($vendor->identification_type == 'national_id') National ID @elseif ($vendor->identification_type == 'passport') Passport @elseif ($vendor->identification_type == 'drivers_license') Drivers License @else Invalid @endif" readonly>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Title</th>
                                            <th>Image</th>
                                            <th>Download</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="width: 5%">
                                                1
                                            </td>
                                            <td style="width: 20%">
                                                National ID Front Image
                                            </td>
                                            <td style="width: 60%">
                                                <img class="image-fluid" height="150px" width="auto" src="{{asset('public/storage/user') . '/' . $vendor->national_id_front}}">
                                            </td>
                                            <td style="width: 15%">
                                                @if(isset($vendor->national_id_front))
                                                <a href="{{asset('public/storage/user') . '/' . $vendor->national_id_front}}" download>
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 5%">
                                                2
                                            </td>
                                            <td style="width: 20%">
                                                National ID Back Image
                                            </td>
                                            <td style="width: 60%">
                                                <img class="image-fluid" height="150px" width="auto" src="{{asset('public/storage/user') . '/' . $vendor->national_id_back}}">
                                            </td>
                                            <td style="width: 15%">
                                                @if(isset($vendor->national_id_back))
                                                <a href="{{asset('public/storage/user') . '/' . $vendor->national_id_back}}" download>
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 5%">
                                                3
                                            </td>
                                            <td style="width: 20%">
                                                Driving licence
                                            </td>
                                            <td style="width: 60%">
                                                <img class="image-fluid" height="150px" width="auto" src="{{asset('public/storage/user') . '/' . $vendor->drivers_license}}">
                                            </td>
                                            <td style="width: 15%">
                                                @if(isset($vendor->drivers_license))
                                                <a href="{{asset('public/storage/user') . '/' . $vendor->drivers_license}}" download>
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 5%">
                                                3
                                            </td>
                                            <td style="width: 20%">
                                                Passport
                                            </td>
                                            <td style="width: 60%">
                                                <img class="image-fluid" height="150px" width="auto" src="{{asset('public/storage/user') . '/' . $vendor->passport}}">
                                            </td>
                                            <td style="width: 15%">
                                                @if(isset($vendor->passport))
                                                <a href="{{asset('public/storage/user') . '/' . $vendor->passport}}" download>
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">Qualification</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12 mb-3">
                            @if($qualifictions != null)
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.</th>
                                        <th>Image</th>
                                        <th>Download</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $count = 1;
                                    @endphp
                                    @foreach ($qualifictions as $index => $qualifiction)
                                    <tr>
                                        <td style="width: 5%">
                                            {{ $count }}
                                        </td>
                                        <td style="width: 80%">
                                            <img class="image-fluid" height="150px" width="auto" src="{{asset('public/storage/user') . '/' . $qualifiction}}">
                                        </td>
                                        <td style="width: 15%">
                                            <a href="{{asset('public/storage/user') . '/' . $qualifiction}}" download>
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @php
                                        $count++;
                                    @endphp
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection