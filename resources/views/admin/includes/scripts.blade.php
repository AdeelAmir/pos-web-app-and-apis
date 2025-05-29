<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).ready(function () {
        /* For Error Messages Without ajax */
        toastr.options = {
            "positionClass": "toast-top-right",
            "progressBar": true,
            "timeOut": 5000
        };

        @if(\Illuminate\Support\Facades\Session::has('success-message'))
            toastr.success("{{ session('success-message') }}");
        @endif

        @if(\Illuminate\Support\Facades\Session::has('error-message'))
            toastr.error("{{ session('error-message') }}");
        @endif

        $(function () {
            $("#datefilter").daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });
        });

        let select2 = $('.select2');
        if(select2.length > 0){
            select2.select2({
                theme: 'bootstrap-5'
            });
        }

        $('#withdrawStatusOption').change(function () {
            var selectedValue = $(this).val();
            var cancellationReasonDiv = $('#cancellation_reason_div');
            var cancellationReason = $('#cancellation_reason');

            if (selectedValue === 'rejected') {
                cancellationReasonDiv.show();
                cancellationReason.attr('required', true);
            } else if (selectedValue === 'completed') {
                cancellationReasonDiv.hide();
                cancellationReason.removeAttr('required');
            }
        });

        $('#vendorSelect').change(function () {
            let selectedValue = $(this).val();
            if (selectedValue !== '') {
                $('#type_div').show();
            } else {
                $('#type_div').hide();
                $('#amount_div').hide();
                $('#error-amount').text('').hide();
                $('#amountInWallet').text('').hide();
            }
        });

        if ($('#dashboard-page').length > 0) {
            $(function () {
                $("#datefilter").daterangepicker({
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear'
                    }
                });

                $("#datefilter").on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                    let startDate = picker.startDate.format('YYYY-MM-DD');
                    let endDate = picker.endDate.format('YYYY-MM-DD');
                    $("#start_date").val(startDate);
                    $("#end_date").val(endDate);
                    DashboardTopCards();
                });

                $("#datefilter").on('cancel.daterangepicker', function (ev, picker) {
                    $(this).val('');
                    $("#start_date").val('');
                    $("#end_date").val('');
                    DashboardTopCards();
                });
            });
            DashboardTopCards();
            DashboardRevenueCard();
            loadIncomeChart();
            loadExpenseChart();
            loadSellingProductsChart();
            loadProductsInStocksChart();
            loadDamageReplaceProductsChart();
            loadBonusChart();
            MakeStoresGetMostCredit();
            MakeTopSellerTable();
        }

        if ($('#report-page').length > 0) {
            $("#datefilter").on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                let startDate = picker.startDate.format('YYYY-MM-DD');
                let endDate = picker.endDate.format('YYYY-MM-DD');
                $("#start_date").val(startDate);
                $("#end_date").val(endDate);
            });

            $("#datefilter").on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
                $("#start_date").val('');
                $("#end_date").val('');
            });
        }

        if ($('#demand-page').length > 0) {
            $("#datefilter").on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                let startDate = picker.startDate.format('YYYY-MM-DD');
                let endDate = picker.endDate.format('YYYY-MM-DD');
                $("#start_date").val(startDate);
                $("#end_date").val(endDate);
            });

            $("#datefilter").on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
                $("#start_date").val('');
                $("#end_date").val('');
            });
        }

        /*Table*/
        MakeCitiesTable();
        MakeSellingCitiesTable();
        MakeCategoryTable();
        MakeProductTable();
        MakeStockTable();
        MakeUsersTable();
        MakeSellersTable();
        MakeSellerTargetTable();
        MakeOfficeSellersTable();
        MakeShopsTable();
        MakeSalesTable();
        MakeOfficeSalesTable();
        MakeReturnsTable();
        MakeOrdersTable();
        MakeDamageTable();
        MakeExchangeCityTable();
        MakeLoansTable();
        MakeOfficeLoanTable();
        MakeExpenditureGivingTable();
        MakeExpenditureOfficeTable();
        MakeExpenditureSellerTable();
        InitializeGooglePlaceAPI();
        MakeSellerReceiveMaximumCredit();
        MakeAllOrdersTable();
        MakeDemandTable();
    });

    // Google API - Start
    function InitializeGooglePlaceAPI() {
        const elements = document.getElementsByClassName('addressField');
        Array.from(elements).forEach((element, index) => {
            new google.maps.places.Autocomplete(element);
        });
    }

    function closeModal(id) {
        $("#" + id).modal('toggle');
    }

    function DashboardTopCards() {
        $.ajax({
            type: "POST",
            url: "{{ route('dashboard.top.cards') }}",
            data: {
                _token: "{{ csrf_token() }}",
                start_date: $("#start_date").val(),
                end_date: $("#end_date").val(),
            },
            success: function (response) {

                $('#total_revenue').text('{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}' + response.total_revenue);
                $('#total_products').text(response.total_stocks);
                $('#total_sellers').text(response.total_sellers);
                $('#total_shops').text(response.total_shops);
                $('#total_users').text(response.total_users);
            },
            error: function () {
                // error
            }
        });
    }

    function DashboardRevenueCard() {
        $.ajax({
            type: "POST",
            url: "{{ route('dashboard.revenue.card') }}",
            data: {
                _token: "{{ csrf_token() }}",
            },
            success: function (response) {
                $('#today_revenue').text('{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}' + response.total_revenue);
                $('#weekly_revenue').text('{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}' + response.weekly_revenue);
                $('#monthly_revenue').text('{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}' + response.monthly_revenue);
                $('#annual_revenue').text('{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}' + response.annual_revenue);
            },
            error: function () {
                // error
            }
        });
    }

    let chart1 = null;
    let chart2 = null;
    let chart3 = null;
    let chart4 = null;
    let chart5 = null;
    let chart6 = null;

    function loadIncomeChart(type) {
        let chart1_id = document.getElementById("chart1");
        chart1_id.innerHTML = "";

        if (chart1) {
            chart1.destroy();
            chart1 = null;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('dashboard.chart.income') }}",
            data: {
                _token: "{{ csrf_token() }}",
                type: type
            },
            success: function (response) {

                let day_wise_income = response.day_wise_income;
                let days = response.days;

                let chart_1_options = {
                    series: [{
                        name: '{{__('messages.apex_charts.chart1.series1')}}',
                        data: day_wise_income
                    }],
                    chart: {
                        height: 290,
                        type: 'area'
                    },
                    dataLabels: {
                        enabled: false
                    },
                    colors: ['#3f8cff'],
                    stroke: {
                        curve: 'smooth'
                    },
                    xaxis: {
                        categories: days
                    },
                };

                if (chart1_id) {
                    chart1 = new ApexCharts(chart1_id, chart_1_options);
                    chart1.render();
                }
            },
            error: function () {
                // error log
            }
        });
    }

    function loadExpenseChart(type) {
        let chart2_id = document.getElementById("chart2");
        chart2_id.innerHTML = "";

        if (chart2) {
            chart2.destroy();
            chart2 = null;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('dashboard.chart.expense') }}",
            data: {
                _token: "{{ csrf_token() }}",
                type: type
            },
            success: function (response) {
                let NameArray = [];
                let ExpensesArray = [];
                let TotalExpense = 0

                response.forEach(element => {
                    NameArray.push(element.name);
                    ExpensesArray.push(element.total_amount);
                    TotalExpense += parseInt(element.total_amount);
                });
                $('#total_expenditure').text('{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}' + TotalExpense);

                let chart_2_options = {
                    series: ExpensesArray,
                    chart: {
                        width: 300,
                        type: 'pie',
                    },
                    labels: NameArray,
                    // colors: ['#fda390', '#9ccdaf', '#f5de68', '#8bc2f2'],
                    dataLabels: {
                        enabled: false
                    },
                    legend: {
                        markers: {
                            shape: 'square',
                            width: 20,
                            height: 20
                        }
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 300
                            },
                            legend: {
                                markers: {
                                    shape: 'square',
                                    width: 20,
                                    height: 20
                                }
                            }
                        }
                    }]
                };
                if (chart2_id) {
                    let chart2 = new ApexCharts(chart2_id, chart_2_options);
                    chart2.render();
                }
            },
            error: function () {
                // Swal.fire(
                //     '{{ __("messages.alerts.error") }}',
                //     '{{ __("messages.alerts.delete_error") }}',
                //     'error'
                // );
            }
        });
    }

    function loadSellingProductsChart(type) {
        let chart3_id = document.getElementById("chart3");
        chart3_id.innerHTML = "";

        if (chart3) {
            chart3.destroy();
            chart3 = null;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('dashboard.chart.bestSellingProducts') }}",
            data: {
                _token: "{{ csrf_token() }}",
                type: type
            },
            success: function (response) {
                let NameArray = [];
                let ProductSold = [];
                let TotalStock = 0;

                response.forEach(element => {
                    NameArray.push(element.product_name);
                    ProductSold.push(parseInt(element.total_product_sold));
                });

                let chart_3_options = {
                    series: ProductSold,
                    labels: NameArray,
                    chart: {
                        width: 320,
                        // width: '100%',
                        type: 'donut',
                    },
                    plotOptions: {
                        pie: {
                            // startAngle: -90,
                            // endAngle: 270,
                            donut: {
                                labels: {
                                    show: true,
                                    total: {
                                        showAlways: true,
                                        show: true,
                                        label: '{{__('messages.apex_charts.chart3.total')}}',
                                        formatter: function (w) {
                                            // Calculate total percentage and format it
                                            const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                            return `${total}%`;
                                        }
                                    }
                                }
                            },
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    colors: ['#7081fd', '#9dc349', '#fcb5f3'],
                    fill: {
                        type: 'gradient',
                    },
                    legend: {
                        // formatter: function(val, opts) {
                        //     return val + " - " + opts.w.globals.series[opts.seriesIndex]
                        // }
                        // position: 'bottom',
                    },
                    responsive: [{
                        breakpoint: 1080, // 1080p screens
                        options: {
                            chart: {
                                width: 450
                            }
                        }
                    }, {
                        breakpoint: 720, // 720p screens
                        options: {
                            chart: {
                                height: 360
                            }
                        }
                    }, {
                        breakpoint: 480, // Mobile screens
                        options: {
                            chart: {
                                height: 360
                            }
                        }
                    }]
                };

                if (chart3_id) {
                    chart3 = new ApexCharts(chart3_id, chart_3_options);
                    chart3.render();
                }
            },
            error: function () {
                // Swal.fire(
                //     '{{ __("messages.alerts.error") }}',
                //     '{{ __("messages.alerts.delete_error") }}',
                //     'error'
                // );
            }
        });
    }

    function loadProductsInStocksChart(type) {
        let chart4_id = document.getElementById("chart4");
        chart4_id.innerHTML = "";

        if (chart4) {
            chart4.destroy();
            chart4 = null;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('dashboard.chart.productsInStocks') }}",
            data: {
                _token: "{{ csrf_token() }}",
                type: type
            },
            success: function (response) {
                let NameArray = [];
                let ProductsInStock = [];
                let TotalStock = response.total_products_stock;

                response.products.forEach(element => {
                    NameArray.push(element.product_name);
                    ProductsInStock.push(parseInt(element.total_stock));
                });

                let chart_4_options = {
                    series: ProductsInStock,
                    labels: NameArray,
                    chart: {
                        width: 345,
                        type: 'donut',
                    },
                    // colors: ['#3c50e0', '#6577f3', '#80caee', '#10adcf'],
                    dataLabels: {
                        enabled: false
                    },
                    legend: {
                        show: false,
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    show: true,
                                    total: {
                                        showAlways: true,
                                        show: true,
                                        label: '{{__('messages.apex_charts.chart4.total')}}',
                                        formatter: function (w) {
                                            // Calculate total percentage and format it
                                            const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                            return `${TotalStock}`;
                                        }
                                    },
                                }
                            },
                            expandOnClick: true
                        }
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 300
                            },
                            legend: {
                                show: false
                            }
                        }
                    }],
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val;
                            }
                        }
                    }
                };

                if (chart4_id) {
                    let chart4 = new ApexCharts(chart4_id, chart_4_options);
                    chart4.render();
                }
            },
            error: function () {
                // Swal.fire(
                //     '{{ __("messages.alerts.error") }}',
                //     '{{ __("messages.alerts.delete_error") }}',
                //     'error'
                // );
            }
        });
    }

    function loadDamageReplaceProductsChart(type) {
        let chart5_id = document.getElementById("chart5");
        chart5_id.innerHTML = "";

        if (chart5) {
            chart5.destroy();
            chart5 = null;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('dashboard.chart.replaceProducts') }}",
            data: {
                _token: "{{ csrf_token() }}",
                type: type
            },
            success: function (response) {
                let NameArray = [];
                let ProductsInStock = [];
                let TotalStock = response.total_damage_products;

                response.products.forEach(element => {
                    NameArray.push(element.product_name);
                    ProductsInStock.push(parseInt(element.total_quantity));
                });

                let chart_5_options = {
                    series: ProductsInStock,
                    labels: NameArray,
                    chart: {
                        width: 345,
                        type: 'donut',
                    },
                    // colors: ['#3c50e0', '#6577f3', '#80caee', '#10adcf'],
                    dataLabels: {
                        enabled: false
                    },
                    legend: {
                        show: false
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                // size: '65%',
                                labels: {
                                    show: true,
                                    total: {
                                        showAlways: true,
                                        show: true,
                                        label: '{{__('messages.apex_charts.chart5.total')}}',
                                        formatter: function (w) {
                                            // Calculate total percentage and format it
                                            const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                            return `${TotalStock}`;
                                        }
                                    },
                                }
                            },
                            expandOnClick: true
                        }
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 300
                            },
                            legend: {
                                // position: 'bottom',
                                show: false
                            }
                        }
                    }],
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val
                            }
                        }
                    }
                };

                if (chart5_id) {
                    let chart5 = new ApexCharts(chart5_id, chart_5_options);
                    chart5.render();
                }
            },
            error: function () {
                // Swal.fire(
                //     '{{ __("messages.alerts.error") }}',
                //     '{{ __("messages.alerts.delete_error") }}',
                //     'error'
                // );
            }
        });
    }

    function loadBonusChart() {
        let chart6_id = document.getElementById("chart6");
        chart6_id.innerHTML = "";

        if (chart6) {
            chart6.destroy();
            chart6 = null;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('dashboard.chart.bonus') }}",
            data: {
                _token: "{{ csrf_token() }}",
            },
            success: function (response) {
                let ProductNameArray = [];
                let ProductQuantityArray = [];
                let ProductPriceArray = [];

                response.forEach(element => {
                    ProductNameArray.push(element.product_name);
                    ProductQuantityArray.push(element.product_quantity);
                    ProductPriceArray.push(element.product_price);
                });

                let chart_6_options = {
                    series: ProductQuantityArray,
                    chart: {
                        width: 300,
                        type: 'pie',
                    },
                    labels: ProductNameArray,
                    // colors: ['#fda390', '#9ccdaf', '#f5de68', '#8bc2f2'],
                    dataLabels: {
                        enabled: false
                    },
                    legend: {
                        markers: {
                            shape: 'square',
                            width: 20,
                            height: 20
                        }
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 300
                            },
                            legend: {
                                markers: {
                                    shape: 'square',
                                    width: 20,
                                    height: 20
                                }
                            }
                        }
                    }]
                };
                if (chart6_id) {
                    let chart6 = new ApexCharts(chart6_id, chart_6_options);
                    chart6.render();
                }
            },
            error: function () {
                // error log
            }
        });
    }

    function MakeTopSellerTable(type) {
        let tableBody = $('#topSellerTableBody');
        if(tableBody){
            tableBody.empty();
        }
        $.ajax({
            type: "POST",
            url: "{{ route('dashboard.top.seller') }}",
            data: {
                _token: "{{ csrf_token() }}",
                type: type
            },
            success: function (response) {
                let data = JSON.parse(response);
                let html = ``;
                data.forEach((element, index) => {
                    html += `<tr>`;
                    html += `<td>${index + 1}</td>`;
                    html += `<td><div class="d-flex align-items-center"><img src="{{ asset('public/storage/users/${element.user_profile_image}')}}" style="width:20px;height:20px"> <span>${element.user_name}</span></div></td>`;
                    html += `<td>${element.product_name}</td>`;
                    html += `<td>${element.total_product_sold}</td>`;
                    html += `<td>${element.total_sale}</td>`;
                    html += `</tr>`;
                });
                tableBody.append(html);
            }
        });
    }

    function MakeStoresGetMostCredit(type) {
        $.ajax({
            type: "POST",
            url: "{{ route('dashboard.store.get.most.credit') }}",
            data: {
                _token: "{{ csrf_token() }}",
                type: type
            },
            success: function (response) {
                let tableBody = $('#topStoreGetMostCredit');
                let html = ``;
                response.forEach((element, index) => {
                    html = `<tr>`;
                    html += `<td>${index + 1}</td>`;
                    html += `<td>${element.shop_id}</td>`;
                    html += `<td>${element.shop_name}</td>`;
                    html += `<td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}${element.total_credit}</td>`;
                    html += `</tr>`;
                    tableBody.append(html);
                });
            }
        });
    }

    function MakeSellerReceiveMaximumCredit(type) {
        $.ajax({
            type: "POST",
            url: "{{ route('dashboard.seller.get.maximum.credit') }}",
            data: {
                _token: "{{ csrf_token() }}",
                type: type
            },
            success: function (response) {
                let tableBody = $('#topSellerGetmaximumCredit');
                let html = ``;
                response.forEach((element, index) => {
                    html = `<tr>`;
                    html += `<td>${index + 1}</td>`;
                    html += `<td>${element.user_id}</td>`;
                    html += `<td>${element.user_name}</td>`;
                    html += `<td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}${element.total_credit}</td>`;
                    html += `</tr>`;
                    tableBody.append(html);
                });
            }
        });
    }

    function MakeAllOrdersTable() {
        if ($('#allOrdersTable').length > 0) {
            let Table = $("#allOrdersTable");
            if(Table){
                Table.empty();
            }
            $.ajax({
                type: "POST",
                url: "{{ route('dashboard.orders') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function (response) {
                    console.log(response);
                    let html = ``;
                    response.forEach((element, index) => {
                        html += `<tr>`;
                        html += `<td>${element.date}</td>`;
                        html += `<td>${element.seller_name}/${element.seller_id}</td>`;
                        html += `<td>${element.shop_name}/${element.shop_id}</td>`;
                        html += `<td>${element.price_type}</td>`;
                        if (element.payment_type == 'Credit') {
                            html += `<td><span class="btn-sm btn-warning cursor-pointer">Credit</span></td>`
                        } else if (element.payment_type == 'Cash') {
                            html += `<td><span class="btn-sm btn-success cursor-pointer">Cash</span></td>`
                        }
                        html += `<td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}${element.grand_total}</td>`;
                        html += `</tr>`;
                    });
                    Table.append(html);
                }
            });
        }
    }

    function ValidateEmail() {
        let id = $("#id").val();
        let email = $("#email").val();
        if (email != '') {
            // status = false;
            // $("#email_error").show();
            // $("#email_error").text('').text('Email already exists');
            $.ajax({
                type: "POST",
                url: "{{ route('validate.email') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    email: email
                },
                success: function (response) {
                    if (response === false) {
                        $("#email_error").show();
                        $("#email_error").text('').text('Email already exists');
                        $("#submit-btn").attr('disabled', "");
                    } else {
                        $("#email_error").hide();
                        $("#email_error").text('');
                        $("#submit-btn").removeAttr('disabled');
                    }
                },
                error: function () { }
            });
        } else {
            $("#email_error").hide();
            $("#email_error").text('');
            $("#submit-btn").removeAttr('disabled');
        }
    }

    function CheckShopName() {
        let name = $("#name").val();
        if (name != '') {
            $.ajax({
                type: "POST",
                url: "{{ route('shops.name.check') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    name: name
                },
                success: function (response) {
                    if (response === false) {
                        $("#name_error").show();
                        $("#name_error").text('').text('Name already exists');
                        $("#submit-btn").attr('disabled', "");
                    } else {
                        $("#name_error").hide();
                        $("#name_error").text('');
                        $("#submit-btn").removeAttr('disabled');
                    }
                },
                error: function () { }
            });
        } else {
            $("#name_error").hide();
            $("#name_error").text('');
            $("#submit-btn").removeAttr('disabled');
        }
    }

    // Countries - START
    function MakeCitiesTable() {
        let Table = $("#cityTable");
        if (Table.length > 0) {
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "destroy": true,
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('cities.warehouse.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [{
                    data: 'id'
                },
                {
                    data: 'name'
                },
                {
                    data: 'action',
                    orderable: false
                },
                ],
                'order': [0, 'asc'],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    function DeleteCity(itemId) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            cancelButtonColor: 'btn btn-secondary',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('cities.warehouse.delete') }}",
                    data: {
                        id: itemId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                '{{ __("messages.alerts.deleted") }}',
                                '{{ __("messages.alerts.item_deleted") }}',
                                'success'
                            ).then(() => {
                                $('#cityTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire(
                                '{{ __("messages.alerts.error") }}',
                                '{{ __("messages.alerts.delete_error") }}',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            '{{ __("messages.alerts.error") }}',
                            '{{ __("messages.alerts.delete_error") }}',
                            'error'
                        );
                    }
                });
            }
        });
    }

    function MakeSellingCitiesTable() {
        let Table = $("#sellingCityTable");
        if (Table.length > 0) {
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "destroy": true,
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('cities.selling.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [{
                    data: 'id'
                },
                {
                    data: 'name'
                },
                {
                    data: 'action',
                    orderable: false
                },
                ],
                'order': [0, 'asc'],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    function DeleteSellingCity(itemId) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            cancelButtonColor: 'btn btn-secondary',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('cities.warehouse.delete') }}",
                    data: {
                        id: itemId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                '{{ __("messages.alerts.deleted") }}',
                                '{{ __("messages.alerts.item_deleted") }}',
                                'success'
                            ).then(() => {
                                $('#cityTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire(
                                '{{ __("messages.alerts.error") }}',
                                '{{ __("messages.alerts.delete_error") }}',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            '{{ __("messages.alerts.error") }}',
                            '{{ __("messages.alerts.delete_error") }}',
                            'error'
                        );
                    }
                });
            }
        });
    }
    // Countries - END

    // Users - START
    function MakeUsersTable() {
        let Table = $("#usersTable");
        if (Table.length > 0) {
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "destroy": true,
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('users.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [{
                    data: 'id'
                },
                {
                    data: 'image'
                },
                {
                    data: 'name'
                },
                {
                    data: 'email'
                },
                {
                    data: 'password'
                },
                {
                    data: 'status'
                },
                {
                    data: 'action',
                    orderable: false
                },
                ],
                'order': [0, 'asc'],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    function changeUserStatus(e) {
        let id = e.split('||')[1];
        let status = e.split('||')[0];
        let html = '<option value="">{{__('messages.modals.user_modal.select')}}</option>';

        if (status == 0) {
            $('#statusOption').html("");
            html += '<option value="1">{{__('messages.modals.user_modal.active')}}</option>';
            $('#statusOption').append(html);
        } else if (status == 1) {
            $('#statusOption').html("");
            html += '<option value="0">{{__('messages.modals.user_modal.ban')}}</option>';
            $('#statusOption').append(html);
        }

        $("#changeUserStatusId").val(id);
        $("#changeUserStatusModal").modal('toggle');
    }

    function DeleteUser(itemId) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            cancelButtonColor: 'btn btn-secondary',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    type: "POST",
                    url: "{{ route('users.delete') }}",
                    data: {
                        id: itemId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                '{{ __("messages.alerts.deleted") }}',
                                '{{ __("messages.alerts.item_deleted") }}',
                                'success'
                            ).then(() => {
                                $('#usersTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire(
                                '{{ __("messages.alerts.error") }}',
                                '{{ __("messages.alerts.delete_error") }}',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            '{{ __("messages.alerts.error") }}',
                            '{{ __("messages.alerts.delete_error") }}',
                            'error'
                        );
                    }
                });
            }
        });
    }
    // Customers - END

    // Sellers - START
    function MakeSellersTable() {
        let Table = $("#sellersTable");
        if (Table.length > 0) {
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "destroy": true,
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('sellers.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [{
                    data: 'id'
                },
                {
                    data: 'seller_id'
                },
                {
                    data: 'profile'
                },
                {
                    data: 'name'
                },
                {
                    data: 'email'
                },
                {
                    data: 'phone'
                },
                {
                    data: 'status'
                },
                {
                    data: 'action',
                    orderable: false
                },
                ],
                'order': [0, 'asc'],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                'dom': 'rtip'
            });
        }
    }

    function makeTable(table,type = null){
        let saleTable = $("#sale-table-div");
        let expenseTable = $("#expense-table-div");
        let itemsTable = $("#items-table-div");
        let loanTable = $("#loan-table-div");
        let bonusTable = $("#bonus-table-div");

        saleTable.hide();
        expenseTable.hide();
        itemsTable.hide();
        loanTable.hide();
        bonusTable.hide();

        if (table == 'total_sale') {
            MakeViewSellerTotalSaleTable(type);
            saleTable.show();
        } else if (table == 'total_expense') {
            MakeViewSellerTotalExpenseTable();
            expenseTable.show();
        } else if (table == 'total_items') {
            MakeViewSellerTotalItemsTable();
            itemsTable.show();
        } else if (table == 'total_loan') {
            MakeViewSellerTotalLoanTable();
            loanTable.show();
        } else if (table == 'total_bonus') {
            MakeViewSellerTotalBonusTable();
            bonusTable.show();
        }
    }

    function MakeViewSellerTotalSaleTable(type) {
        let Table = $("#sellerTotalSaleTable");
        if (Table.length > 0) {
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "destroy": true,
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('sellers.view.sale') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val(),
                        "seller_id": $("#seller_id").val(),
                        "type": type
                    }
                },
                'columns': [
                    {data: 'id'},
                    {data: 'shop_name'},
                    {data: 'cash'},
                    {data: 'loan'},
                    {data: 'status'},
                ],
                'order': [0, 'asc'],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                'dom': 'rtip'
            });
        }
    }

    function MakeViewSellerTotalExpenseTable() {
        let Table = $("#sellerTotalExpenseTable");
        if (Table.length > 0) {
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "destroy": true,
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('sellers.view.expense') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val(),
                        "seller_id": $("#seller_id").val()
                    }
                },
                'columns': [
                    {data: 'id'},
                    {data: 'seller_name'},
                    {data: 'date'},
                    {data: 'amount'},
                    {data: 'action'},
                ],
                'order': [0, 'asc'],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                'dom': 'rtip'
            });
        }
    }

    function MakeViewSellerTotalItemsTable() {
        let Table = $("#sellerTotalItemsTable");
        if (Table.length > 0) {
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "destroy": true,
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('sellers.view.items') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val(),
                        "seller_id": $("#seller_id").val()
                    }
                },
                'columns': [
                    {data: 'id'},
                    {data: 'product_name'},
                    {data: 'quantity'},
                    {data: 'boxes'},
                ],
                'order': [0, 'asc'],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                'dom': 'rtip'
            });
        }
    }

    function MakeViewSellerTotalLoanTable() {
        let Table = $("#sellerTotalLoanTable");
        if (Table.length > 0) {
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "destroy": true,
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('sellers.view.loan') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val(),
                        "seller_id": $("#seller_id").val()
                    }
                },
                'columns': [
                    {data: 'id'},
                    {data: 'shop_name'},
                    {data: 'date'},
                    {data: 'loan'},
                ],
                'order': [0, 'asc'],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                'dom': 'rtip'
            });
        }
    }

    function MakeViewSellerTotalBonusTable() {
        let Table = $("#sellerTotalBonusTable");
        if (Table.length > 0) {
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "destroy": true,
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('sellers.view.bonus') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val(),
                        "seller_id": $("#seller_id").val()
                    }
                },
                'columns': [
                    {data: 'id'},
                    {data: 'shop_name'},
                    {data: 'date'},
                    {data: 'bonus'},
                ],
                'order': [0, 'asc'],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                'dom': 'rtip'
            });
        }
    }

    function changeSellerStatus(e) {
        let id = e.split('||')[1];
        let status = e.split('||')[0];
        let html = '<option value="">{{__('messages.modals.seller_modal.select')}}</option>';

        if (status == 0) {
            $('#statusOption').html("");
            html += '<option value="1">{{__('messages.modals.seller_modal.active')}}</option>';
            $('#statusOption').append(html);
        } else if (status == 1) {
            $('#statusOption').html("");
            html += '<option value="0">{{__('messages.modals.seller_modal.ban')}}</option>';
            $('#statusOption').append(html);
        }

        $("#changeSellerStatusId").val(id);
        $("#changeSellerStatusModal").modal('toggle');
    }

    function DeleteSellers(itemId) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('sellers.delete') }}",
                    data: {
                        id: itemId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                '{{ __("messages.alerts.deleted") }}',
                                '{{ __("messages.alerts.item_deleted") }}',
                                'success'
                            ).then(() => {
                                $('#sellersTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire(
                                '{{ __("messages.alerts.error") }}',
                                '{{ __("messages.alerts.delete_error") }}',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            '{{ __("messages.alerts.error") }}',
                            '{{ __("messages.alerts.delete_error") }}',
                            'error'
                        );
                    }
                });
            }
        });
    }

    function MakeSellerTargetTable() {
        let Table = $("#targetTable");
        if (Table.length > 0) {
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "destroy": true,
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('sellers.target.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val(),
                        "id": $("#seller_id").val()
                    }
                },
                'columns': [
                    { data: 'id' },
                    { data: 'month' },
                    { data: 'year' },
                    { data: 'quantity' },
                    { data: 'completion' },
                    { data: 'action', orderable: false },
                ],
                'order': [0, 'asc'],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                'dom': 'rtip'
            });
        }
    }

    // function targetAddModal(e) {
    //     $("#id").val(e);
    //     $("#addTargetModal").modal('toggle');
    // }

    // function targetEditModal(e) {
    //     let id = e.split('||')[1];
    //     let seller_id = e.split('||')[2];
    //     let product_id = e.split('||')[3];
    //     let quantity = e.split('||')[4];
    //     let month = e.split('||')[5];
    //     let year = e.split('||')[6];

    //     $("#target_id").val(id);
    //     $("#target_seller_id").val(seller_id);

    //     $.ajax({
    //         type: "GET",
    //         url: "{{ route('sellers.target.products') }}",
    //         data: {
    //             _token: "{{ csrf_token() }}"
    //         },
    //         success: function (data) {
    //             let productHTML = `<option value="">Select</option>`;
    //             data.forEach(element => {
    //                 if (element.id == product_id) {
    //                     productHTML += `<option value="${element.id}" selected>${element.name}</option>`;
    //                 }else{
    //                     productHTML += `<option value="${element.id}">${element.name}</option>`;
    //                 }
    //             });
    //             $("#edit_product").append(productHTML);
    //             $("#edit_product_id").val(product_id);
    //         }
    //     });

    //     $("#edit_quantity").val(quantity);

    //     let monthsArray = [
    //         'January',
    //         'February',
    //         'March',
    //         'April',
    //         'May',
    //         'June',
    //         'July',
    //         'August',
    //         'September',
    //         'October',
    //         'November',
    //         'December',
    //     ];
    //     let monthsHTML = `<option>Select</option>`;

    //     monthsArray.forEach(element => {
    //         if (element == month) {
    //             monthsHTML += `<option value="${element}" selected>${element}</option>`;
    //         } else {
    //             monthsHTML += `<option value="${element}">${element}</option>`;
    //         }
    //     });

    //     $("#edit_month").append(monthsHTML);
    //     $("#edit_year").val(year);

    //     $("#editTargetModal").modal('toggle');
    // }

    // Sellers - END

    // Office Sellers - END
    function MakeOfficeSellersTable() {
        let Table = $("#officeSellersTable");
        if (Table.length > 0) {
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "destroy": true,
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('sellers.office.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [{
                    data: 'id'
                },
                {
                    data: 'seller_id'
                },
                {
                    data: 'profile'
                },
                {
                    data: 'name'
                },
                {
                    data: 'email'
                },
                {
                    data: 'phone'
                },
                {
                    data: 'status'
                },
                {
                    data: 'action',
                    orderable: false
                },
                ],
                'order': [0, 'asc'],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                'dom': 'rtip'
            });
        }
    }

    function changeOfficeSellerStatus(e) {
        let id = e.split('||')[1];
        let status = e.split('||')[0];
        let html = '<option value="">{{__('messages.modals.seller_modal.select')}}</option>';

        if (status == 0) {
            $('#statusOption').html("");
            html += '<option value="1">{{__('messages.modals.seller_modal.active')}}</option>';
            $('#statusOption').append(html);
        } else if (status == 1) {
            $('#statusOption').html("");
            html += '<option value="0">{{__('messages.modals.seller_modal.ban')}}</option>';
            $('#statusOption').append(html);
        }

        $("#changeOfficeSellerStatusId").val(id);
        $("#changeOfficeSellerStatusModal").modal('toggle');
    }

    function DeleteOfficeSellers(itemId) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    type: "POST",
                    url: "{{ route('sellers.office.delete') }}",
                    data: {
                        id: itemId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                '{{ __("messages.alerts.deleted") }}',
                                '{{ __("messages.alerts.item_deleted") }}',
                                'success'
                            ).then(() => {
                                $('#officeSellersTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire(
                                '{{ __("messages.alerts.error") }}',
                                '{{ __("messages.alerts.delete_error") }}',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            '{{ __("messages.alerts.error") }}',
                            '{{ __("messages.alerts.delete_error") }}',
                            'error'
                        );
                    }
                });
            }
        });
    }
    // Office Sellers - END

    // Category - START
    function MakeCategoryTable() {
        let Table = $("#categoryTable");
        if (Table.length > 0) {
            Table.DataTable().destroy();
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('categories.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [{
                    data: 'id'
                },
                {
                    data: 'icon'
                },
                {
                    data: 'name'
                },
                {
                    data: 'action',
                    orderable: false
                },
                ],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    function DeleteCategory(itemId) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    type: "POST",
                    url: "{{ route('categories.delete') }}",
                    data: {
                        id: itemId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                '{{ __("messages.alerts.deleted") }}',
                                '{{ __("messages.alerts.item_deleted") }}',
                                'success'
                            ).then(() => {
                                $('#categoryTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire(
                                '{{ __("messages.alerts.error") }}',
                                '{{ __("messages.alerts.delete_error") }}',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            '{{ __("messages.alerts.error") }}',
                            '{{ __("messages.alerts.delete_error") }}',
                            'error'
                        );
                    }
                });
            }
        });

    }
    // Category - END

    // Products - START
    function MakeProductTable() {
        let Table = $("#productTable");
        if (Table.length > 0) {
            Table.DataTable().destroy();
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('products.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val(),
                        "city": $("#city").val()
                    }
                },
                'columns': [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'category'},
                    {data: 'city'},
                    {data: 'stock'},
                    {data: 'boxes'},
                    {data: 'gen_price'},
                    {data: 'whole_price'},
                    {data: 'action', orderable: false},
                ],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    function CalculateStock() {
        let stock_amount = null;
        let pieces = $('#pieces').val();
        let box = $('#box').val();
        $.ajax({
            type: "POST",
            url: "{{ route('products.stock.pieces') }}",
            data: {
                _token: "{{ csrf_token() }}",
                id: $('#id').val(),
            },
            success: function (response) {
                if (response.success) {
                    if (response.pieces && box) {
                        stock_amount = parseInt(response.pieces) * parseInt(box);
                        $('#stock').val('').val(stock_amount);
                    }else{
                        $('#stock').val('');
                    }
                }
            }
        });

    }

    function DeleteProduct(itemId) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('products.delete') }}",
                    data: {
                        id: itemId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                '{{ __("messages.alerts.deleted") }}',
                                '{{ __("messages.alerts.item_deleted") }}',
                                'success'
                            ).then(() => {
                                $('#productTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire(
                                '{{ __("messages.alerts.error") }}',
                                '{{ __("messages.alerts.delete_error") }}',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            '{{ __("messages.alerts.error") }}',
                            '{{ __("messages.alerts.delete_error") }}',
                            'error'
                        );
                    }
                });
            }
        });
    }

    function ViewProduct(id, image, name, category_name, gen_price, wholesale_price, ex_price, description) {
        gen_price = gen_price ? `{{\App\Helpers\SiteHelper::settings()['Currency_Icon']}}` + gen_price : gen_price;
        wholesale_price = wholesale_price ? `{{\App\Helpers\SiteHelper::settings()['Currency_Icon']}}` + wholesale_price : wholesale_price;
        ex_price = ex_price ? `{{\App\Helpers\SiteHelper::settings()['Currency_Icon']}}` + ex_price : ex_price;
        $("#product_id").val(id);
        $("#product-name-heading").text(name);
        $("#product-img").html(`<img src="${image}" class="view-modal-img" alt="" />`);
        $("#product-name").text(name);
        $("#product-category").text(category_name);
        $("#product-gen-price").text(gen_price);
        $("#product-wholesale-price").text(wholesale_price);
        $("#product-ex-price").text(ex_price);
        $("#description").text(description);
        $("#viewProductModal").modal('toggle');
    }

    function redirectToEdit() {
        let product_id = $('#product_id').val();
        let url = "{{ route('products.edit', ':id') }}";
        url = url.replace(':id', product_id);
        window.location.href = url;
    }

    function OpenProductStock(id) {
        $("#id").val(id);
        $("#addStockModal").modal('toggle');
    }

    function MakeStockTable() {
        let Table = $("#stockTable");
        if (Table.length > 0) {
            Table.DataTable().destroy();
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('products.stock.load') }}",
                    "type": "POST",
                    "data": {
                        "product_id": $("#product_id").val(),
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [
                    {
                        data: 'id'
                    },
                    {
                        data: 'pieces'
                    },
                    {
                        data: 'box'
                    },
                    {
                        data: 'stock'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'action',
                        orderable: false
                    },
                ],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    function DeleteStock(itemId) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('products.stock.delete') }}",
                    data: {
                        id: itemId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                '{{ __("messages.alerts.deleted") }}',
                                '{{ __("messages.alerts.item_deleted") }}',
                                'success'
                            ).then(() => {
                                $('#stockTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire(
                                '{{ __("messages.alerts.error") }}',
                                '{{ __("messages.alerts.delete_error") }}',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            '{{ __("messages.alerts.error") }}',
                            '{{ __("messages.alerts.delete_error") }}',
                            'error'
                        );
                    }
                });
            }
        });
    }
    // Products - END

    // Shops - START
    function MakeShopsTable() {
        let Table = $("#shopTable");
        if (Table.length > 0) {
            Table.DataTable().destroy();
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('shops.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [
                    {
                        data: 'id'
                    },
                    {
                        data: 'shop_id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'total_sale'
                    },
                    {
                        data: 'location'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'action',
                        orderable: false
                    },
                ],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    function DeleteShop(itemId) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('shops.delete') }}",
                    data: {
                        id: itemId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                '{{ __("messages.alerts.deleted") }}',
                                '{{ __("messages.alerts.item_deleted") }}',
                                'success'
                            ).then(() => {
                                $('#shopTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire(
                                '{{ __("messages.alerts.error") }}',
                                '{{ __("messages.alerts.delete_error") }}',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            '{{ __("messages.alerts.error") }}',
                            '{{ __("messages.alerts.delete_error") }}',
                            'error'
                        );
                    }
                });
            }
        });
    }
    // Shops - END

    // Sales - START
    function MakeSalesTable() {
        let Table = $("#saleTable");
        if (Table.length > 0) {
            Table.DataTable().destroy();
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('sales.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [
                    {
                        data: 'date'
                    },
                    {
                        data: 'seller_id_name'
                    },
                    {
                        data: 'total_items'
                    },
                    {
                        data: 'total_total'
                    },
                    {
                        data: 'payment_status'
                    },
                    {
                        data: 'cash'
                    },
                    {
                        data: 'loan'
                    },
                    {
                        data: 'action',
                        orderable: false
                    },
                ],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    function DeleteSale(itemId) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('sales.delete') }}",
                    data: {
                        id: itemId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                '{{ __("messages.alerts.deleted") }}',
                                '{{ __("messages.alerts.item_deleted") }}',
                                'success'
                            ).then(() => {
                                $('#saleTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire(
                                '{{ __("messages.alerts.error") }}',
                                '{{ __("messages.alerts.delete_error") }}',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            '{{ __("messages.alerts.error") }}',
                            '{{ __("messages.alerts.delete_error") }}',
                            'error'
                        );
                    }
                });
            }
        });
    }

    function MakeOfficeSalesTable() {
        let Table = $("#officeSaleTable");
        if (Table.length > 0) {
            Table.DataTable().destroy();
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('sales.office.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [
                    {
                        data: 'date'
                    },
                    {
                        data: 'seller_id_name'
                    },
                    {
                        data: 'total_items'
                    },
                    {
                        data: 'boxes'
                    },
                    {
                        data: 'total_total'
                    },
                    {
                        data: 'payment_status'
                    },
                    {
                        data: 'cash'
                    },
                    {
                        data: 'loan'
                    },
                    {
                        data: 'action',
                        orderable: false
                    },
                ],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    function DeleteOfficeSale(itemId) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('sales.office.delete') }}",
                    data: {
                        id: itemId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                '{{ __("messages.alerts.deleted") }}',
                                '{{ __("messages.alerts.item_deleted") }}',
                                'success'
                            ).then(() => {
                                $('#officeSaleTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire(
                                '{{ __("messages.alerts.error") }}',
                                '{{ __("messages.alerts.delete_error") }}',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            '{{ __("messages.alerts.error") }}',
                            '{{ __("messages.alerts.delete_error") }}',
                            'error'
                        );
                    }
                });
            }
        });
    }

    let DataArray = [];
    if ($("#sale-edit-page").length > 0) {
        DataArray = $("#jsonProducts").val() != '' ? JSON.parse($("#jsonProducts").val()) : [];
    }

    function PriceTypeSelected() {
        DataArray = [];
        $("#jsonProducts").val('');
        $('#sale-product-table-body').html('');
        $('#grand_total').val(0);
        $('#grandTotal').text(0);
    }

    function SearchBox() {
        let searchTerm = $("#product").val();
        let payment_type = $("#price_type").val();
        let city_id = $("#city_id").val();
        console.log(payment_type);
        if (payment_type === '') {
            Swal.fire({
                title: '{{ __("messages.alerts.payment_type_not_selected") }}',
                text: '{{ __("messages.alerts.please_select_an_option") }}',
                icon: 'warning',
                confirmButtonText: '{{ __("messages.alerts.ok_button") }}'
            }).then(() => {
                $("#product").val('');
            });
        } else {
            if (searchTerm) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('sales.products') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        searchTerm: searchTerm,
                        city_id: city_id
                    },
                    success: function (response) {
                        try {
                            dataArray = JSON.parse(response);
                        } catch (e) {
                            console.error('Error parsing JSON:', e);
                            return;
                        }
                        if (Array.isArray(dataArray) && dataArray.length > 0) {
                            $("#search-box").removeClass('d-none');
                            $("#search-box").html('');

                            const existingIds = DataArray.map(item => item.id);
                            dataArray.forEach(element => {
                                const hasExistingId = existingIds.includes(element.id);
                                let singleElement;
                                if (hasExistingId) {
                                    singleElement =
                                        `<div class="search-box-element-active px-2 py-2">${element.name}</div>`;
                                } else {
                                    let price_type = null;
                                    if (payment_type == 'wholesale_price') {
                                        price_type = element.wholesale_price;
                                    } else if (payment_type == 'extra_price') {
                                        price_type = element.extra_price;
                                    } else {
                                        price_type = element.retail_price;
                                    }
                                    singleElement =
                                        `<div class="search-box-element px-2 py-2 cursor-pointer" onclick="addElementToTable(${element.id}, '${element.name}', ${price_type}, '${element.wholesale_price}', '${element.extra_price}', ${element.total_stock}, '${element.boxes_pieces}')">${element.name}</div>`;
                                }
                                $("#search-box").append(singleElement);
                            });
                        } else {
                            $("#search-box").addClass('d-none');
                            $("#search-box").html('');
                        }
                    },
                    error: function () { }
                });
            } else {
                $("#search-box").addClass('d-none');
                $("#search-box").html('');
            }
        }
    }

    function addElementToTable(id, name, price, wholesale_price, extra_price, stock, pieces) {
        DataArray.push({
            'id': id,
            'name': name,
            'price': price,
            'wholesale_price': wholesale_price,
            'extra_price': extra_price,
            'stock': stock,
            'pieces': pieces,
            'quantity': 0,
            'sub_total': 0
        });

        $('#sale-product-table-body').html('');
        DataArray.forEach(element => {
            let html = `<tr id="tr-${element.id}">`;
            html += `<td>${element.name}</td>`;
            html +=
                `<td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}<span id="price-${element.id}">${element.price}</span></td>`;
            html += `<td id="stock-${element.id}">${element.stock}</td>`;
            html += `<td id="boxes-${element.id}">${element.pieces}</td>`;
            if (element.quantity != 0) {
                html += `<td>
                    <div class="d-flex">
                        <div>
                            <button type="button" id="minus-btn" class="btn btn-primary quantity-left-minus btn-number px-2" onclick="removeQuantity(${element.id});">
                                <span class="fa fa-minus"></span>
                            </button>
                        </div>
                        <div>
                            <input type="number" id="quantity-${element.id}" name="quantity" class="form-control input-number" value="${element.quantity}" min="1" required onkeyup="incrementSubTotal(${element.id})">
                        </div>
                        <div>
                            <button type="button" id="plus-btn" class="btn btn-primary quantity-right-plus btn-number px-2" onclick="addQuantity(${element.id});">
                                <span class="fa fa-plus"></span>
                            </button>
                        </div>
                    </div>
                </td>`;
            } else {
                html += `<td>
                    <div class="d-flex">
                        <div>
                            <button type="button" id="minus-btn" class="btn btn-primary quantity-left-minus btn-number px-2" onclick="removeQuantity(${element.id});">
                                <span class="fa fa-minus"></span>
                            </button>
                        </div>
                        <div>
                            <input type="number" id="quantity-${element.id}" name="quantity" class="form-control input-number" value="0" min="1" required onkeyup="incrementSubTotal(${element.id})">
                        </div>
                        <div>
                            <button type="button" id="plus-btn" class="btn btn-primary quantity-right-plus btn-number px-2" onclick="addQuantity(${element.id});">
                                <span class="fa fa-plus"></span>
                            </button>
                        </div>
                    </div>
                </td>`;
            }
            if (element.sub_total != 0) {
                html +=
                    `<td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}${element.sub_total}</td>`;
            } else {
                html +=
                    `<td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}<span id="sub-total-price-${element.id}">0</span></td>`;
            }
            html +=
                `<td><span id="" onclick="removeElementFormTable(${element.id})" class="text-danger fs-6 cursor-pointer ml-1" data-toggle="tooltip" title="{{ __('messages.btns.delete') }}"><i class="fa fa-trash"></i></span></td>`;
            html += `</tr>`;

            $('#jsonProducts').val(JSON.stringify(DataArray));
            $('#sale-product-table-body').append(html);
        });

        $("#product").val('');
        $("#search-box").addClass('d-none');
        $("#search-box").html('');
        updateGrandTotal();
    }

    function addQuantity(id) {
        const val = $(`#quantity-${id}`).val();
        const stock = $(`#stock-${id}`).text();

        if (parseInt(val) < parseInt(stock)) {
            const incrementedVal = parseInt(val) + 1;
            $(`#quantity-${id}`).val(incrementedVal);
            incrementSubTotal(id);
        }

    }

    function incrementSubTotal(id) {
        $("#product_error").text('').hide();
        const quantity = $(`#quantity-${id}`).val() != '' ? parseInt($(`#quantity-${id}`).val()) : 0;
        const price = parseInt($(`#price-${id}`).text());
        const stock = parseInt($(`#stock-${id}`).text());
        let subTotal = 0;

        if (quantity < 0) {
            $(`#quantity-${id}`).val(0);
            $(`#sub-total-price-${id}`).text('').text(subTotal);
        } else if (quantity >= stock) {
            subTotal = price * stock;
            $(`#quantity-${id}`).val(0).val(stock);
            $(`#sub-total-price-${id}`).text('').text(subTotal);
        } else if (quantity != 0) {
            subTotal = price * quantity;
            $(`#sub-total-price-${id}`).text('').text(subTotal);
        } else {
            $(`#sub-total-price-${id}`).text('').text(subTotal);
        }
        DataArray.forEach(element => {
            if (element.id == id) {
                element.quantity = quantity >= stock ? stock : quantity;
                element.sub_total = subTotal;
            }
        });
        $('#jsonProducts').val(JSON.stringify(DataArray));
        updateGrandTotal();
    }

    function removeQuantity(id) {
        let quantity = parseInt($(`#quantity-${id}`).val());
        let parsedVal = parseInt(quantity);
        if (parsedVal != 0) {
            let decrementedVal = parsedVal - 1;
            $(`#quantity-${id}`).val(decrementedVal);
        }
        decrementSubTotal(id);
    }

    function decrementSubTotal(id) {
        let quantity = parseInt($(`#quantity-${id}`).val());
        let price = parseInt($(`#price-${id}`).text());
        let subTotal = parseInt($(`#sub-total-price-${id}`).text());
        subTotal = price * quantity;
        $(`#sub-total-price-${id}`).text('').text(subTotal);

        DataArray.forEach(element => {
            if (element.id == id) {
                element.quantity = quantity;
                element.sub_total = subTotal;
            }
        });
        $('#jsonProducts').val(JSON.stringify(DataArray));
        updateGrandTotal();
    }

    function removeElementFormTable(id) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                let val = $("#tr-" + id).remove();

                const index = DataArray.findIndex(item => item.id === id);
                if (index !== -1) {
                    DataArray.splice(index, 1);
                }

                $('#jsonProducts').val(JSON.stringify(DataArray));
                updateGrandTotal();

                Swal.fire(
                    '{{ __("messages.alerts.deleted") }}',
                    '{{ __("messages.alerts.item_deleted") }}',
                    'success'
                );
            }
        });
    }

    function updateGrandTotal() {
        const totalSubTotal = DataArray.reduce((total, item) => parseFloat(total) + parseFloat(item.sub_total), 0);
        $("#grandTotal").text('').text(totalSubTotal);
        $("#grand_total").val('').val(totalSubTotal);
    }

    function SubmitSaleForm() {
        let status = true;
        let products = null;
        const jsonProducts = $("#jsonProducts").val();
        const bonusValue = $("input[name='bonus']:checked").val();

        if (jsonProducts == '' || jsonProducts == [] || jsonProducts == '[]') {
            status = false;
            $("#product_error").text('').text('Please select atleast one product').show();
        } else {
            $("#product_error").text('').hide();
        }

        if (jsonProducts != '') {
            products = JSON.parse(jsonProducts);

            products.forEach(element => {
                if (element.quantity == 0 || element.quantity == '' || element.quantity == "") {
                    status = false;
                    $("#product_error").text('').text('Product quantity must be greater than 0').show();
                } else {
                    $("#product_error").text('').hide();
                }
            });
        }

        if (!$("#sale-form")[0].reportValidity()) {
            status = false;
        }

        if (status) {
            $("#sale-form").submit();
        }
    }

    function SubmitOfficeSaleForm() {
        let status = true;
        let products = null;
        const jsonProducts = $("#jsonProducts").val();

        if (jsonProducts == '' || jsonProducts == [] || jsonProducts == '[]') {
            status = false;
            $("#product_error").text('').text('Please select atleast one product').show();
        } else {
            $("#product_error").text('').hide();
        }

        if (jsonProducts != '') {
            products = JSON.parse(jsonProducts);

            products.forEach(element => {
                if (element.quantity == 0 || element.quantity == '' || element.quantity == "") {
                    status = false;
                    $("#product_error").text('').text('Product quantity must be greater than 0').show();
                } else {
                    $("#product_error").text('').hide();
                }
            });
        }

        if (!$("#office-sale-form")[0].reportValidity()) {
            status = false;
        }

        if (status) {
            $("#office-sale-form").submit();
        }
    }

    function changeSaleStatus(e) {
        let id = e.split('||')[1];
        let status = e.split('||')[0];
        let html = '<option value="">{{__('messages.modals.sale_modal.select')}}</option>';

        if (status == 'Pending') {
            $('#statusOption').html("");
            html += '<option value="Completed">{{__('messages.modals.sale_modal.completed')}}</option>';
            $('#statusOption').append(html);
        } else if (status == 'Completed') {
            $('#statusOption').html("");
            html += '<option value="Pending">{{__('messages.modals.sale_modal.pending')}}</option>';
            $('#statusOption').append(html);
        }

        $("#changeSaleStatusId").val(id);
        $("#changeSaleStatusModal").modal('toggle');
    }

    function changeOfficeLoanStatus(e) {
        let id = e.split('||')[1];
        let status = e.split('||')[0];
        let html = '<option value="">{{__('messages.modals.sale_modal.select')}}</option>';

        if (status == 'Pending') {
            $('#statusOption').html("");
            html += '<option value="Completed">{{__('messages.modals.sale_modal.completed')}}</option>';
            $('#statusOption').append(html);
        } else if (status == 'Completed') {
            $('#statusOption').html("");
            html += '<option value="Pending">{{__('messages.modals.sale_modal.pending')}}</option>';
            $('#statusOption').append(html);
        }

        $("#changeOfficeLoanStatusId").val(id);
        $("#changeOfficeLoanStatusModal").modal('toggle');
    }

    function openSalePrintModal(divId) {
        // Get the content of the specified div
        const printContents = document.getElementById(divId).innerHTML;

        // Create a new window
        const printWindow = window.open();

        // Write the content to the new window
        printWindow.document.write('<html><head><title>Print</title>');
        printWindow.document.write(`<link rel="stylesheet" href="{{asset('public/assets/vendor/fonts/boxicons.css')}}" />
        <link rel="stylesheet" href="{{asset('public/assets/vendor/css/core.css')}}" class="template-customizer-core-css" />
        <link rel="stylesheet" href="{{asset('public/assets/vendor/css/theme-default.css')}}" class="template-customizer-theme-css" />
        <link rel="stylesheet" href="{{asset('public/assets/css/demo.css')}}" />

        <link rel="stylesheet" href="{{asset('public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />
        <link rel="stylesheet" href="{{asset('public/assets/vendor/libs/apex-charts/apex-charts.css')}}" />



        <link rel="stylesheet" href="{{asset('public/assets/vendor/libs/datatables/css/datatables.min.css')}}">

        <link rel="stylesheet" href="{{asset('public/assets/vendor/libs/select2-bt-5/css/select2.min.css')}}">
        <link rel="stylesheet" href="{{asset('public/assets/vendor/libs/select2-bt-5/css/select2-bootstrap-5-theme.min.css')}}">

        <link rel="stylesheet" href="{{asset('public/assets/vendor/libs/fontawesome/css/all.min.css')}}">
        <link rel="stylesheet" href="{{asset('public/assets/vendor/libs/fontawesome/css/fontawesome.min.css')}}">

        <link rel="stylesheet" href="{{asset('public/assets/vendors/libs/sweetalert2/sweetalert2.min.css')}}">

        <link rel="stylesheet" href="{{asset('public/assets/vendors/libs/toastr/toastr.min.css')}}">

        <link rel="stylesheet" href="{{asset('public/assets/css/custom.css')}}">

        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">
        `);
        printWindow.document.write('</head><body>');
        printWindow.document.write(printContents);
        printWindow.document.write('</body></html>');

        printWindow.document.close(); // Close the document
        printWindow.print(); // Trigger the print dialog
    }
    // Sales - END

    // Office Loan - END
    function MakeOfficeLoanTable() {
        let Table = $("#officeLoanTable");
        if (Table.length > 0) {
            Table.DataTable().destroy();
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('loan.office.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [
                    {
                        data: 'date'
                    },
                    {
                        data: 'seller_id_name'
                    },
                    {
                        data: 'total_items'
                    },
                    {
                        data: 'total_total'
                    },
                    {
                        data: 'payment_status'
                    },
                    {
                        data: 'cash'
                    },
                    {
                        data: 'loan'
                    },
                    {
                        data: 'action',
                        orderable: false
                    },
                ],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    function AddOfficeLoanPartialPayment(id){
        $("#changeOfficeLoanPartialPaymentId").val(id);
        $("#changeOfficeLoanPartialPaymentModal").modal('toggle');
    }

    // Orders - START
    function MakeReturnsTable() {
        let Table = $("#returnTable");
        if (Table.length > 0) {
            Table.DataTable().destroy();
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('returns.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [
                    { data: 'date' },
                    { data: 'seller_id_name' },
                    { data: 'total_items' },
                    { data: 'boxes' },
                    { data: 'grand_total' },
                    { data: 'action', orderable: false },
                ],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    function DeleteReturn(itemId) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('returns.delete') }}",
                    data: {
                        id: itemId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                '{{ __("messages.alerts.deleted") }}',
                                '{{ __("messages.alerts.item_deleted") }}',
                                'success'
                            ).then(() => {
                                $('#returnTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire(
                                '{{ __("messages.alerts.error") }}',
                                '{{ __("messages.alerts.delete_error") }}',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            '{{ __("messages.alerts.error") }}',
                            '{{ __("messages.alerts.delete_error") }}',
                            'error'
                        );
                    }
                });
            }
        });
    }

    function getProductsForReturn() {
        let date = $("#date").val();
        let seller_id = $("#seller_id").val();
        let city_id = $("#city_id").val();
        if (date && seller_id && city_id) {
            $.ajax({
                type: "POST",
                url: "{{ route('returns.seller.products') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    date: date,
                    seller_id: seller_id,
                    city_id: city_id
                },
                success: function (response) {
                    let products = JSON.parse(response);
                    let html = '';
                    products.forEach(element => {
                        element.sub_total = 0;
                        element.return_quantity = 0;
                        html += `
                        <tr>
                            <td>${element.name}</td>
                            <td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}<span id="return-price-${element.id}">${element.retail_price}</span></td>
                            <td class="text-primary">${element.total_stock}</td>
                            <td id="return-remaining-product-${element.id}">${element.remaining_product}</td>
                            <td id="return-boxes-${element.id}">${element.pieces}</td>
                            <td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}<span id="return-sub-total-${element.id}">0</span></td>
                            <td><input type="number" class="form-control input-number-return" id="return-quantity-${element.id}" name="return_quantity" value="0" min="0" onkeyup="changeReturn(${element.id})"></td>
                        </tr>`
                    });
                    $("#jsonReturnData").val(JSON.stringify(products));
                    if(products.length > 0) {
                        $("#sale_id").val(products[0].sale_id);
                    }
                    $('#return_products_table').html('').html(html);
                },
                error: function () { }
            });
        }
    }

    function changeReturn(id) {
        let remainingStock = $(`#return-remaining-product-${id}`).text() != '' ? parseInt($(`#return-remaining-product-${id}`).text()) : 0;
        let price = parseInt($(`#return-price-${id}`).text());
        let quantity = parseInt($(`#return-quantity-${id}`).val());
        let subTotal = 0;

        if (quantity < 0) {
            $(`#return-quantity-${id}`).val(0);
            $(`#return-sub-total-${id}`).text('').text(subTotal);
        } else if (quantity >= remainingStock) {
            subTotal = price * remainingStock;
            $(`#return-quantity-${id}`).val(0).val(remainingStock);
            $(`#return-sub-total-${id}`).text('').text(subTotal);
        } else if (quantity != 0) {
            subTotal = price * quantity;
            $(`#return-sub-total-${id}`).text('').text(subTotal);
        } else {
            $(`#return-sub-total-${id}`).text('').text(subTotal);
        }

        let ReturnProducts = JSON.parse($('#jsonReturnData').val());
        ReturnProducts.forEach(element => {
            if (element.id == id) {
                element.return_quantity = quantity >= remainingStock ? remainingStock : quantity;
                element.sub_total = subTotal;
            }
        });
        $('#jsonReturnData').val(JSON.stringify(ReturnProducts));
        updateReturnGrandTotal();
    }

    function updateReturnGrandTotal() {
        let ReturnProducts = JSON.parse($('#jsonReturnData').val());
        const totalSubTotal = ReturnProducts.reduce((total, item) => parseFloat(total) + parseFloat(item.sub_total), 0);
        $("#returnGrandTotal").text('').text(totalSubTotal);
        $("#return_grand_total").val('').val(totalSubTotal);
    }
    // Orders - END

    // Orders - START
    function MakeOrdersTable() {
        let Table = $("#orderTable");
        if (Table.length > 0) {
            Table.DataTable().destroy();
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('orders.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [
                    { data: 'id' },
                    { data: 'date' },
                    { data: 'seller_name_id' },
                    { data: 'shop_name_id' },
                    { data: 'price_type' },
                    { data: 'sale_type' },
                    { data: 'grand_total' },
                    { data: 'action', orderable: false },
                ],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    function DeleteOrder(itemId) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('orders.delete') }}",
                    data: {
                        id: itemId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                '{{ __("messages.alerts.deleted") }}',
                                '{{ __("messages.alerts.item_deleted") }}',
                                'success'
                            ).then(() => {
                                $('#orderTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire(
                                '{{ __("messages.alerts.error") }}',
                                '{{ __("messages.alerts.delete_error") }}',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            '{{ __("messages.alerts.error") }}',
                            '{{ __("messages.alerts.delete_error") }}',
                            'error'
                        );
                    }
                });
            }
        });
    }
    // Orders - END

    // Loan - START
    function MakeLoansTable() {
        let Table = $("#loanTable");
        if (Table.length > 0) {
            Table.DataTable().destroy();
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('loan.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [
                    // { data: 'id' },
                    { data: 'date' },
                    { data: 'seller_name_id' },
                    { data: 'shop_name_id' },
                    { data: 'status' },
                    { data: 'sale_type' },
                    { data: 'cash' },
                    { data: 'credit' },
                    { data: 'action', orderable: false },
                ],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }
    // Loan - END

    // Damage & Replace - START
    function MakeDamageTable() {
        let Table = $("#damageReplaceTable");
        if (Table.length > 0) {
            Table.DataTable().destroy();
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('damage_replace.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [
                    { data: 'date' },
                    { data: 'seller_id_name' },
                    { data: 'quantity' },
                    { data: 'boxes' },
                    { data: 'grand_total' },
                    { data: 'action', orderable: false },
                ],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    let DamageDataArray = [];
    if ($("#damage-edit-page").length > 0) {
        DamageDataArray = $("#jsonProducts").val() != '' ? JSON.parse($("#jsonProducts").val()) : [];
    }

    function DamageSearchBox() {
        let searchTerm = $("#product").val();
        if (searchTerm) {
            $.ajax({
                type: "POST",
                url: "{{ route('damage_replace.products') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    searchTerm: searchTerm
                },
                success: function (response) {
                    try {
                        damageDataArray = JSON.parse(response);
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        return;
                    }
                    if (Array.isArray(damageDataArray) && damageDataArray.length > 0) {
                        $("#search-box").removeClass('d-none');
                        $("#search-box").html('');

                        const existingIds = DamageDataArray.map(item => item.id);
                        damageDataArray.forEach(element => {
                            const hasExistingId = existingIds.includes(element.id);
                            let singleElement;
                            if (hasExistingId) {
                                singleElement =
                                    `<div class="search-box-element-active px-2 py-2">${element.name}</div>`;
                            } else {
                                singleElement =
                                    `<div class="search-box-element px-2 py-2 cursor-pointer" onclick="addDamageElementToTable(${element.id}, '${element.name}', ${element.retail_price}, '${element.wholesale_price}', '${element.extra_price}', ${element.total_stock}, '${element.pieces}')">${element.name}</div>`;
                            }
                            $("#search-box").append(singleElement);
                        });
                    } else {
                        $("#search-box").addClass('d-none');
                        $("#search-box").html('');
                    }
                },
                error: function () { }
            });
        } else {
            $("#search-box").addClass('d-none');
            $("#search-box").html('');
        }
    }

    function addDamageElementToTable(id, name, price, wholesale_price, extra_price, stock, pieces) {
        DamageDataArray.push({
            'id': id,
            'name': name,
            'price': price,
            'wholesale_price': wholesale_price,
            'extra_price': extra_price,
            'stock': stock,
            'pieces': pieces,
            'quantity': 0,
            'sub_total': 0
        });

        $('#sale-product-table-body').html('');
        let html = '';
        DamageDataArray.forEach(element => {
            html += `<tr id="tr-${element.id}">`;
            html += `<td>${element.name}</td>`;
            html +=
                `<td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}<span id="damage-price-${element.id}">${element.price}</span></td>`;
            html += `<td id="damage-stock-${element.id}">${element.stock}</td>`;
            html += `<td id="damage-boxes-${element.id}">${element.pieces}</td>`;
            if (element.sub_total != 0) {
                html +=
                    `<td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}<span id="damage-sub-total-${element.id}">${element.sub_total}</span></td>`;
            } else {
                html +=
                    `<td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}<span id="damage-sub-total-${element.id}">0</span></td>`;
            }
            if (element.quantity != 0) {
                html += `<td>
                    <input type="number" class="form-control input-number-damage" id="damage-quantity-${element.id}" name="damage_quantity" value="${element.quantity}" min="1" required onkeyup="changeDamage(${element.id})">
                </td>`;
            } else {
                html += `<td>
                    <input type="number" class="form-control input-number-damage" id="damage-quantity-${element.id}" name="damage_quantity" value="0" min="1" required onkeyup="changeDamage(${element.id})">
                </td>`;
            }
            html +=
                `<td><span id="" onclick="removeDamageElementFromTable(${element.id})" class="text-danger fs-6 cursor-pointer ml-1" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></span></td>`;
            html += `</tr>`;
        });

        $('#jsonProducts').val(JSON.stringify(DamageDataArray));
        $('#damage-product-table-body').html('').append(html);

        $("#product").val('');
        $("#search-box").addClass('d-none');
        $("#search-box").html('');
        updateDamageGrandTotal();
    }

    function removeDamageElementFromTable(id) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $("#tr-" + id).remove();
                const index = DamageDataArray.findIndex(item => item.id == id);
                if (index !== -1) {
                    DamageDataArray.splice(index, 1);
                }

                $('#jsonProducts').val(JSON.stringify(DamageDataArray));
                updateDamageGrandTotal();

                Swal.fire(
                    '{{ __("messages.alerts.deleted") }}',
                    '{{ __("messages.alerts.item_deleted") }}',
                    'success'
                );
            }
        });
    }

    function changeDamage(id) {
        let stock = $(`#damage-stock-${id}`).text() != '' ? parseInt($(`#damage-stock-${id}`).text()) : 0;
        let price = parseFloat($(`#damage-price-${id}`).text());
        let quantity = parseInt($(`#damage-quantity-${id}`).val());
        let subTotal = 0;

        if (quantity < 0) {
            $(`#damage-quantity-${id}`).val(0);
            $(`#damage-sub-total-${id}`).text('').text(subTotal);
        } else if (quantity >= stock) {
            subTotal = price * stock;
            $(`#damage-quantity-${id}`).val(0).val(stock);
            $(`#damage-sub-total-${id}`).text('').text(subTotal);
        } else if (quantity != 0) {
            subTotal = price * quantity;
            $(`#damage-sub-total-${id}`).text('').text(subTotal);
        } else {
            $(`#damage-sub-total-${id}`).text('').text(subTotal);
        }

        DamageDataArray.forEach(element => {
            if (element.id == id) {
                element.quantity = quantity >= stock ? stock : quantity;
                element.sub_total = subTotal;
            }
        });
        $('#jsonProducts').val(JSON.stringify(DamageDataArray));
        updateDamageGrandTotal();
    }

    function updateDamageGrandTotal() {
        let grandTotal = 0;
        const DamageProducts = JSON.parse($('#jsonProducts').val());
        DamageProducts.forEach(element => {
            grandTotal = grandTotal + parseFloat(element.sub_total);
        });
        $("#damageGrandTotal").text('').text(grandTotal);
        $("#damage_grand_total").val('').val(grandTotal);
    }

    // Damage & Replace - END

    // Exchange City - START

    function MakeExchangeCityTable() {
        let Table = $("#exchangeCityTable");
        if (Table.length > 0) {
            Table.DataTable().destroy();
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('exchange_city.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [
                    { data: 'date' },
                    { data: 'seller_id_name' },
                    { data: 'total_items' },
                    { data: 'grand_total' },
                    { data: 'action', orderable: false },
                ],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    function DeleteExchangeCity(itemId) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('exchange_city.delete') }}",
                    data: {
                        id: itemId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                '{{ __("messages.alerts.deleted") }}',
                                '{{ __("messages.alerts.item_deleted") }}',
                                'success'
                            ).then(() => {
                                $('#exchangeCityTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire(
                                '{{ __("messages.alerts.error") }}',
                                '{{ __("messages.alerts.delete_error") }}',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            '{{ __("messages.alerts.error") }}',
                            '{{ __("messages.alerts.delete_error") }}',
                            'error'
                        );
                    }
                });
            }
        });
    }

    function getProductsForExchangeCity() {
        let date = $("#date").val();
        let seller_id = $("#seller_id").val();
        let city_id = $("#from_city_id").val();
        if (date && seller_id && city_id) {
            $.ajax({
                type: "POST",
                url: "{{ route('exchange_city.seller.products') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    date: date,
                    seller_id: seller_id,
                    city_id: city_id
                },
                success: function (response) {
                    let products = JSON.parse(response);
                    let html = '';
                    products.forEach(element => {
                        element.sub_total = 0;
                        element.quantity = 0;
                        html += `
                        <tr>
                            <td>${element.name}</td>
                            <td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}<span id="exchange-city-price-${element.id}">${element.retail_price}</span></td>
                            <td class="text-primary">${element.total_stock}</td>
                            <td id="exchange-city-remaining-product-${element.id}">${element.remaining_product}</td>
                            <td id="exchange-city-boxes-${element.id}">${element.pieces}</td>
                            <td>{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }}<span id="exchange-city-sub-total-${element.id}">0</span></td>
                            <td><input type="number" class="form-control input-number-return" id="exchange-city-quantity-${element.id}" name="exchange_city_quantity" value="0" onkeyup="changeExchangeCity(${element.id})"></td>
                        </tr>`
                    });
                    $("#jsonProducts").val(JSON.stringify(products));
                    $("#sale_id").val(products[0].sale_id);
                    $('#exchange_city_products_table').html(html);
                },
                error: function () { }
            });
        }
    }

    function changeExchangeCity(id) {
        let remainingStock = $(`#exchange-city-remaining-product-${id}`).text() != '' ? parseInt($(`#exchange-city-remaining-product-${id}`).text()) : 0;
        let price = parseInt($(`#exchange-city-price-${id}`).text());
        let quantity = parseInt($(`#exchange-city-quantity-${id}`).val());
        let subTotal = 0;

        if (quantity < 0) {
            $(`#exchange-city-quantity-${id}`).val(0);
            $(`#exchange-city-sub-total-${id}`).text('').text(subTotal);
        } else if (quantity >= remainingStock) {
            subTotal = price * remainingStock;
            $(`#exchange-city-quantity-${id}`).val(0).val(remainingStock);
            $(`#exchange-city-sub-total-${id}`).text('').text(subTotal);
        } else if (quantity != 0) {
            subTotal = price * quantity;
            $(`#exchange-city-sub-total-${id}`).text('').text(subTotal);
        } else {
            $(`#exchange-city-sub-total-${id}`).text('').text(subTotal);
        }

        let ReturnProducts = JSON.parse($('#jsonProducts').val());
        ReturnProducts.forEach(element => {
            if (element.id == id) {
                element.quantity = quantity >= remainingStock ? remainingStock : quantity;
                element.sub_total = subTotal;
            }
        });
        $('#jsonProducts').val(JSON.stringify(ReturnProducts));
        updateExchangeCityGrandTotal();
    }

    function updateExchangeCityGrandTotal() {
        let ReturnProducts = JSON.parse($('#jsonProducts').val());
        const totalSubTotal = ReturnProducts.reduce((total, item) => total + item.sub_total, 0);
        $("#exchangeCityGrandTotal").text('').text(totalSubTotal);
        $("#exchange_city_grand_total").val('').val(totalSubTotal);
    }
    // Exchange City - END

    // Expenditure Giving - START
    function MakeExpenditureGivingTable() {
        let Table = $("#expenditureGivingTable");
        if (Table.length > 0) {
            Table.DataTable().destroy();
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "ajax": {
                    "url": "{{ route('expenditure.giving.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [
                    { data: 'id' },
                    { data: 'name' },
                    { data: 'action', orderable: false },
                ],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    function DeleteExpenditureGiving(itemId) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('expenditure.giving.delete') }}",
                    data: {
                        id: itemId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                '{{ __("messages.alerts.deleted") }}',
                                '{{ __("messages.alerts.item_deleted") }}',
                                'success'
                            ).then(() => {
                                $('#expenditureGivingTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire(
                                '{{ __("messages.alerts.error") }}',
                                '{{ __("messages.alerts.delete_error") }}',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            '{{ __("messages.alerts.error") }}',
                            '{{ __("messages.alerts.delete_error") }}',
                            'error'
                        );
                    }
                });
            }
        });
    }
    // Expenditure Giving - END

    // Expenditure Office - START
    function MakeExpenditureOfficeTable() {
        let Table = $("#expenditureOfficeTable");
        if (Table.length > 0) {
            Table.DataTable().destroy();
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('expenditure.office.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [{
                    data: 'id'
                },
                {
                    data: 'date'
                },
                {
                    data: 'expenditure_amount'
                },
                {
                    data: 'action',
                    orderable: false
                },
                ],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    function DeleteExpenditureOffice(itemId) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('expenditure.office.delete') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: itemId
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                '{{ __("messages.alerts.deleted") }}',
                                '{{ __("messages.alerts.item_deleted") }}',
                                'success'
                            ).then(() => {
                                $('#expenditureOfficeTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire(
                                '{{ __("messages.alerts.error") }}',
                                '{{ __("messages.alerts.delete_error") }}',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            '{{ __("messages.alerts.error") }}',
                            '{{ __("messages.alerts.delete_error") }}',
                            'error'
                        );
                    }
                });
            }
        });
    }
    // Expenditure Office - END

    // Expenditure Seller - START
    function MakeExpenditureSellerTable() {
        let Table = $("#expenditureSellerTable");
        if (Table.length > 0) {
            Table.DataTable().destroy();
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('expenditure.seller.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [{
                    data: 'id'
                },
                {
                    data: 'seller'
                },
                {
                    data: 'date'
                },
                {
                    data: 'expenditure_amount'
                },
                {
                    data: 'action',
                    orderable: false
                },
                ],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    function DeleteExpenditureSeller(itemId) {
        Swal.fire({
            title: '{{ __("messages.alerts.are_you_sure") }}',
            text: '{{ __("messages.alerts.cannot_revert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef3737',
            confirmButtonText: '{{ __("messages.alerts.yes_delete") }}',
            cancelButtonText: '{{ __("messages.alerts.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('expenditure.seller.delete') }}",
                    data: {
                        id: itemId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                '{{ __("messages.alerts.deleted") }}',
                                '{{ __("messages.alerts.item_deleted") }}',
                                'success'
                            ).then(() => {
                                $('#expenditureSellerTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire(
                                '{{ __("messages.alerts.error") }}',
                                '{{ __("messages.alerts.delete_error") }}',
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            '{{ __("messages.alerts.error") }}',
                            '{{ __("messages.alerts.delete_error") }}',
                            'error'
                        );
                    }
                });
            }
        });
    }

    function MakeExpenditureNameTable() {
        if ($("#expenditureSellerTable").length) {
            $("#expenditureSellerTable").DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('expenditure.giving.all') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val()
                    }
                },
                'columns': [
                    { data: 'date' },
                    { data: 'expenditure_name' },
                    { data: 'action', orderable: false },
                ],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    // Expenditure Seller - END

    // REPORT - START
    function reportFilter(){
        let startDate = $("#start_date").val();
        let endDate = $("#end_date").val();
        if (startDate != '' && endDate != '') {
            const routeUrl = "{{ route('report.filter', ['startDate' => ':startDate', 'endDate' => ':endDate']) }}";
            const url = routeUrl.replace(':startDate', encodeURIComponent(startDate)).replace(':endDate', encodeURIComponent(endDate));
            window.location.href = url;
        } else {
            const url = "{{ route('report')}}";
            window.location.href = url;
        }
    }
    // REPORT - END

    function MakeDemandTable() {
        let Table = $("#demandsTable");
        if (Table.length > 0) {
            Table.DataTable().destroy();
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, 200, 400],
                    ['50', '100', '200', '400']
                ],
                "language": {
                    "paginate": {
                        "next": "{{__('messages.paginate.next')}}",
                        "previous": "{{__('messages.paginate.previous')}}",
                    },
                    "info": "{{__('messages.paginate.info')}}",
                },
                "ajax": {
                    "url": "{{ route('demands.load') }}",
                    "type": "POST",
                    "data": {
                        "searchTerm": $("#search").val(),
                        "length": $("#no_of_entries").val(),
                        "start_date": $("#start_date").val(),
                        "end_date": $("#end_date").val()
                    }
                },
                'columns': [
                    {
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'date'
                    },
                    {
                        data: 'action',
                        orderable: false
                    },
                ],
                "drawCallback": function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "dom": "rtip"
            });
        }
    }

    function openDemandPrintModal(divId) {
        // Get the content of the specified div
        const printContents = document.getElementById(divId).innerHTML;

        // Create a new window
        const printWindow = window.open();

        // Write the content to the new window
        printWindow.document.write('<html><head><title>Print Demands</title>');
        printWindow.document.write(`<link rel="stylesheet" href="{{asset('public/assets/vendor/fonts/boxicons.css')}}" />
        <link rel="stylesheet" href="{{asset('public/assets/vendor/css/core.css')}}" class="template-customizer-core-css" />
        <link rel="stylesheet" href="{{asset('public/assets/vendor/css/theme-default.css')}}" class="template-customizer-theme-css" />
        <link rel="stylesheet" href="{{asset('public/assets/css/demo.css')}}" />

        <link rel="stylesheet" href="{{asset('public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />
        <link rel="stylesheet" href="{{asset('public/assets/vendor/libs/apex-charts/apex-charts.css')}}" />
        <link rel="stylesheet" href="{{asset('public/assets/vendor/libs/datatables/css/datatables.min.css')}}">

        <link rel="stylesheet" href="{{asset('public/assets/vendor/libs/select2-bt-5/css/select2.min.css')}}">
        <link rel="stylesheet" href="{{asset('public/assets/vendor/libs/select2-bt-5/css/select2-bootstrap-5-theme.min.css')}}">

        <link rel="stylesheet" href="{{asset('public/assets/vendor/libs/fontawesome/css/all.min.css')}}">
        <link rel="stylesheet" href="{{asset('public/assets/vendor/libs/fontawesome/css/fontawesome.min.css')}}">

        <link rel="stylesheet" href="{{asset('public/assets/vendors/libs/sweetalert2/sweetalert2.min.css')}}">

        <link rel="stylesheet" href="{{asset('public/assets/vendors/libs/toastr/toastr.min.css')}}">

        <link rel="stylesheet" href="{{asset('public/assets/css/custom.css')}}">

        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">
        `);
        printWindow.document.write('</head><body>');
        printWindow.document.write(printContents);
        printWindow.document.write('</body></html>');

        printWindow.document.close(); // Close the document
        printWindow.print(); // Trigger the print dialog
    }
</script>