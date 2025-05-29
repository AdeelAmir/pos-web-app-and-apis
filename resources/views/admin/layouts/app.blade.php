<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('admin.layouts.partials.head')
</head>
<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      @include('admin.layouts.partials.sidebar')
      <div class="layout-page">
        @include('admin.layouts.partials.navbar')
        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y">
            @yield('content')
          </div>
          @include('admin.layouts.partials.footer')
        </div>
      </div>
    </div>
  </div>
</body>
@include('admin.layouts.partials.footer_scripts')
@include('admin.includes.scripts')
</html>