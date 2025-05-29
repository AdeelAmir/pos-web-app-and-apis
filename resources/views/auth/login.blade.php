@extends('auth.layouts.app')
@section('content')
<!-- Login -->
<div class="col-12">
  @if(session()->has('error'))
  {{session('error')}}
  @elseif(session()->has('success'))
  {{session('error')}}
  @endif
</div>
<div class="card shadow-none">
  <div class="card-body login-card-body">
    <!-- Logo -->
    <div class="app-brand justify-content-center">
      <div class="app-brand demo mb-0">
        <span class="app-brand-link mt-3">
          <img src="{{asset("public/assets/img/logo/logo.png")}}" alt="logo" style="width: 200px; height: auto;">
        </span>
      </div>
    </div>
    <!-- /Logo -->
    <h4 class="mb-4 text-center text-dark">Login to your account</h4>
    <div class="row">
      <div class="col-md-12">
        @if(session()->has('message'))
        <div class="alert alert-success">
          {{ session('message') }}
        </div>
        @elseif(session()->has('error'))
        <div class="alert alert-danger">
          {{ session('error') }}
        </div>
        @endif
      </div>
    </div>
    <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="text" class="form-control login-input" id="email" name="email" value="{{old('email')}}" required autofocus />
      </div>
      <div class="mb-3 form-password-toggle">
        <div class="d-flex justify-content-between">
          <label class="form-label" for="password">Password</label>
        </div>
        <div class="input-group input-group-merge">
          <input type="password" id="password" class="form-control login-input" name="password"
            {{-- placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" --}}
            aria-describedby="password" required />
          {{-- <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span> --}}
        </div>
      </div>
      <div class="mb-3">
        <div class="form-check">
          <label class="form-check-label" for="remember-me">
            <input class="form-check-input me-3" type="checkbox" id="remember-me" name="remember" /><span class="fs-7">Remember Me</span>
          </label>
        </div>
      </div>
      <div class="mb-3">
        <button class="btn btn-primary login-btn d-grid w-100" type="submit">Log-in</button>
      </div>
      <div class="mb-3 text-center text-secondary">
        <a href="{{ route('password.request') }}" class="text-secondary">
          <small>Forgot Password?</small>
        </a>
      </div>
    </form>
  </div>
</div>
@endsection
