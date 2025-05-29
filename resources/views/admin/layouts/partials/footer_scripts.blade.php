<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{asset('public/assets/vendor/libs/jquery/jquery.js')}}"></script>
<script src="{{asset('public/assets/vendor/js/bootstrap.js')}}"></script>
<script src="{{asset('public/assets/vendor/libs/popper/popper.js')}}"></script>
<script src="{{asset('public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>

<script src="{{asset('public/assets/vendor/js/menu.js')}}"></script>
<!-- endbuild -->

<!-- Apex Charts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<!-- SweetAlert JS -->
<script src="{{ asset('public/assets/vendor/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>


{{-- <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script> --}}

<!-- Vendors JS -->

<!-- Main JS -->
<script src="{{asset('public/assets/js/main.js')}}"></script>

<!-- Page JS -->
<script src="{{asset('public/assets/vendor/js/select2/select2.min.js')}}"></script>

<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
{{-- Form Repeater --}}
<script src="{{ asset('public/assets/vendor/js/repeater/jquery.repeater.min.js') }}"></script>
<script src="{{ asset('public/assets/vendor/js/repeater/jquery.form-repeater.js') }}"></script>

{{-- Spartan Image Selector --}}
{{-- <script src="{{ asset('public/assets/vendor/js/spartan-multi-image-picker/spartan-multi-image-picker-min.js') }}"></script> --}}
{{-- <script src="{{ asset('public/assets/vendor/js/spartan-multi-image-picker/spartan-multi-image-picker.js') }}"></script> --}}

<!-- DateRangePicker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js" defer></script>

<!-- Datatables -->
<script src="{{ asset('public/assets/vendor/libs/datatables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('public/assets/vendor/libs/datatables/js/datatables.min.js')}}"></script>

<!-- Select2 -->
{{-- <script src="{{asset('public/assets/vendor/libs/select2/js/select2.min.js')}}"></script> --}}
<script src="{{ asset('public/assets/vendor/libs/select2-bt-5/js/select2.full.min.js') }}"></script>

<!-- Toastr -->
<script src="{{ asset('public/assets/vendor/libs/toastr/toastr.min.js') }}"></script>

<!-- Google Map APIs -->
<script type="text/javascript"
    src="https://maps.googleapis.com/maps/api/js?key={{base64_decode(\App\Helpers\SiteHelper::settings()['GoogleAPIKey'])}}&libraries=places">
</script>