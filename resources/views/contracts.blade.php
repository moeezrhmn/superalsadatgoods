@extends('layouts.app')

@section('title')
New Contracts
@endsection

@section('head-import')
<link rel="stylesheet" type="text/css" href="plugins/table/datatable/datatables.css">
<link rel="stylesheet" type="text/css" href="plugins/table/datatable/custom_dt_html5.css">
<link rel="stylesheet" type="text/css" href="plugins/table/datatable/dt-global_style.css">
<link rel="stylesheet" type="text/css" href="plugins/table/datatable/custom_dt_custom.css">
<link rel="stylesheet" type="text/css" href="plugins/bootstrap-select/bootstrap-select.min.css">

<link href="plugins/sweetalerts/sweetalert2.min.css" rel="stylesheet" type="text/css" />
<link href="plugins/sweetalerts/sweetalert.css" rel="stylesheet" type="text/css" />
<link href="assets/css/components/custom-sweetalert.css" rel="stylesheet" type="text/css" />

{{-- select2 --}}
<link rel="stylesheet" type="text/css" href="plugins/select2/select2.min.css">

<link rel="stylesheet" type="text/css" href="assets/css/forms/switches.css">
<link href="assets/css/tables/table-basic.css" rel="stylesheet" type="text/css" />
<style>
    /* filters -- */
    #filters #date-filter-input {
        min-width: 10rem;
        padding: 2px;
        height: calc(0.7em + 1.4rem + 2px);
        width: 220px;

    }

    @media screen and (max-width:768px) {
        :is(#filters) :is(.status, .freightpaid) {
            max-width: 9rem;
        }
    }

    /* filters --------------- */

    #newContractDatatable :is(th, td) {
        padding: 0;
        font-size: 13px;
    }

    #newContractDatatable th {
        white-space: nowrap;
        padding: 10px 7px;
    }

    #newContractDatatable td {
        padding: 2px 5px;
        border: 1px solid #0000004b;
        /* text-align: center; */
    }

    #newContractDatatable td:is(.date, .vehicle_no) {
        white-space: nowrap;
        font-weight: 500;
        color: #ffffff;
    }

    #newContractDatatable td.company {
        font-size: 13px;
        font-weight: 600;
        color: #ffffff;
        white-space: nowrap;
    }

    #newContractDatatable td.remarks {
        font-size: 10px;
    }

    #newContractDatatable :is(input.form-control) {
        padding: 5px;
        min-width: 7rem;
    }

    :is(#newContractDatatable, #filters) :is(.company) {
        min-width: 10rem;
    }


    :is(#newContractDatatable, #filters) .bootstrap-select span.filter-option {
        white-space: nowrap;
        font-size: 12px;
    }

    :is(#newContractDatatable, #filters) .bootstrap-select>select.company {
        z-index: -1;
    }

    :is(#newContractDatatable, #filters) .bootstrap-select.btn-group>.dropdown-toggle {
        padding: 5px 0 3px 15px;
    }

    #newContractDatatable label.switch {
        margin-bottom: 0;
    }

    #newContractDatatable .switchTd {
        min-width: 6.5rem;
    }

    #newContractDatatable :is(select.custom_update_status, select.custom_update_purchase_status) {
        background: transparent;
        padding: 3px;
        color: white;
        border-radius: 4px;
    }

    /* status select */
    #newContractDatatable :is(select.custom_update_status:focus, select.custom_update_purchase_status:focus) {
        box-shadow: 1px 0px 23px #ff000070;
        border-color: red;
    }
    #newContractDatatable :is(.custom_update_status option, .custom_update_purchase_status option) {
        font-weight: 700;
        font-size: 14px;
        background: #060818;
    }

    #newContractDatatable .bootstrap-status-select {
        min-width: 6rem;
    }

    /* status select ---------------- */

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

 
</style>
@endsection


@section('content')



<div class="container-fluid  mt-3">
    <div class="row g-0 ">
        <div class="col-lg-12 col-12 ">
            <div class="statbox widget box box-shadow contract_page  ">
                <div class="widget-content widget-content-area p-0 justify-pill">

                    <ul class="nav nav-tabs mb-3 mt-1 justify-content-center" id="justifyCenterTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="justify-pills-home-tab" data-toggle="pill"
                                href="#justify-pills-home" role="tab" aria-controls="justify-pills-home"
                                aria-selected="true">Contracts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="justify-pills-profile-tab" data-toggle="pill"
                                href="#justify-pills-profile" role="tab" aria-controls="justify-pills-profile"
                                aria-selected="false">Add</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="justify-pills-tabContent">
                        <div class="tab-pane fade show active" id="justify-pills-home" role="tabpanel"
                            aria-labelledby="justify-pills-home-tab">
                            <div class=" container-fluid contract-datatable">
                                {{-- Filters --}}
                                <form id="filters"  >
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row  ">
                                                <div class="col-7 col-md-6 ">

                                                    <select id="company_filter" data-live-search="true"
                                                        class="selectpicker company" name="company_id">
                                                        <option value=""> Select Companies:</option>
                                                        @foreach ($companies as $item)
                                                        <option data-subtext="{{ $item['contact']  }}"
                                                            value="{{$item['id']}}">
                                                            {{ $item['name'] }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="row">
                                                        <div class="col-4">
                                                            <select name="month" class="selectpicker w-100 month" id="month">
                                                                <option value="" >All</option>
                                                                <option value="1" >Jan</option>
                                                                <option value="2" >Feb</option>
                                                                <option value="3" >March</option>
                                                                <option value="4" >April</option>
                                                                <option value="5" >May</option>
                                                                <option value="6" >June</option>
                                                                <option value="7" >July</option>
                                                                <option value="8" >Aug</option>
                                                                <option value="9" >Sep</option>
                                                                <option value="10" >Oct</option>
                                                                <option value="11" >Nov</option>
                                                                <option value="12" >Dec</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-5">
                                                            <select name="year" class="selectpicker w-100 year" id="year">
                                                            <script>
                                                                getLastFiveYears().forEach(e => {
                                                                    document.write('<option value="'+e+'" >'+e+'</option>');
                                                                });
                                                            </script>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <!-- <input type="text" name="date_filter" placeholder=" date filter"
                                                        id="date-filter-input" class="form-control"> -->
                                                </div>
                                                <div class="col-5 col-md-6 ">
                                                    <select id="status-filter" class="selectpicker status"
                                                        name="status">
                                                        <option value=""> Contract status:</option>
                                                        <option value="approved">Approved</option>
                                                        <option value="pending">Pending</option>
                                                    </select>
                                                    <select id="purchasestatus-filter" class="selectpicker purchase_status"
                                                        name="purchase_status">
                                                        <option value=""> Purchase Status:</option>
                                                        <option value="approved">Approved</option>
                                                        <option value="pending">Pending</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row mt-2 mt-md-0">
                                                <div class="col-md-2 ">
                                                    <div class=" d-flex d-md-block justify-content-end" >
                                                        <button type="submit" data-type="filter" class="btn btn-secondary">Filter</button>
                                                        <a href="{{  route('contracts.index') }}" data-type="reset" class="btn mt-md-2  btn-primary">Reset</a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </form>
                                {{-- TABLE --}}
                                <div class="row">
                                    <div class="col">
                                        <div class="table-responsive " >
                                            <table id="newContractDatatable" class="table mb-4">
                                                <thead>
                                                    <th>Company</th>
                                                    <th>Date</th>
                                                    <th>Vehicle No.</th>
                                                    <th>Vehicle Name</th>
                                                    <th>Bility</th>
                                                    <th>Quantity</th>
                                                    <th>Item</th>
                                                    <th>Freight</th>
                                                    <th>Purchase Status</th>
                                                    <th>Charge/Day</th>
                                                    <th>Stop Charge</th>
                                                    <th>Labour Charge</th>
                                                    <th>Sub Total</th>
                                                    <th>Total</th>
                                                    <th> Profit </th>
                                                    <th>Tax %</th>
                                                    <th>Remarks</th>
                                                    <th>Picture</th>
                                                    <th>Status</th>
                                                </thead>
                                                <tbody id="contract-table-tbody">
                                                    <tr id="loadingDiv">
                                                        <td colspan="19">
                                                            <div style="min-height:200px; display: flex;
                                                            align-items:center; justify-content: center; ">
                                                                <div
                                                                    class="spinner-border text-success align-self-center loader-lg">
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <td>
                                                        <select id="companySelect2" data-live-search="true"
                                                            class="selectpicker company" name="company">
                                                            <option value=""> Select Companies:</option>
                                                            @foreach ($companies as $item)
                                                            <option data-subtext="{{ $item['contact']  }}"
                                                                value="{{$item['id']}}"> {{ $item['name'] }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="hidden" name="update_contract_id">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="date" placeholder="Date" id="inputDate"
                                                            class="form-control">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="vehicle_no" placeholder="vehicle no"
                                                            class="form-control">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="vehicle_name"
                                                            placeholder="vehicle name" class="form-control">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="bility" disabled placeholder="bility"
                                                            class="form-control">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="quantity" placeholder="quantity"
                                                            class="form-control">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="item" placeholder="item"
                                                            class="form-control">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="freight" placeholder="freight"
                                                            class="form-control">
                                                    </td>
                                                    <td align="center">
                                                        <select id="purchase_status" name="purchase_status"
                                                                class="purchase_status selectpicker  w-100  "
                                                                data-style="custom-styling btn btn-outline-success  bootstrap-purchase-status-select">
                                                                <option selected class="text-success" value="approved">Approved</option>
                                                                <option class="text-danger" value="pending">Pending</option>
                                                            </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" placeholder="charge/day"
                                                            name="charge_per_day" class="form-control">
                                                    </td>
                                                    <td>
                                                        <input type="number" placeholder="stop charge"
                                                            name="stop_charges" class="form-control">
                                                    </td>
                                                    <td>
                                                        <input type="number" placeholder="labour charge"
                                                            name="labour_charges" class="form-control">
                                                    </td>
                                                    <th>
                                                        <span id="purchaseTotal"></span>
                                                        <input type="hidden" name="purchase_total">
                                                    </th>
                                                    <td>
                                                        <input type="number" placeholder="total" name="sale_total"
                                                            class="form-control">
                                                    </td>
                                                    <th>
                                                        <span id="liveCalProfit"></span>
                                                        <input type="hidden" name="profit">
                                                        </td>
                                                    <th class="d-flex align-items-center">
                                                        <input type="number" placeholder="tax %" name="tax_percent"
                                                            class="form-control">=
                                                        <span id="taxAmount">0</span>
                                                        <input type="hidden" name="tax_amount">
                                                    </th>
                                                    <td>
                                                        <input type="text" placeholder="remarks" name="remarks"
                                                            class="form-control">
                                                    </td>
                                                    <td>
                                                        <input type="file" id="contractImage" placeholder="image"
                                                            name="img" class="form-control">
                                                        <input type="hidden" name="old_img">
                                                        <span class="text-danger" id="oldImage"></span>
                                                    </td>
                                                    <td align="center" class="switchTd">
                                                        <div style="
                                                        display: flex;
                                                        gap: 10px;
                                                        align-items: center;
                                                        justify-content: center;">
                                                            <select id="statusEdit" name="status"
                                                                class="status selectpicker  w-100  "
                                                                data-style="custom-styling btn btn-outline-success  bootstrap-status-select">
                                                                <option selected class="text-success" value="approved">Approved</option>
                                                                <option class="text-danger" value="pending">Pending
                                                                </option>
                                                            </select>
                                                            <button class="btn btn-primary m-0 "
                                                                id="add-contract-btn">Save</button>
                                                        </div>
                                                    </td>

                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="tab-pane fade" id="justify-pills-profile" role="tabpanel"
                            aria-labelledby="justify-pills-profile-tab">
                            <h3>wokring on it</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- REPORT FOR THE FILTERED DATE --}}
    <div class="row my-4 ">
        <div class="col">
            <div class="card mb-3 component-card_1">
                <div class="card-body" id="report-cardbody" >
                   
                </div>
            </div>
        </div>
    </div>
    <div class="row my-4 ">
        <div class="col">
            <div class="card mb-3 component-card_2">
                <div class="card-body" id="dailyreport-cardbody" >
                   
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
<script>
    var assetUrl = @json(asset('storage'));
    var defContracts = {!!  json_encode( $contracts ) !!};
    var defbility = {!!  json_encode( $bility ) !!};
    const defReport = {!!  json_encode( $report ) !!};
    const defReportDaily = {!!  json_encode( $reportDaily ) !!};
    const addURL = '{{ route("api.contract.add") }}'                                                            
    
    
</script>
@section('footer-import')
<script src="plugins/table/datatable/datatables.js"></script>
<!-- NOTE TO Use Copy CSV Excel PDF Print Options You Must Include These Files  -->
<script src="plugins/table/datatable/button-ext/dataTables.buttons.min.js"></script>
<script src="plugins/table/datatable/button-ext/jszip.min.js"></script>
<script src="plugins/table/datatable/button-ext/buttons.html5.min.js"></script>
<script src="plugins/table/datatable/button-ext/buttons.print.min.js"></script>

{{-- select 2 --}}
<script src="plugins/select2/select2.min.js"></script>
<script src="plugins/select2/custom-select2.js"></script>
{{-- sweetalert2 --}}
<script src="plugins/sweetalerts/sweetalert2.min.js"></script>
<script src="plugins/sweetalerts/custom-sweetalert.js"></script>

<script src="plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="assets/js/contracts.js?v=1.0.2"></script>
@endsection