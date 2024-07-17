@extends('layouts.app')


@section('title')
Expense
@endsection


@section('head-import')
<link rel="stylesheet" type="text/css" href="plugins/table/datatable/datatables.css">
<link rel="stylesheet" type="text/css" href="plugins/table/datatable/custom_dt_html5.css">
<link rel="stylesheet" type="text/css" href="plugins/table/datatable/dt-global_style.css">
<link rel="stylesheet" type="text/css" href="plugins/table/datatable/custom_dt_custom.css">
<link href="assets/css/components/custom-modal.css" rel="stylesheet" type="text/css" />

<link href="plugins/sweetalerts/sweetalert2.min.css" rel="stylesheet" type="text/css" />
<link href="plugins/sweetalerts/sweetalert.css" rel="stylesheet" type="text/css" />
<link href="assets/css/components/custom-sweetalert.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="plugins/bootstrap-select/bootstrap-select.min.css">
<style>
    :is(#filters) .bootstrap-select span.filter-option {
        white-space: nowrap;
        font-size: 12px;
    }

    :is(#filters) .bootstrap-select>select.company {
        z-index: -1;
    }

    :is(#filters) .bootstrap-select.btn-group>.dropdown-toggle {
        padding: 5px 0 3px 15px;
    }

    .form-control {
        background: transparent;
    }

    .table>tbody>tr>td {
        font-size: 13px;
        border: 1px solid #0000004b;
        padding: 0;
        padding: 7px 13px 7px 13px;
        color: #888ea8;
        white-space: pre-wrap;

    }

    .table>thead>tr>th,
    .table>tbody>tr>th {
        border: 1px solid #0000004b;
        padding: 0;
        padding: 7px 13px 7px 13px;
        white-space: pre-wrap;
    }
</style>
@endsection


@section('content')

<div class="container-fluid mt-3  ">
    <div class="row">
        <div class="col-md-2 col-5 layout-spacing">
            <div class="statbox widget expense_page box box-shadow">
                <div class="widget-header mb-2">
                    <div class="row ">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4>Report</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content p-3 widget-content-area">
                    <div class="row ">
                        <div class="col ">
                            <div class="data">
                                <table class="table table-dark">
                                    <thead>
                                        <tr>
                                            <th colspan="100%"> Expenses </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th>Daily</th>
                                            <td>{{$daily['amount']}}</td>
                                        </tr>
                                        <tr>
                                            <th> <span> @if(empty($filters['filter']))
                                                    {{ Carbon\Carbon::now()->format('F') }}
                                                    @else
                                                        @if($filters['month'])
                                                            {{$filters['month'] . ' ' . $filters['year']}}
                                                        @else
                                                            All
                                                        @endif
                                                    @endif </span> </th>
                                            <td>{{ $expenses['amount'] }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-7 layout-spacing">
            <div class="statbox widget expense_page box box-shadow">
                <div class="widget-header mb-2">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4>Category</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content p-3 widget-content-area">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <button type="button" data-toggle="modal" data-target="#categoryModel" class="btn btn-primary btn-sm"> Add</button>
                            <button type="button" class="btn btn-warning btn-sm" id="editCategory"> Edit</button>
                            <!-- <button class="btn btn-danger btn-sm"  > Delete</button> -->
                        </div>
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <div class="data p-1 ">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox"></th>
                                            <th>Name</th>
                                            <th>Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($category as $val)
                                        <tr>
                                            <td> <input type="checkbox" value="{{ $val->id }}" name="select_category"> </td>
                                            <td>{{$val->name}}</td>
                                            <td>{{$val->detail}}</td>
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
        <div class="col-md-7 col-12 layout-spacing ">
            <div class="statbox widget expense_page box box-shadow">
                <div class="widget-header mb-2">
                    <div class="row ">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4>Manage Expense</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content p-3 widget-content-area expense_filters ">
                    <form id="filters" class="row" action="{{ route('expenses.index') }}">
                        <input type="hidden" name="filter" value="1" >
                        <div class="col-md-4">
                            <select name="month" class="selectpicker w-100 month" id="month">
                                <option value="">All</option>
                                <option value="1">Jan</option>
                                <option value="2">Feb</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">Aug</option>
                                <option value="9">Sep</option>
                                <option value="10">Oct</option>
                                <option value="11">Nov</option>
                                <option value="12">Dec</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="year" class="selectpicker w-100 year" id="year">
                                <script>
                                    getLastFiveYears().forEach(e => {
                                        document.write('<option value="' + e + '" >' + e + '</option>');
                                    });
                                </script>
                            </select>
                        </div>
                        <div class="col-md-4 ">
                            <div class=" d-flex mb-4 justify-content-end">
                                <a href="#"  onclick="event.preventDefault(); document.getElementById('filters').submit()" class="btn mt-md-2  btn-info">Filter</a>
                                <a href="{{  route('expenses.index') }}" data-type="reset" class="btn mt-md-2  btn-primary">Reset</a>
                            </div>
                        </div>
                    </form>
                    <div class="row ">
                        <div class="col-12">
                            <div class="data">
                                <table id="expenseTable" class="table ">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- EXPENSE MODAL -->
<div class="modal fade" id="expenseModal" tabindex="-1" role="dialog" aria-labelledby="expenseModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expenseModal"> Save Expense </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form id="expenseForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="expenseFormError"></div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group  d-flex flex-column">
                                <label for="category">Category </label>
                                <select name='expense_category_id' class="selectpicker" style="width: 100%;" id="category">
                                    @foreach($category as $val)
                                    <option value="{{ $val->id }}"> {{ $val->name }} </option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="expense_id" name='expense_id'>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col ">
                            <label for="date">Date </label>
                            <input type="text" class="form-control flatpickr flatpickr-input active" id="expense-date" name='date' rows="3">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col ">
                            <label for="amount">Amount </label>
                            <input type="number" class="form-control" id="amount" name='amount' rows="3">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col ">
                            <label for="description">Description </label>
                            <textarea class="form-control" id="description" name='description' rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- CATEGORY MODAL -->
<div class="modal fade" id="categoryModel" tabindex="-1" role="dialog" aria-labelledby="categoryModel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModel"> Save or Update Category </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form id="categoryform">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col">
                            <div class="form-group mb-4">
                                <label for="name">Name </label>
                                <input type="text" class="form-control" id="name" name='name'>
                                <input type="hidden" id="category_id" name='category_id'>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col mb-4">
                            <label for="detail">Detail </label>
                            <textarea class="form-control" id="detail" name='detail' rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection


{{-- Footer import --}}
@section('footer-import')

<!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
<script src="plugins/table/datatable/datatables.js"></script>
<!-- NOTE TO Use Copy CSV Excel PDF Print Options You Must Include These Files  -->
<script src="plugins/table/datatable/button-ext/dataTables.buttons.min.js"></script>
<script src="plugins/table/datatable/button-ext/jszip.min.js"></script>
<script src="plugins/table/datatable/button-ext/buttons.html5.min.js"></script>
<script src="plugins/table/datatable/button-ext/buttons.print.min.js"></script>

<script src="plugins/sweetalerts/sweetalert2.min.js"></script>
<script src="plugins/sweetalerts/custom-sweetalert.js"></script>
<script src="plugins/bootstrap-select/bootstrap-select.min.js"></script>


<script>
    const expense = {!!json_encode($expenses['expense']) !!};
    console.log('all expense here => ', expense)
    const Dateoptions = {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    };
    const toast = swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        padding: '2em'
    });
    $(document).ready(function() {
        $('.selectpicker').selectpicker();

        function confirmFunction(
            title = 'Are you sure?',
            text = 'You wont be able to revert this!',
            type = "warning",
            confirmButtonText = 'Delete') {
            return new Promise((resolve) => {
                swal({
                    title: title,
                    text: text,
                    type: type,
                    showCancelButton: true,
                    confirmButtonText: confirmButtonText,
                    padding: '2em'
                }).then(function(result) {
                    if (result.value) {
                        resolve(true);
                    } else {
                        resolve(false);
                    }
                });
            })
        }
        let selectedRow = null;
        var expenseDataTable = null;

        function settingExpenseTable(tableData = null) {
            if ($.fn.DataTable.isDataTable('#expenseTable')) {
                $('#expenseTable').DataTable().destroy();
            }

            expenseDataTable = $('#expenseTable').DataTable({
                'data': tableData ? tableData : expense,
                'columns': [{
                        "data": "Category",
                        className: '',
                        "render": function(data, type, row, meta) {

                            return `${row.category.name}`
                        }
                    },
                    {
                        "data": "Date",
                        className: '',
                        "render": function(data, type, row, meta) {
                            return `${new Date(row.date).toLocaleDateString('en-US', Dateoptions)}`
                        }
                    },
                    {
                        "data": "Description",
                        className: '',
                        "render": function(data, type, row, meta) {
                            return `${row.description}`
                        }
                    },
                    {
                        "data": "Amount",
                        "render": function(data, type, row, meta) {
                            return `${row.amount}`
                        }
                    },
                ],
                "createdRow": function(row, data, dataIndex) {
                    $(row).attr('data-id', data.id);
                    $(row).attr('data-index', dataIndex)
                },
                "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-10 d-flex justify-content-md-start justify-content-center'<'dt--length-menu mr-2 'l> B><'col-sm-12 col-md-2 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
                    "<'table-responsive'tr>" +
                    "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p> >",
                buttons: {
                    buttons: [{
                            extend: 'csv',
                            className: 'btn btn-sm'
                        },
                        {
                            extend: 'print',
                            className: 'btn btn-sm'
                        },
                        {
                            text: 'Delete',
                            className: 'btn btn-sm  ',
                            init: function(api, node, config) {
                                $(node).removeClass('dt-button');
                                $(node).addClass('btn btn-sm btn-danger ');
                            },
                            action: function() {
                                if (!selectedRow) return;
                                confirmFunction().then((confirmed) => {
                                    if (confirmed) {
                                        deleteCall(selectedRow);
                                    }
                                })
                            }
                        },
                        {
                            text: "Edit",
                            className: 'btn btn-sm ',
                            init: function(api, node, config) {
                                $(node).removeClass('dt-button');
                                $(node).addClass('btn btn-sm btn-warning ');
                            },
                            action: function() {
                                if (!selectedRow) return;
                                editCall(selectedRow);
                            }
                        },
                        {
                            text: "Add",
                            className: 'btn btn-sm ',
                            init: function(api, node, config) {
                                $(node).removeClass('dt-button');
                                $(node).addClass('btn btn-sm btn-success ');
                            },
                            action: function() {
                                $('#expenseModal').modal('show');
                            }
                        },
                    ]
                },
                "oLanguage": {
                    "oPaginate": {
                        "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                        "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                    },
                    "sInfo": "Showing page _PAGE_ of _PAGES_",
                    "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                    "sSearchPlaceholder": "Search...",
                    "sLengthMenu": " _MENU_",
                },
                "lengthMenu": [7, 13, 21, 51],
                "pageLength": 13,
                // order: [[4, 'asc']]
            });
        }
        (() => settingExpenseTable())();
        var expenseDateFp = null;
        expenseDateFp = flatpickr(document.getElementById('expense-date'));
        $('#expenseModal').on('shown.bs.modal', function() {
            $(".flatpickr-calendar").css('z-index', '1050')
        });
        $('#expenseModal').on('hidden.bs.modal', function() {
            // expenseDateFp?.destroy();
            $("#expenseForm")[0].reset();
            $('#expenseForm textarea[name=detail]').text('   ');
        });
        $('#categoryModel').on('hidden.bs.modal', function() {
            $("#categoryform")[0].reset();
            $('#categoryform textarea[name=detail]').text('   ');
        });
        $(document).on('click', '#expenseTable tr', function() {
            const tr = $(this);
            const rowId = tr.data('id');
            // console.log(selectedRow, rowId)
            if (selectedRow) {
                selectedRow.css("backgroundColor", "");
                if (selectedRow.data('id') == rowId) {
                    selectedRow = null;
                    return
                }
            }
            selectedRow = tr;
            selectedRow.css('backgroundColor', "#1c213aad")
        })

        // AJAX CALLS
        $(document).on('submit', '#expenseForm', function(e) {
            e.preventDefault();
            const form = $(this);
            $.ajax({
                url: "{{ route('expenses.add') }}",
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
                },
                data: form.serialize(),
                dataType: "json",
                success: function(res) {
                    console.log(res)
                    // $('#expenseModal').modal('hide')
                    form[0].reset()
                    toast({
                        type: `${res.alertType}`,
                        title: `${res.msg}`,
                        padding: '2em',
                    })
                    let index = form.find('input[name=expense_id]').data('index');
                    if (!index) {
                        expense.push(res?.data)
                    } else {
                        expense[index] = res?.data;
                    }
                    settingExpenseTable()
                },
                error: (err) => {
                    $(".expenseFormError").html(err.responseJSON.message)
                    setTimeout(() => {
                        $(".expenseFormError").next().html('');
                    }, 1500);
                }

            });
        });

        function editCall(row = null) {
            if (!row) return console.log('Required row not found');
            id = row.data('id');
            index = row.data('index');
            url = "{!!  route('expenses.edit', ['id'=>':id']) !!}".replace(':id', id);
            $.get(url, {}, function(res) {
                $('#expenseModal').modal('show');
                if (!res.status) {
                    return toast({
                        type: `${res?.alertType}`,
                        title: `${res?.msg}`,
                        padding: '2em',
                    })
                }
                form = $('#expenseModal #expenseForm')
                form.find('input[name=expense_id]').val(res.data.id);
                form.find('input[name=expense_id]').attr('data-index', index);
                select = form.find('select[name=expense_category_id]');
                $(select).val(res.data.category.id);
                // $('#category').selectpicker('refresh');
                $(select).selectpicker('refresh');
                expenseDateFp.setDate(res.data.date);
                form.find('input[name=amount]').val(res.data.amount);
                form.find('textarea[name=description]').val(res.data.description);
            })
        }

        function deleteCall(row = null) {
            if (!row) return console.log('Required row not found');
            id = row.data('id');
            url = "{!!  route('expenses.delete', ['id'=>':id']) !!}".replace(':id', id);
            $.ajax({
                url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
                },
                type: 'delete',
                success: function(res) {
                    console.log(res)
                    toast({
                        type: `${res.alertType}`,
                        title: `${res.msg}`,
                        padding: '2em',
                    })
                    return res?.status && $(row).remove();
                },
                error: (err) => {
                    err.msg && toast({
                        type: `${err.alertType || 'error'}`,
                        title: `${err.msg}`,
                        padding: '2em',
                    })
                }
            })
        }
        $("#categoryform").on('submit', function(e) {
            e.preventDefault();
            $('#categoryModel').modal('hide')
            const form = $(this);
            $.ajax({
                url: "{{ route('expenses.categoryAdd') }}",
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
                },
                data: $(this).serialize(),
                dataType: "json",
                success: function(res) {
                    toast({
                        type: `${res.alertType}`,
                        title: `${res.msg}`,
                        padding: '2em',
                    })
                    form[0].reset()

                },
                error: (err) => {
                    $("#categoryform #name").next().html(err.responseJSON.message)
                    setTimeout(() => {
                        $("#categoryform #name").next().html('');
                    }, 1500);
                }

            });

        })
        $(document).on('click', '#editCategory', function() {
            const input = $('input[name=select_category]:checked');
            const id = input.val();
            if (input.length > 1 || input.length == 0) {
                return toast({
                    type: `error`,
                    title: `Please select a single category!`,
                    padding: '2em',
                })
            }
            url = "{!! route('expenses.categoryEdit', ['id' => ':id' ] ) !!}".replace(':id', id)
            $.get(url, {}, function(data) {
                console.log(data)
                if (!data.status) {
                    return toast({
                        type: `error`,
                        title: `${data.msg}`,
                        padding: '2em',
                    })
                }
                $('#categoryModel').modal('show');
                $('#categoryModel').find('input[name=name]').val(data.data.name);
                $('#categoryModel').find('input[name=category_id]').val(data.data.id);
                $('#categoryModel').find('textarea').val(data.data.detail);
            });
        })
    });
</script>

@endsection