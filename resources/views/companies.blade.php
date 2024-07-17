@extends('layouts.app')

@section('title')
Companies
@endsection


@section('head-import')

<link rel="stylesheet" type="text/css" href="plugins/table/datatable/dt-global_style.css">
<link rel="stylesheet" type="text/css" href="assets/css/forms/switches.css">
@endsection




@section('content')


<div class="container mt-3">
    <div class="row">

        <div id="tabsVertical" class="col-lg-12 col-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4>Manage Companies</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area vertical-pill">
                    <div class="row mb-4 mt-3">
                        <div class="col-sm-2 col-12">
                            <div class="nav flex-column nav-pills mb-sm-0 mb-3" id="rounded-vertical-pills-tab" role="tablist" aria-orientation="vertical">
                                <a class="nav-link active mb-2  mx-auto" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true"> List </a>
                                <a class="nav-link mb-2  mx-auto" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">Add</a>
                            </div>
                        </div>

                        <div class="col-sm-9 col-12">
                            <div class="tab-content" id="v-pills-tabContent">
                                <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                                    <style>
                                        .table-responsive::-webkit-scrollbar {
                                            height: 2px;
                                        }

                                        .table-responsive::-webkit-scrollbar-track {
                                            height: 2px;
                                            background: transparent;
                                        }

                                        .table-responsive::-webkit-scrollbar-thumb {
                                            height: 2px;
                                            background: #009688;
                                        }

                                        .pagination-contracts .page-item .page-link {
                                            cursor: auto;
                                            background-color: #fff0;
                                            border-color: #515558;
                                            cursor: pointer;
                                        }

                                        .pagination-contracts .page-item .page-link {
                                            color: #fff;
                                        }

                                        .pagination-contracts .page-item.active .page-link {
                                            background: #009688;
                                            cursor: none;
                                        }
                                    </style>
                                    <livewire:company.view />
                                </div>
                                <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">

                                    <livewire:company.add />
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('footer-import')

@endsection

@endsection