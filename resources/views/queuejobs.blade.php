@extends('layouts.app')

@section('title')
Queue Jobs
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

<link rel="stylesheet" type="text/css" href="plugins/select2/select2.min.css">

<link rel="stylesheet" type="text/css" href="assets/css/forms/switches.css">
<link href="assets/css/tables/table-basic.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<style>
    table td{
        font-size: 15px !important;
    }
</style>
<div class="container-fluid  mt-3">
    <div class="row g-0 ">
        <div class="col-lg-12 col-12 ">
            <div class="statbox widget box box-shadow contract_page  ">
                <div class="widget-content widget-content-area p-0 justify-pill">

                    <ul class="nav nav-tabs mb-3 mt-1 justify-content-center" id="justifyCenterTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="justify-pills-home-tab" data-toggle="pill" href="#justify-pills-home" role="tab" aria-controls="justify-pills-home" aria-selected="true">Queue Jobs </a>
                        </li>                   
                    </ul>
                    <div class="tab-content" id="justify-pills-tabContent">
                        <div class="tab-pane fade show active" id="justify-pills-home" role="tabpanel" aria-labelledby="justify-pills-home-tab">
                            <div class=" container-fluid contract-datatable">
                                <div class="row">
                                    <div class="col">
                                        <div class="table-responsive ">
                                            <table id="queuejobs_table" class="table mb-4">
                                                <thead>
                                                    <th>id</th>
                                                    <th>Queue</th>
                                                    <th>payload</th>
                                                    <th>Attempts</th>
                                                    <th>Reserved at</th>
                                                    <th>Available at</th>
                                                    <th>Created at</th>
                                                </thead>
                                                <tbody >
                                                    @foreach($queuejobs as $k => $val)
                                                    @php $payload = unserialize(json_decode($val->payload)->data->command)->data[0]->data @endphp
                                                    <tr>
                                                        <td> {{ $val->id }} </td>
                                                        <td> {{ $val->queue }} </td>
                                                        <td>  Amount: ({{number_format($payload['amount'])}}), Action: ({{$payload['action']}}) , Purpose:({{$payload['purpose']}}) , User id:({{$payload['user_id']}})</td> 
                                                        <td> {{ $val->attempts }} </td>
                                                        <td> {{ $val->reserved_at }} </td>
                                                        <td>
                                                            {{ \Carbon\Carbon::now()->setTimestamp($val->available_at)->toDateString() }}
                                                        </td>
                                                        <td> {{\Carbon\Carbon::now()->setTimestamp($val->created_at)->toDateString() }} </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer-import')
<script src="plugins/table/datatable/datatables.js"></script>
<script src="plugins/table/datatable/button-ext/dataTables.buttons.min.js"></script>
<script src="plugins/table/datatable/button-ext/jszip.min.js"></script>
<script src="plugins/table/datatable/button-ext/buttons.html5.min.js"></script>
<script src="plugins/table/datatable/button-ext/buttons.print.min.js"></script>

{{-- sweetalert2 --}}
<script src="plugins/sweetalerts/sweetalert2.min.js"></script>
<script src="plugins/sweetalerts/custom-sweetalert.js"></script>

<script src="plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function(){
         $('#queuejobs_table').DataTable({
            "ordering": false
         });
    })
</script>
@endsection