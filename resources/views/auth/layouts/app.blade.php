<!DOCTYPE html>
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
>
@include('auth.includes.head')
<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
          <div class="authentication-inner">
            @yield('content')
          </div>
        </div>
    </div>
</body>
<!-- Helpers -->
<script src="{{asset('public/assets/vendor/js/helpers.js')}}"></script>

<!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
<script src="{{asset('public/assets/js/config.js')}}"></script>

<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{asset('public/assets/vendor/libs/jquery/jquery.js')}}"></script>
<script src="{{asset('public/assets/vendor/libs/popper/popper.js')}}"></script>
<script src="{{asset('public/assets/vendor/js/bootstrap.js')}}"></script>
<script src="{{asset('public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
<script src="{{asset('public/assets/vendor/js/menu.js')}}"></script>
<!-- endbuild -->
<!-- Vendors JS -->

<!-- Main JS -->
<script src="{{asset('public/assets/js/main.js')}}"></script>
<!-- Page JS -->

<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
</html>
