@extends('layouts.app')

@section('title')
Dashboard
@endsection

@section('head-import')
<!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
<link href="plugins/apex/apexcharts.css" rel="stylesheet" type="text/css">
<link href="assets/css/dashboard/dash_2.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->
@endsection


@section('content')
<style>
    .widget-account-invoice-three .widget-amount .w-a-info {
        width: 100%;
    }

    .widget-account-invoice-three .widget-heading .wallet-usr-info span {
        padding: 7px 12px 5px 11px;
    }
    .wallet-balance h5{
        background: rgb(0 0 0 / 20%);
        padding: 13px 17px 7px 17px;
        border-radius: 10px;
    }
    .wallet-balance p{
        background: rgb(0 0 0 / 10%);
        padding: 13px 17px 7px 17px;
        border-radius: 10px;
    }
</style>
<div class="container my-3">
    <div class="row">
        <div class="col">
            <div class="widget widget-account-invoice-three">
                <div class="widget-heading">
                    <div class="wallet-usr-info">
                        <div class="usr-name ">
                            <h4>Investment</h4>
                            <span class=""> Total: {{ number_format($investments['total_investment']) }} - {{ 'Today: '. number_format( 
                            $investments['today_investment']) }} </span>
                        </div>
                        <div class="add">
                            <a href="{{route('investment.index')}}">
                                <span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="feather feather-plus">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg></span>
                            </a>
                        </div>
                    </div>
                    <div class="wallet-balance">
                        <p> Balance:</p>
                        <h5 class=""><span class="w-currency">Rs.</span> {{ number_format($calculations['balance']) }} </h5>
                    </div>
                    <div class="wallet-balance">
                        <p>Approved Profit:</p>
                        <h5 class="" ><span class="w-currency">Rs.</span> {{ number_format($calculations['approved_profit']) }}
                            <small style="font-size: 13px;"> Today: {{number_format($calculations['today']['approved_profit']) }} </small>
                        </h5>
                    </div>
                    <div class="wallet-balance">
                        <p>Pending Profit:</p>
                        <h5 class=""><span class="w-currency">Rs.</span> {{ number_format($calculations['pending_profit']) }}
                        <small style="font-size: 13px;"> Today: {{number_format($calculations['today']['pending_profit']) }} </small>
                    </h5>
                </div>
                <div class="wallet-balance">
                    <p>Current Investment:</p>
                        <h5 class=""><span class="w-currency">Rs.</span> {{ number_format($calculations['current_invested']['total'])  }}
                        <small style="font-size: 13px;"> Today: {{number_format($calculations['today']['current_invested']['total']) }} </small>
                        </h5>
                    </div>
                </div>

                <div class="widget-amount">
                    <div class="container-fluid">
                        <div class="row ">
                            <div class="col mb-2">
                                <div class="w-a-info  funds-received">
                                    <span>Contracts <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-chevron-up">
                                            <polyline points="18 15 12 9 6 15"></polyline>
                                        </svg></span>
                                    <a href="{{route('contracts.index')}}" target="_blank">
                                        <p style=" background: rgba(33, 150, 243, 0.169);
                                        color:#2196f3; font-size:20px;
                                        " > {{ sizeOf($report['total_contracts']) }} </p>
                                    </a>
                                    <small> Today: {{ sizeOf($dailyReport['total_contracts']) }} </small>
                                </div>
                            </div>
                            <div class="col mb-2">
                                <div class="w-a-info  funds-spent">
                                    <span> Pending <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-chevron-down">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg></span>
                                    <a href="{{route('contracts.index')}}" target="_blank">
                                        <p style="background: rgba(231, 81, 91, 0.151);
                                            color: rgb(231, 81, 90);font-size:20px;
                                        "> 
                                            {{ sizeOf($report['pending_status']) }} </p>
                                    </a>
                                    <small> Today: {{ sizeOf($dailyReport['pending_status']) }} </small>
                                </div>
                            </div>
                            <div class="col mb-2">
                                <div class="w-a-info funds-spent">
                                    <span style="white-space: nowrap;">Driver payments & charges Pending</span>
                                    <a href="{{route('contracts.index')}}" target="_blank">
                                        <p style="background: rgba(139, 94, 236, 0.151);
                                        color:rgb(139, 94, 236); font-size:20px;
                                        "> {{ sizeOf($report['pending_purchase']) }} </p>
                                    </a>
                                    <small> Today: {{ sizeOf($dailyReport['pending_purchase']) }} </small>
                                </div>
                            </div>
                            <div class="col mb-2">
                                <div class="w-a-info funds-spent">
                                    <span>Approved <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-chevron-down">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg></span>
                                    <a href="{{route('contracts.index')}}" target="_blank">
                                        <p style="
                                        background: rgba(0, 150, 135, 0.15);
                                        color:rgb(4, 199, 179);font-size:20px;" > {{ sizeOf($report['approved_status']) }} </p>
                                    </a>
                                    <small> Today: {{ sizeOf($dailyReport['approved_status']) }} </small>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="widget-content">

                    <div class="bills-stats">
                        <span>Expenses</span>
                    </div>

                    <div class="invoice-list">

                        <div class="inv-detail">
                            {{-- <div class="info-detail-1">
                                <p>Bike</p>
                                <p><span class="w-currency">$</span> <span class="bill-amount">13.85</span></p>
                            </div>
                            <div class="info-detail-2">
                                <p>Shop</p>
                                <p><span class="w-currency">$</span> <span class="bill-amount">15.66</span></p>
                            </div> --}}
                            <div class="info-detail-2">
                                <p>All Expenses: </p>
                                <p> <a href="{{route('expenses.index')}}"><span class="w-currency">Rs.</span> <span
                                            class="bill-amount">{{ number_format($calculations['expenses'])  }} </span> </a> </p>
                            </div>
                            <div class="info-detail-2">
                                <p>Today</p>
                                <p> <a href="{{route('expenses.index')}}"><span class="w-currency">Rs.</span> <span
                                    class="bill-amount">{{ number_format($calculations['today']['expenses']) }} </span> </a> </p>
                            </div>
                        </div>

                        <div class="inv-action">
                            <a href="javascript:void(0);" class="btn btn-outline-primary view-details">View Details</a>
                            <a href="javascript:void(0);" class="btn btn-outline-primary pay-now">Pay Now $29.51</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection


@section('footer-import')
<!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
<script src="plugins/apex/apexcharts.min.js"></script>
<script src="assets/js/dashboard/dash_2.js"></script>
<!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
@endsection