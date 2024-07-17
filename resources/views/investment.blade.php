@extends('layouts.app')

@section('title')
Investment
@endsection


@section('head-import')

<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" type="text/css" href="plugins/table/datatable/datatables.css">
<link rel="stylesheet" type="text/css" href="plugins/table/datatable/custom_dt_html5.css">
<link rel="stylesheet" type="text/css" href="assets/css/forms/theme-checkbox-radio.css">
<link rel="stylesheet" type="text/css" href="plugins/table/datatable/dt-global_style.css">
<!-- END PAGE LEVEL CUSTOM STYLES -->

<link rel="stylesheet" type="text/css" href="assets/css/forms/switches.css">
<link href="assets/css/tables/table-basic.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="plugins/select2/select2.min.css">

<link href="plugins/sweetalerts/sweetalert2.min.css" rel="stylesheet" type="text/css" />
<link href="plugins/sweetalerts/sweetalert.css" rel="stylesheet" type="text/css" />
<link href="assets/css/components/custom-sweetalert.css" rel="stylesheet" type="text/css" />


@endsection




@section('content')
<style>
    .form-control {
        background: transparent;
    }
</style>
<div class="container-fluid investment_page  mt-3">
    <div class="row">
        <div class="col-lg-12 col-12 ">
            <div class="statbox widget box box-shadow contract_page  ">
                <div class="widget-content widget-content-area px-3 pb-3 justify-pill">

                    <ul class="nav nav-tabs mb-3 mt-1 justify-content-center" id="justifyCenterTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="justify-pills-home-tab" data-toggle="pill"
                                href="#justify-pills-home" role="tab" aria-controls="justify-pills-home"
                                aria-selected="true">View</a>
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
                            {{-- table start here --}}
                            <div class="row justify-content-end">
                                <div class="col">
                                    <div class="filter-form-cotracts">
                                        <form action="{{ route('investment.index') }}" method="get">
                                            <div class="row ">
                                                <div class="col">
                                                    <input type="text" name="filter-date-investment"
                                                        class="form-control" placeholder="Filter by date"
                                                        id="investment-filters-by-date">
                                                </div>
                                                <div class="col">
                                                    <button type="submit" class="btn btn-secondary"> Filter
                                                    </button>
                                                    <a href="{{route('investment.index')}}"
                                                        class="btn btn-warning">Reset</a>
                                                </div>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex justify-content-end ">
                                        <button class="btn btn-sm btn-outline-secondary mb-2" id="editInvestment"> Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger mb-2" id="deleteInvestment">delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="cancel-row">

                                <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                                    <div class="widget-content widget-content-area br-6">
                                        <table id="html5-extension" class="table" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th class="checkbox-column dt-no-sorting"> Record Id </th>
                                                    <th>Name</th>
                                                    <th>Amount</th>
                                                    <th>Date</th>
                                                    <th>Details</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($investments as $key=>$value)
                                                <tr data-id="{{$value['id']}}" class="inner-row">
                                                    <td class="checkbox-column"> {{$value['id'] }} </td>
                                                    <td> {{ $value['name'] }} </td>
                                                    <td> {{ $value['amount'] }} </td>
                                                    <td> {{ Carbon\Carbon::parse($value['date'])->translatedFormat('d, F, Y') }} </td>
                                                    <td> {{ $value['detail'] }} </td>
                                                </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="justify-pills-profile" role="tabpanel"
                            aria-labelledby="justify-pills-profile-tab">

                            <div class="row justify-content-center">
                                <div class="col-md-8">

                                    <form id="investmentForm">
                                        @csrf
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group mb-4">
                                                    <label for="name">Name </label>
                                                    <input type="text" class="form-control" id="name" name='name'>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group mb-4">
                                                    <label for="amount">Amount </label>
                                                    <input type="number" class="form-control" name='amount' id="amount">
                                                    <span class="text-danger error"> </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-4">
                                            <label for="investmentDate">Date </label>
                                            <input id="investmentDate" name='date'
                                                class="form-control flatpickr flatpickr-input active" type="text"
                                                placeholder="Select Date..">
                                        </div>


                                        <div class="form-group mb-4">
                                            <label for="detail">Detail </label>
                                            <textarea class="form-control" id="detail" name='detail'
                                                rows="3"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary mt-3">Submit</button>
                                    </form>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- modal --}}

<div class="modal fade" id="investmentEditModal" tabindex="-1" role="dialog" aria-labelledby="investmentEditModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="investmentEditModalLabel"> Edit Investment </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-x">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form id="investmentFormEdit">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col">
                            <div class="form-group mb-4">
                                <label for="name">Name </label>
                                <input type="text" class="form-control" id="name" name='name'>
                                <input type="hidden"  id="investmentid" name='investmentid'>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group mb-4">
                                <label for="amount">Amount </label>
                                <input type="number" class="form-control" name='amount' id="amount">
                                <span class="text-danger error"> </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="investmentDateEdit">Date </label>
                        <input id="investmentDateEdit" name='date' class="form-control flatpickr flatpickr-input active"
                            type="text" placeholder="Select Date..">
                    </div>

                    <div class="form-group mb-4">
                        <label for="detail">Detail </label>
                        <textarea class="form-control" id="detail" name='detail' rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Discard</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection



@section('footer-import')

<!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
<script src="plugins/table/datatable/datatables.js"></script>
<!-- NOTE TO Use Copy CSV Excel PDF Print Options You Must Include These Files  -->
<script src="plugins/table/datatable/button-ext/dataTables.buttons.min.js"></script>
<script src="plugins/table/datatable/button-ext/jszip.min.js"></script>
<script src="plugins/table/datatable/button-ext/buttons.html5.min.js"></script>
<script src="plugins/table/datatable/button-ext/buttons.print.min.js"></script>

<script src="plugins/select2/select2.min.js"></script>
<script src="plugins/select2/custom-select2.js"></script>

<script src="plugins/sweetalerts/sweetalert2.min.js"></script>
<script src="plugins/sweetalerts/custom-sweetalert.js"></script>

<script>
    $(document).ready(function () {


      investmentTable =  $('#html5-extension').DataTable( {
            headerCallback:function(e, a, t, n, s) {
                e.getElementsByTagName("th")[0].innerHTML='<label class="new-control new-checkbox checkbox-outline-info m-auto">\n<input type="checkbox" class="new-control-input chk-parent select-customers-info" id="customer-all-info">\n<span class="new-control-indicator"></span><span style="visibility:hidden">c</span>\n</label>'
            },
            columnDefs:[ {
                targets:0, width:"30px", className:"", orderable:!1, render:function(e, a, t, n) {
                    return'<label class="new-control new-checkbox checkbox-outline-info  m-auto">\n<input type="checkbox" class="new-control-input child-chk select-customers-info" id="customer-all-info">\n<span class="new-control-indicator"></span><span style="visibility:hidden">c</span>\n</label>'
                }
            }],
            "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'B><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
        "<'table-responsive'tr>" +
        "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
            buttons: {
                buttons: [
                    { extend: 'copy', className: 'btn btn-sm' },
                    { extend: 'csv', className: 'btn btn-sm' },
                    { extend: 'excel', className: 'btn btn-sm' },
                    { extend: 'print', className: 'btn btn-sm' }
                ]
            },
            "oLanguage": {
                "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                "sInfo": "Showing page _PAGE_ of _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Search...",
               "sLengthMenu": "Results :  _MENU_",
            },
            "stripeClasses": [],
            "lengthMenu": [7, 10, 20, 50],
            "pageLength": 7 
        } );
        multiCheck(investmentTable);
        const toast = swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        padding: '2em'
                    });

        var fl = flatpickr($('.investment_page #investmentDate'));    
               // filter date pivker
       var investmentFiltersByDate = flatpickr(document.getElementById('investment-filters-by-date'), {
    mode: "range"
});
        
     
        //  AJAX REQ GOES HERE
        // adding
        $("#investmentForm").on('submit', function (e) {
            e.preventDefault();
            let form = $(this);
            $.ajax({
                url: "/investments/add",
                type: "post",
                data: $(this).serialize(),
                dataType: "json",
                success: function (res) {
                    console.log(res);
                    if(res.status){
                        toast({
                        type: `${res.alertType}`,
                        title: `${res.msg}`,
                        padding: '2em',
                    })
                    form[0].reset()

                    }
                },
                error:(err)=>{
                    console.log(err);
                    $("#amount").next().html(err.responseJSON.message)

                    setTimeout(() => {
                        $("#amount").next().html('');
                    }, 1500);
                }
            });
        });

        // deleting
        $('#deleteInvestment').on('click', function () {
            let delRecords = [];
            let delRows = $("input:checkbox:checked").parents('tr.inner-row');
            delRows.each( function (index,ele) { 
                delRecords.push(ele.dataset.id);
            });
            

            if ( delRecords == undefined || delRecords.length  < 1 ) {
                swal({
                            title: 'No row selected.',
                            padding: '2em'
                        });
             } else {
                swal({
                      title: 'Are you sure?',
                      text: 'You wont be able to revert this!',
                      type: 'warning',
                      showCancelButton: true,
                      confirmButtonText: 'Delete',
                      padding: '2em'
                    }).then(function(result) {
                        if(result.value){

                            $.ajax({
                                url: "{{ route('investment.delete') }}",
                                type: "post",
                                headers:{
                                    'X-CSRF-TOKEN':$("meta[name=csrf-token]").attr('content')
                                },
                                data: {delRecords},
                                dataType: "json",
                                success: function (res) {
                                    console.log(res)     
                                    if(res.status){
                                        toast({
                                            type: `${res.alertType}`,
                                            title: `${res.msg}`,
                                            padding: '2em',
                                        })
                                        delRows.remove();
                                    }
                                }
                            });
                        }
                        
                    })
                       
            }
        })


        // Edit Investment
        $("#editInvestment").on('click', ()=>{
            let editRedcords = [];
            let editRows = $("input:checkbox:checked").parents('tr.inner-row');
            editRows.each( function (index,ele) { 
                editRedcords.push(ele.dataset.id);
            });

            if(editRedcords == undefined || editRedcords.length != 1){
                swal({
                    title: 'Please select only one row to edit.',
                    padding: '2em'
                });
            }else{
                // $('.nav-tabs a[href="#justify-pills-profile"]').tab('show')
                $('#investmentEditModal').modal('show');
                $.ajax({
                    url: "{{ route('investment.edit') }}",
                    type: "post",
                    headers: {
                        "X-CSRF-TOKEN":$('meta[name=csrf-token]').attr('content')
                    },
                    data: {id:editRedcords[0] },
                    dataType: "json",
                    success: function (res) {
                        if(res.status){
                            $('#investmentFormEdit input[name=name]').val(res.data.name);
                            $('#investmentFormEdit input[name=amount]').val(res.data.amount);
                            $('#investmentFormEdit input[name=date]').val(res.data.date);
                            $('#investmentFormEdit textarea[name=detail]').text(res.data.detail);
                            $('#investmentFormEdit input[name=investmentid]').val(res.data.id);
                        }
                    }
                });
            }
        })

        $('#investmentEditModal').on('shown.bs.modal', function () {
        var flatpickrInstance = flatpickr($('#investmentDateEdit')[0]); 
        $(".flatpickr-calendar").css('z-index','1050')
        });

        $('#investmentEditModal').on('hidden.bs.modal', function () {
            $('#investmentFormEdit')[0].reset()
            $('#investmentFormEdit textarea[name=detail]').text('   ');
        });

        $('#investmentFormEdit').on('submit', function (e) {
            e.preventDefault();

            const form = $(this)
            $.ajax({
                url: "{{ route('investment.update') }}",
                type: "post",
                data: $(this).serialize(),
                dataType: "json",
                success: function (res) {
                    console.log(res)
                    toast({
                            type: `${res.alertType}`,
                            title: `${res.msg}`,
                            padding: '2em',
                        })
                    form[0].reset()
                    $('#investmentEditModal').modal('hide');
                }
            });
        })
        
    });
</script>


@endsection