<footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl d-flex flex-wrap justify-content-center py-2 flex-md-row flex-column">
        <p class="text-muted text-center">
            Copyright © {{date("Y")}} <a href="{{url('/login')}}" target="_blank">{{\App\Helpers\SiteHelper::settings()['PageTitle']}}</a>
            <br>All Rights Reserved.
        </p>
    </div>
</footer>