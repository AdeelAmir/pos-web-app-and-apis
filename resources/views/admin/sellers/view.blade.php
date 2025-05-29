@extends('admin.layouts.app')
@section('content')
    <style>
        .rounded_circle{
            height: 150px !important;
            width: 150px !important;
            border-radius: 50% !important;
        }
    </style>
    <div class="content container-fluid">
        <div class="row">
            <div class="mb-3 d-flex justify-content-between">
                <h3 class="mb-0">{{__('messages.view_seller_title')}}</h3>
                <div>
                    <button type="button" class="btn btn-secondary px-4 py-2 ms-1 me-1" onclick="window.location.href='{{route('sellers')}}'">
                        <i class="fa fa-chevron-left me-1" aria-hidden="true"></i> {{__('messages.btns.back')}}
                    </button>
                    <button type="button" class="btn btn-primary px-4 py-2 ms-1" onclick="window.location.href='{{route('sellers.target.add',[$seller->id])}}'">
                        <i class="fa fa-plus me-1" aria-hidden="true"></i> {{ __('messages.btns.add_target') }}
                    </button>
                </div>
            </div>
            <div class="col-md-12 offset-md-0 grid-margin stretch-card">
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <h5 class="mb-3 mb-md-0">{{__('messages.general_detail')}}</h5>
                                </div>
                            </div>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-3">
                                <img src="{{ $seller->profile_image }}" alt="Profile Image" class="rounded_circle object-fit-cover">
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="hidden" name="seller_id" id="seller_id" value="{{ $seller->id }}">
                                <div class="mb-1">{{__('messages.seller_id')}}: {{ $seller->id }}</div>
                                <div class="mb-1">{{__('messages.seller_name')}}: {{ $seller->name }}</div>
                                <div>{{__('messages.phone_number')}}: {{ $seller->phone }}</div>
                                <div class="mb-1">{{__('messages.email')}}: {{ $seller->email }}</div>
                                <div class="mb-1">{{__('messages.status')}}: @if($seller->status == 1) <span class="btn-sm btn-success cursor-pointer">Active</span> @else <span class="btn-sm btn-danger cursor-pointer">Ban</span> @endif</div>
                                <div>{{__('messages.description')}}:</div>
                                <p>{{ $seller->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <style>
                    .bg-white-transparent{
                        background: rgba(255, 255, 255, 0.384);
                        border-radius: 1rem;
                        padding: .8rem .8rem;
                    }
                    
                    .rounded-custom{
                        border-radius: 1rem !important;
                    }
                </style>
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-purple rounded-custom cursor-pointer" onclick="makeTable('total_sale','all')">
                                <div class="card-body text-white d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bg-white-transparent fs-4 fas fa-shopping-cart"></i>                            
                                    </div>
                                    <div class="d-flex flex-column justify-content-center align-items-end">
                                        <span class="fs-4 fw-bold m-0">{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }} {{ $totalSale }}</span>
                                        <small class="text-end">{{__('messages.seller_view_card.total_sale')}}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success rounded-custom cursor-pointer" onclick="makeTable('total_expense')">
                                <div class="card-body text-white d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bg-white-transparent fs-4 fas fa-wallet"></i>                            
                                    </div>
                                    <div class="d-flex flex-column justify-content-center align-items-end">
                                        <span class="fs-4 fw-bold m-0">{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }} {{ $totalExpenses }}</span>
                                        <small class="text-end">{{__('messages.seller_view_card.total_expenditure')}}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary rounded-custom cursor-pointer" onclick="makeTable('total_items')">
                                <div class="card-body text-white d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bg-white-transparent fs-4 fas fa-box"></i>                            
                                    </div>
                                    <div class="d-flex flex-column justify-content-center align-items-end">
                                        <span class="fs-4 fw-bold m-0">{{ $totalGoods }}</span>
                                        <small class="text-end">{{__('messages.seller_view_card.total_good')}}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning rounded-custom cursor-pointer" onclick="makeTable('total_loan')">
                                <div class="card-body text-white d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bg-white-transparent fs-4 fas fa-dollar-sign"></i>                            
                                    </div>
                                    <div class="d-flex flex-column justify-content-center align-items-end">
                                        <span class="fs-4 fw-bold m-0">{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }} {{ $totalCreditLeft }}</span>
                                        <small class="text-end">{{__('messages.seller_view_card.total_credit_left')}}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-purple rounded-custom cursor-pointer" onclick="makeTable('total_sale','today')">
                                <div class="card-body text-white d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bg-white-transparent fs-4 fas fa-shopping-cart"></i>                            
                                    </div>
                                    <div class="d-flex flex-column justify-content-center align-items-end">
                                        <span class="fs-4 fw-bold m-0">{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }} {{ $todaySale }}</span>
                                        <small class="text-end">{{ __('messages.seller_view_card.today_sale') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success rounded-custom cursor-pointer" onclick="makeTable('total_bonus')">
                                <div class="card-body text-white d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bg-white-transparent fs-4 fas fa-shopping-cart"></i>                      
                                    </div>
                                    <div class="d-flex flex-column justify-content-center align-items-end">
                                        <span class="fs-4 fw-bold m-0">{{ App\Helpers\SiteHelper::settings()['Currency_Icon'] }} {{ $totalBonus }}</span>
                                        <small class="text-end">{{__('messages.seller_view_card.total_bonus')}}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3" id="sale-table-div" style="display: none">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="datatable1 table table-stripped w-100" id="sellerTotalSaleTable">
                                <thead>
                                    <tr>
                                        <td>#</td>
                                        <td>{{__('messages.table_elements.shop_name')}}</td>
                                        <td>{{__('messages.table_elements.cash')}}</td>
                                        <td>{{__('messages.table_elements.loan')}}</td>
                                        <td>{{__('messages.table_elements.status')}}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mb-3" id="expense-table-div" style="display: none">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="datatable1 table table-stripped w-100" id="sellerTotalExpenseTable">
                                <thead>
                                    <tr>
                                        <td>#</td>
                                        <td>{{__('messages.table_elements.seller_name')}}</td>
                                        <td>{{__('messages.table_elements.date')}}</td>
                                        <td>{{__('messages.table_elements.amount')}}</td>
                                        <td>{{__('messages.table_elements.action')}}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mb-3" id="items-table-div" style="display: none">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="datatable1 table table-stripped w-100" id="sellerTotalItemsTable">
                                <thead>
                                    <tr>
                                        <td>#</td>
                                        <td>{{__('messages.table_elements.product')}}</td>
                                        <td>{{__('messages.table_elements.quantity')}}</td>
                                        <td>{{__('messages.table_elements.boxes')}}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mb-3" id="loan-table-div" style="display: none">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="datatable1 table table-stripped w-100" id="sellerTotalLoanTable">
                                <thead>
                                    <tr>
                                        <td>#</td>
                                        <td>{{__('messages.table_elements.shop_name')}}</td>
                                        <td>{{__('messages.table_elements.date')}}</td>
                                        <td>{{__('messages.table_elements.loan')}}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mb-3" id="bonus-table-div" style="display: none">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="datatable1 table table-stripped w-100" id="sellerTotalBonusTable">
                                <thead>
                                    <tr>
                                        <td>#</td>
                                        <td>{{__('messages.table_elements.shop_name')}}</td>
                                        <td>{{__('messages.table_elements.date')}}</td>
                                        <td>{{__('messages.table_elements.bonus')}}</td>
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
@endsection