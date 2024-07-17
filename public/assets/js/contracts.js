const Dateoptions = { day: '2-digit', month: 'long', year: 'numeric' };
const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

let selectedRow = null;
const toast = swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2000,
    padding: '1px'
});

let loadingHTMLTr = `<tr id="loadingDiv">
<td colspan="19">
    <div style="min-height:200px; display: flex;
    aling-items:center; justify-content: center; ">
        <div
            class="spinner-border text-success align-self-center loader-lg">
        </div>
    </div>
</td>
</tr>`;

// CONTRACT CLASS
class Contracts {
    constructor() {
        this.profit = 0;
        this.freight = 0;
        this.labourCharges = 0;
        this.chargePerDay = 0;
        this.stopCharges = 0;
        this.purchaseTotal = 0;
        this.saleTotal = 0;
        this.taxPercent = 0;
        this.taxAmount = 0;
    }

    calculate() {
        this.purchaseTotal = this.freight + this.labourCharges + this.chargePerDay + this.stopCharges;
        this.taxAmount = (this.taxPercent / 100) * this.saleTotal;
        this.profit = this.saleTotal - this.purchaseTotal - this.taxAmount;
    }

    emptyFields(){
        this.profit = 0;
        this.freight = 0;
        this.labourCharges = 0;
        this.chargePerDay = 0;
        this.stopCharges = 0;
        this.purchaseTotal = 0;
        this.saleTotal = 0;
        this.taxPercent = 0;
        this.taxAmount = 0;
    }
}
// OBJECT OF CLASS CONTRACTS
let contracts = new Contracts();


function str_pad(num, str_len) {
    num = num.toString();
    var numberOfZerosToAdd = str_len - num.length;
    if (numberOfZerosToAdd > 0) {
        for (var i = 0; i < numberOfZerosToAdd; i++) {
            num = '0' + num;
        }
    }
    return num;
}

function datePicker(id, mode=false) {
    if(!document.getElementById(id)) return;
    para = {};
    if(mode) para['mode'] = "range";
    const Date = flatpickr(document.getElementById(id), para);
    return Date;
}
function select2Custom(id) {
    return document.getElementById(id).select2({ tags: true })
}

function liveCalculations(e) {
    const inputName = e.target.name;
    // console.log(e.target.value)
    const inputValue = parseFloat(e.target.value || 0);

    switch (inputName) {
        case 'freight':
            contracts.freight = inputValue;
            break;
        case 'charge_per_day':
            contracts.chargePerDay = inputValue;
            break;
        case 'stop_charges':
            contracts.stopCharges = inputValue;
            break;
        case 'labour_charges':
            contracts.labourCharges = inputValue;
            break;
        case 'sale_total':
            contracts.saleTotal = inputValue;
            break;
        case 'tax_percent':
            contracts.taxPercent = inputValue;
            break;
        default:
            break;
    }
    contracts.calculate();
    $('#purchaseTotal').html(contracts.purchaseTotal.toFixed(0));
    $('#taxAmount').html(contracts.taxAmount.toFixed(0));
    $('#liveCalProfit').html(contracts.profit.toFixed(0));
    // setting values to hidden fields
    $("input[name=purchase_total]").val(contracts.purchaseTotal);
    $("input[name=tax_amount]").val(contracts.taxAmount);
    $("input[name=profit]").val(contracts.profit);
}

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
        }).then(function (result) {
            if (result.value) {
                resolve(true);
            } else {
                resolve(false);
            }
        });
    })
}
function checkStatusColor(value) {
    if (!value) return;
    if (value == 'approved' || value == true) {
        return '#1abc9c';
    } else if (value == 'pending' || value == false) {
        return '#e7515a ';
    }
}
function statusColorUpdate(tag = null, value = null) {
    console.log(tag, value)
    if (tag == null || value == null) return
    tag.css({
        "color": checkStatusColor(value),
        "border-color": checkStatusColor(value),
        "box-shadow": `0px 0px 11px -2px ${checkStatusColor(value)} `
    })
}

function statusStyle(status) {
    if (status == 'approved') {
        return "style='color:#1abc9c; border-color:#1abc9c; box-shadow:0px 0px 11px -2px #1abc9c; '"
    } else if (status == 'pending') {
        return "style='color:#e7515a ; border-color:#e7515a ; box-shadow:0px 0px 11px -2px #e7515a ; '"
    }
}

$(document).ready(function () {
    $('#loadingDiv').hide()
    // FETCHING DATA
    function fetchData(filters, res) {
        $('#newContractDatatable tfoot').css('display', 'none')
        $('#newContractDatatable #contract-table-tbody').html(loadingHTMLTr)
        $('#loadingDiv').show()
        
        // check if data already available
        if(defContracts && !filters){
            res = {'report':defReport, 'contracts':defContracts}
            ReportUpdate(res, defReportDaily);
            settingDataTables(defContracts);
        }else{
            $.ajax({
                url: "/api/contract/get",
                type: "get",
                data: filters,
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name=csrf-token]').attr('content'),
                },
                success: function (res) {
                    console.log(res)
                    if (!res.status) return;
                    ReportUpdate(res);
                    settingDataTables(res.contracts);
                    defContracts = res.contracts;
                    defbility = res.bility;
                },
                error: function (err) {  
                }
            })
        }
        $('#newContractDatatable tfoot').css('display', '');
        $('#loadingDiv').hide();
        if(defbility?.length == 0){
            $('input[name=bility]').val(1);
        }else{
            let lastbility = parseInt(defbility[defbility?.length - 1]) + 1;
            console.log('last contract ', lastbility);
            // lastbility = parseInt(lastbility) + 1;
            $('input[name=bility]').val(lastbility);
        }
    }

    fetchData();
    // select2Custom('companySelect2')
    // datePicker('date-filter-input');
    datePicker('inputDate');
    $('input[name="freight"], input[name="charge_per_day"], input[name="stop_charges"], input[name="labour_charges"], input[name="sale_total"], input[name="tax_percent"]').on('input', liveCalculations);


    $(document).on('click', '#newContractDatatable tbody tr', function () {
        const tr = $(this);
        const rowId = tr.data('id');

        if (selectedRow) {
            selectedRow.css("backgroundColor", "");
            if (selectedRow.data('id') == rowId) {
                selectedRow = null;
                return
            }
        }
        selectedRow = tr;
        selectedRow.css('backgroundColor', "#1c213aad")
        // console.log(selectedRow, rowId)
    })

    $(document).on('change', "#newContractDatatable select.status", function () {
        const selectTag = $(this);
        let selectButton = $('button.bootstrap-status-select');
        selectButton.removeClass('btn-outline-success');
        selectButton.removeClass('btn-outline-danger');
        selectButton.removeClass('btn-outline-warning');

        if (selectTag.val() == 'approved') {
            selectButton.addClass('btn-outline-success');
        } else if (selectTag.val() == 'pending') {
            selectButton.addClass('btn-outline-danger');
        } else {
            selectButton.addClass('btn-outline-warning');

        }
    })
    $(document).on('change', "#newContractDatatable select.purchase_status", function () {
        const selectTag = $(this);
        let selectButton = $('button.bootstrap-purchase-status-select');
        selectButton.removeClass('btn-outline-success');
        selectButton.removeClass('btn-outline-danger');
        selectButton.removeClass('btn-outline-warning');

        if (selectTag.val() == 'approved') {
            selectButton.addClass('btn-outline-success');
        } else if (selectTag.val() == 'pending') {
            selectButton.addClass('btn-outline-danger');
        } else {
            selectButton.addClass('btn-outline-warning');

        }
    })
    $(document).on('change', "select.custom_update_status", function () {
        const selectTag = $(this);
        const value = selectTag.val();
        const id = selectTag.data('id');
        statusColorUpdate(selectTag, value);
        UpdateStatus(id, value);
        
    })
    $(document).on('change', "select.custom_update_purchase_status", function () {
        const selectTag = $(this);
        const value = selectTag.val();
        const id = selectTag.data('id')
        statusColorUpdate(selectTag, value);
        UpdatePurchaseStatus(id, value);
    })
    $(document).on('click', '#add-contract-btn', function () {
        const imgInput = $('#contractImage')[0];
        const selectedImage = imgInput.files[0];
        var formData = new FormData();
        formData.append('company_id', $('select[name=company]').val());
        formData.append('date', $('input[name=date]').val())
        formData.append('vehicle_number', $('input[name=vehicle_no]').val())
        formData.append('vehicle_name', $('input[name=vehicle_name]').val())
        formData.append('bility', $('input[name=bility]').val())
        formData.append('quantity', $('input[name=quantity]').val())
        formData.append('item', $('input[name=item]').val())
        formData.append('freight', $('input[name=freight]').val())
        formData.append('purchase_status', $('#newContractDatatable  select[name=purchase_status]').val())
        formData.append('charge_per_day', $('input[name=charge_per_day]').val() || 0 )
        formData.append('stop_charges', $('input[name=stop_charges]').val() || 0  )
        formData.append('labour_charges', $('input[name=labour_charges]').val() || 0 )
        formData.append('purchase_total', $('input[name=purchase_total]').val())
        formData.append('sale_total', $('input[name=sale_total]').val() || 0  )
        formData.append('profit', $('input[name=profit]').val() || 0  )
        formData.append('tax_percent', $('input[name=tax_percent]').val() || 0  )
        formData.append('tax_amount', $('input[name=tax_amount]').val() || 0  )
        formData.append('remarks', $('input[name=remarks]').val())
        formData.append('img', selectedImage)
        formData.append('status', $('#newContractDatatable select[name=status]').val())
        // for editing
        formData.append('update_contract_id', $('input[name=update_contract_id]').val())
        formData.append('old_img', $('input[name=old_img]').val())
        const rowIndex = $('input[name=update_contract_id]').data('index');
        console.log('this the row index ',rowIndex)
        addCall(formData, rowIndex, $('input[name=bility]').val())
    })


    // FILTERS 
    $(document).on('submit', '#filters', function (e) {
        e.preventDefault();
        const form = $(this);
        const data = {
           'company_id':form.find('select[name=company_id]').val(),
           'month':form.find('select[name=month]').val(),
           'year':form.find('select[name=year]').val(),
           'status':form.find('select[name=status]').val(),
           'purchase_status':form.find('select[name=purchase_status]').val(),
           'type':form.find('button[type=submit]:focus').data('type'),
        }
        fetchData(data, null)
    })

    













































    function addCall(formData, index, newbility = null) {
        if (!formData) return;
        var checkContractID = $('input[name=update_contract_id]').val()
        if (checkContractID) {
            $('#add-contract-btn').text('Updating');
        } else {
            $('#add-contract-btn').text('Saving');
        }
        $.ajax({
            url:addURL,
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
            },
            processData: false,
            contentType: false,
            data: formData,
            success: function (res) {
                $('#add-contract-btn').text('Save');
                console.log(res)
                toast({
                    type: res.status ? 'success' : 'error',
                    title: res?.msg,
                    padding: '1em',
                })
                if(!res.status) return;
                $('#newContractDatatable tfoot input').val('')
                $('#newContractDatatable tfoot #oldImage').html('')
                $('input[name=update_contract_id]').data('index', '')

                $('#purchaseTotal').html('');
                $('#taxAmount').html('');

                $('#liveCalProfit').html('');
                $('#newContractDatatable').DataTable().destroy()
                // 
                if(defContracts && index != undefined && index != null && index != ""){
                    defContracts[index] = res?.data;
                }else{
                    defContracts.unshift(res?.data);
                }
                if(newbility) defbility.push(newbility);
                fetchData()
                contracts.emptyFields()
                
            },
            error:(res)=>{
                toast({
                    type: 'error',
                    title: res?.responseJSON.message,
                    padding: '1em',
                })
            }
        });
    }

})



function deleteCall(id) {
    if (!id) return;

    $.ajax({
        url: `/api/contract/delete/${id}`,
        type: "DELETE",
        headers: {
            'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
        },
        dataType: "json",
        success: function (res) {
            // console.log(res)
            toast({
                type: res.status ? 'success' : 'error',
                title: res?.msg,
                padding: '2em',
            })
            if (res.status) {
                $(selectedRow).fadeOut();
            }
        }
    });
}
function editCall(id, index) {
    if (!id) return;

    $.ajax({
        type: "GET",
        url: "/api/contract/edit/" + id,
        dataType: "json",
        success: function (res) {
            console.log(res)
            if (res.status) {
                $('#companySelect2').selectpicker('val', res.contract?.company_id);
                $('input[name=update_contract_id]').val(res.contract?.id)
                $('input[name=update_contract_id]').attr('data-index',index);
                $('input[name=date]').val(res.contract?.date)
                $('input[name=vehicle_no]').val(res.contract?.vehicle_number)
                $('input[name=vehicle_name]').val(res.contract?.vehicle_name)
                $('input[name=bility]').val(res.contract?.bility)
                $('input[name=quantity]').val(res.contract?.quantity)
                $('input[name=item]').val(res.contract?.item)
                $('input[name=freight]').val(res.contract?.freight)

                $('#newContractDatatable select[name=purchase_status]').selectpicker('val', res.contract?.purchase_status);
                $('#newContractDatatable select[name=purchase_status]').trigger('change');

                $('input[name=charge_per_day]').val(res.contract?.charge_per_day)
                $('input[name=stop_charges]').val(res.contract?.stop_charges)
                $('input[name=labour_charges]').val(res.contract?.labour_charges)
                $('input[name=purchase_total]').val(res.contract?.purchase_total)
                $('input[name=sale_total]').val(res.contract?.sale_total)
                let profit = res.contract.sale_total - res.contract.purchase_total - res.contract.tax_amount;
                $('input[name=profit]').val(profit)
                $('input[name=tax_percent]').val(res.contract?.tax_percent)
                $('input[name=tax_amount]').val(res.contract?.tax_amount)
                $('input[name=remarks]').val(res.contract?.remarks)
                $('input[name=old_img]').val(res.contract?.img)
                $('#oldImage').html((!res.contract?.img) ? "! no old image" : res.contract?.img.substring(15, 23) + '...' + res.contract?.img.split('.').pop());
                $('#newContractDatatable select[name=status]').selectpicker('val', res.contract?.status);
                $('#newContractDatatable select[name=status]').trigger('change'); 
                $('#purchaseTotal').html(res.contract.purchase_total);
                $('#taxAmount').html(res.contract.tax_amount);

                $('#liveCalProfit').html(profit);
                $('#add-contract-btn').text('Update');

                // Contract object calculations
                contracts.freight = res.contract?.freight
                contracts.chargePerDay = res.contract?.charge_per_day
                contracts.stopCharges = res.contract?.stop_charges
                contracts.labourCharges = res.contract?.labour_charges
                contracts.purchaseTotal = res.contract?.pur
                contracts.saleTotal = res.contract?.sale_total
                contracts.taxPercent = res.contract?.tax_percent
                contracts.taxAmount = res.contract?.taxAmount
            }
        }
    });

}


function UpdateStatus(id, status) {
    // console.log(id, status)
    if (!status || !id) return;

    $.ajax({
        type: "POST",
        url: "/api/contract/status-update/" + id + '/' + status,
        headers: {
            'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
        },
        dataType: "json",
        success: function (res) {
            console.log(res)
            toast({
                type: res.status ? 'success' : 'error',
                title: res?.msg,
                padding: '2em',
            })
        }
    });
}

function UpdatePurchaseStatus(id, purchase_status) {
    if (!id) return;
    $.ajax({
        type: "POST",
        url: "/api/contract/purchase-status-update/" + id + '/' + purchase_status,
        headers: {
            'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
        },
        dataType: "json",
        success: function (res) {
            toast({
                type: res.status ? 'success' : 'error',
                title: res?.msg,
                padding: '2em',
            })
        },
        error:(err)=>{
            toast({
                type:'error',
                title: err?.responseJSON?.message,
                padding: '2em',
            })
        }
    });
}

function ReportUpdate(data, dailyData){
    if (!dailyData) dailyData = defReportDaily;
    console.log('here is report daily data => ',dailyData)
    // console.log('here is data => ',data)
    if(!data){
        $('#report-cardbody').html(" <h4> NO REPORT </h4> ")
        return;
    };
    let date = new Date();
/*
: \n Date: ${(data.filter_data && data.filter_data.date_filter) || ' <span class="text-danger"> none </span> ' } , Company: ${(data.filter_data && data.filter_data?.company?.name) || ' <span class="text-danger"> none </span> ' } , Freight: ${(data.filter_data && data.filter_data?.freightpaid_filter) || ' <span class="text-danger"> none </span> ' } , Status: ${(data.filter_data && data.filter_data?.status_filter) || ' <span class="text-danger"> none </span> ' }
*/  
    let reportHTML = `
    <div class='row mb-3'>
        <div class='col-6'>
         <h5 class="card-title">Report of ( ${ data?.filters ? (!data?.filters?.month ? ' All ' :  (data?.filters?.month + ' - ' + data?.filters?.year)) : date.toLocaleString('default', { month: 'long' })} ) </h5>
        </div>
        <div class='col-6'>
            <div class='row justify-content-end'>
                <form action='/generate-pdf' method='POST' >
                    <input type='hidden' name='_token' value='${csrfToken}'>
                    <input type='hidden' name='report' value='${JSON.stringify(data.report)}' >
                    <input type='hidden' name='contracts' value='${JSON.stringify(data.contracts)}' >
                    <input type='hidden' name='filters' value='${JSON.stringify(data.filters || 0 )}' >
                    <button type='submit' class='btn btn-primary' > Download PDF </button>
                </form>
            </div>
        </div>
    </div>
    <div class='row'>
        <div class='col-6 col-md-2'>
            <table class='table table-dark'>
                <thead>
                    <tr>
                        <th colspan='100%' class='text-center' > Contracts </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th> Pending </th>
                        <td> ${data.report?.pending_status?.length} </td>
                    </tr>
                    <tr>
                        <th> Approved </th>
                        <td> ${data.report?.approved_status?.length} </td>
                    </tr>
                    <tr>
                        <th> Total </th>
                        <td>  ${data?.contracts?.length} </td>
                    </tr>
                </tbody>
            </table>
        </div >
        <div class='col-6 col-md-2'>
            <table class='table table-dark'>
                <thead>
                    <tr>
                        <th colspan='100%' class='text-center' > Driver Payments </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th> Pending </th>
                        <td> ${data.report?.pending_purchase?.length} </td>
                    </tr>
                    <tr>
                        <th> Approved </th>
                        <td> ${data.report?.approved_purchase?.length} </td>
                    </tr>
                </tbody>
            </table>
        </div >
        <div class='col-12 col-md-4'>
            <table class='table table-dark'>
                <thead>
                    <tr>
                        <th colspan='100%' class='text-center' > Pending Contracts </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th> Tax Amount </th>
                        <td>${number_format(data.report?.calculations?.contracts?.pending?.tax_amount)} </td>
                    </tr>
                    <tr>
                        <th> Investment </th>
                        <td>${number_format(data.report?.calculations?.contracts?.pending?.approved_purchase?.total)} + Driver pay (${number_format(data.report?.calculations?.contracts?.pending?.pending_purchase?.total)}) <br> = ${number_format(data.report?.calculations?.contracts?.pending?.total_purchase)}  </td>
                    </tr>
                    <tr>
                        <th> Bill - Sale </th>
                        <td> ${number_format(data.report?.calculations?.contracts?.pending?.sale_total)} </td>
                    </tr>
                    <tr>
                        <th> Profit </th>
                        <td>${number_format(data.report?.calculations?.contracts?.pending?.profit)} </td>
                    </tr>
                </tbody>
            </table>
        </div >
        <div class='col-12 col-md-4'>
            <table class='table table-dark'>
                <thead>
                    <tr>
                        <th colspan='100%' class='text-center' > Approved Contracts </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th> Tax Amount </th>
                        <td>${number_format(data.report?.calculations?.contracts?.approved?.tax_amount)} </td>
                    </tr>
                    <tr>
                        <th> Investment </th>
                        <td>${number_format(data.report?.calculations?.contracts?.approved?.approved_purchase?.total)} + Pending (${number_format(data.report?.calculations?.contracts?.approved?.pending_purchase?.total)}) <br> = ${ number_format(data.report?.calculations?.contracts?.approved?.total_purchase)}  </td>
                    </tr>
                    <tr>
                        <th> Bill - Sale </th>
                        <td> ${ number_format(data.report?.calculations?.contracts?.approved?.sale_total)} </td>
                    </tr>
                    <tr>
                        <th> Profit </th>
                        <td>${number_format(data.report?.calculations?.contracts?.approved?.profit)} </td>
                    </tr>
                    <tr>
                        <th> Expenses </th>
                        <td>${number_format(data.report?.calculations?.expenses?.amount)} </td>
                    </tr>
                    <tr>
                        <th> Profit - Expenses </th>
                        <td>${number_format(data.report?.calculations?.contracts?.approved?.profit)} - ${number_format(data.report?.calculations?.expenses?.amount)} <br> = ${number_format(data.report?.calculations?.contracts?.approved?.profit - data.report?.calculations?.expenses?.amount)} </td>
                    </tr>
                </tbody>
            </table>
        </div >
    </div >
    

    `;
    const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        let dailyReport = `
        <div class='row mb-3'>
            <div class='col-6'>
                <h5 class="card-title">Report of ( ${daysOfWeek[date.getDay()]+' - '+date.getDate()+' '+date.toLocaleString('default', { month: 'long' })} ) </h5>
            </div>
            <div class='col-6'>
                <div class='row justify-content-end'>
                    <form action='/generate-pdf' method='POST' >
                        <input type='hidden' name='_token' value='${csrfToken}'>
                        <input type='hidden' name='daily' value='1'>
                        <input type='hidden' name='report' value='${JSON.stringify(dailyData)}' >
                        <button type='submit' class='btn btn-primary' > Download PDF </button>
                    </form>
                </div>
            </div>
        </div>
        <div class='row'>
        <div class='col-6 col-md-2'>
        <table class='table table-dark'>
            <thead>
                <tr>
                    <th colspan='100%' class='text-center' > Contracts </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th> Pending </th>
                    <td> ${dailyData?.pending_status?.length} </td>
                </tr>
                <tr>
                    <th> Approved </th>
                    <td> ${dailyData?.approved_status?.length} </td>
                </tr>
                <tr>
                    <th> Total </th>
                    <td>  ${dailyData?.pending_status?.length + dailyData?.approved_status?.length} </td>
                </tr>
            </tbody>
        </table>
    </div >
    <div class='col-6 col-md-2'>
        <table class='table table-dark'>
            <thead>
                <tr>
                    <th colspan='100%' class='text-center' > Driver Pay Pending  </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th> Pending </th>
                    <td> ${ (dailyData?.pending_purchase?.length)} </td>
                </tr>
                <tr>
                    <th> Approved </th>
                    <td> ${ (dailyData?.approved_purchase?.length)} </td>
                </tr>
            </tbody>
        </table>
    </div >
    <div class='col-12 col-md-4'>
        <table class='table table-dark'>
            <thead>
                <tr>
                    <th colspan='100%' class='text-center' > Pending Contracts </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th> Tax Amount </th>
                    <td>${number_format(dailyData?.calculations?.contracts?.pending?.tax_amount)} </td>
                </tr>
                <tr>
                    <th> Investment </th>
                    <td>${ number_format(dailyData?.calculations?.contracts?.pending?.approved_purchase?.total)} + Driver pay (${ number_format(dailyData?.calculations?.contracts?.pending?.pending_purchase?.total)}) <br> = ${ number_format(dailyData?.calculations?.contracts?.pending?.total_purchase)}  </td>
                </tr>
                <tr>
                    <th> Bill - Sale </th>
                    <td> ${ number_format(dailyData?.calculations?.contracts?.pending?.sale_total)} </td>
                </tr>
                <tr>
                    <th> Profit </th>
                    <td>${ number_format(dailyData?.calculations?.contracts?.pending?.profit)} </td>
                </tr>
            </tbody>
        </table>
    </div >
    <div class='col-12 col-md-4'>
        <table class='table table-dark'>
            <thead>
                <tr>
                    <th colspan='100%' class='text-center' > Approved Contracts </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th> Tax Amount </th>
                    <td>${ number_format(dailyData?.calculations?.contracts?.approved?.tax_amount)} </td>
                </tr>
                <tr>
                    <th> Investment </th>
                    <td>${ number_format(dailyData?.calculations?.contracts?.approved?.approved_purchase?.total)} + Pending (${ number_format(dailyData?.calculations?.contracts?.approved?.pending_purchase?.total)}) <br> = ${ number_format(dailyData?.calculations?.contracts?.approved?.total_purchase)}  </td>
                </tr>
                <tr>
                    <th> Bill - Sale </th>
                    <td> ${ number_format(dailyData?.calculations?.contracts?.approved?.sale_total) } </td>
                </tr>
                <tr>
                    <th> Profit </th>
                    <td>${ number_format(dailyData?.calculations?.contracts?.approved?.profit)} </td>
                </tr>
            </tbody>
        </table>
        </div >
        </div >
    
        `;
    $('#dailyreport-cardbody').html(dailyReport)
    $('#report-cardbody').html(reportHTML)

}

function settingDataTables(data=Array[Object]){
    if ($.fn.DataTable.isDataTable('#newContractDatatable')) {
        $('#newContractDatatable').DataTable().destroy();
    }
    var ContractTable = $('#newContractDatatable').DataTable({
        'data': data,
        'columns': [
            {
                "data": "Company",
                className: 'company',
                "render": function (data, type, row, meta) {

                    return `${row.company.name || ''}`
                }
            },
            {
                "data": "Date",
                className: 'date',
                "render": function (data, type, row, meta) {
                    return `${new Date(row.date).toLocaleDateString('en-US', Dateoptions)}`
                }
            },
            {
                "data": "Vehicle No.",
                className: 'vehicle_no',
                "render": function (data, type, row, meta) {
                    return `${row.vehicle_number || ''}`
                }
            },
            {
                "data": "Vehicle Name",
                "render": function (data, type, row, meta) {
                    return `${row.vehicle_name || ''}`
                }
            },
            {
                "data": "Bility",
                "render": function (data, type, row, meta) {
                    return `${str_pad(row.bility, 5)}`
                }
            },
            {
                "data": "Quantity",
                "render": function (data, type, row, meta) {
                    return `${row.quantity || ''}`
                }
            },
            {
                "data": "Item",
                "render": function (data, type, row, meta) {
                    return `${row.item || ''}`
                }
            },
            {
                "data": "Freight",
                "render": function (data, type, row, meta) {
                    return `<span style='color:#fff;' >${number_format(row.freight)}</span>`
                }
            },
            {
                "data": "Purchase Status",
                className: '',
                "render": function (data, type, row, meta) {
                    return `<select data-id="${row.id}" name="update_purchase_status" ${statusStyle(row.purchase_status)} class="custom_update_purchase_status  w-100" >
                    <option ${row.purchase_status == 'approved' && 'selected'} class="text-success"  value="approved" >Approved</option>
                    <option ${row.purchase_status == 'pending' && 'selected'} class="text-danger"  value="pending" >Pending</option>
                    </select>`
                }
            },
            {
                "data": "Charge/Day",
                "render": function (data, type, row, meta) {
                    return `${number_format(row.charge_per_day)}`
                }
            },
            {
                "data": "Stop Charge",
                "render": function (data, type, row, meta) {
                    return `${number_format(row.stop_charges)}`
                }
            },
            {
                "data": "Labour Charge",
                "render": function (data, type, row, meta) {
                    return `${number_format(row.labour_charges)}`
                }
            },
            {
                "data": "Sub Total",
                "render": function (data, type, row, meta) {
                    return `<span style='color:#fff;' >${number_format(row.purchase_total)}</span>`
                }
            },
            {
                "data": "Total",
                "render": function (data, type, row, meta) {
                    return ` <span style='color:#fff;' > ${number_format(row.sale_total)} </span> `
                }
            },
            {
                "data": "Profit",
                "render": function (data, type, row, meta) {
                    var profittd = parseFloat(row.sale_total) - (parseFloat(row.purchase_total) +
                        parseFloat(row.tax_amount));
                    return `<span style='color:#fff;' > ${number_format(profittd)} </span>`
                }
            },
            {
                "data": "Tax %",
                "render": function (data, type, row, meta) {
                    return ` ${row.tax_percent} = ${number_format(row.tax_amount)}`
                }
            },
            {
                "data": "Remarks",
                className: 'remarks',
                "render": function (data, type, row, meta) {
                    return `${row.remarks  || ''}`
                }
            },
            {
                "data": "Picture",
                "render": function (data, type, row, meta) {
                    var imageUrl = assetUrl + "/" + row.img;
                    return ` <img src="${imageUrl}"
                style="max-width: 50px;" class="img-fluid" alt="image">`
                }
            },
            {
                "data": "Status",
                className: 'text-center ',
                "render": function (data, type, row, meta) {
                    return `<select data-id='${row.id}'  name='update_status' ${statusStyle(row.status)} class=" custom_update_status  w-100 " >
                    <option ${row.status == 'approved' && 'selected'} class="text-success"  value="approved" >Approved</option>
                    <option ${row.status == 'pending' && 'selected'} class="text-danger"  value="pending" >Pending</option>
                    </select>`;
                }
            },
        ],
        "createdRow": function (row, data, dataIndex) {
            $(row).attr('data-id', data.id);
            $(row).attr('data-index', dataIndex);
        },
        "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'<'dt--length-menu mr-2 'l> B><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
            "<'table-responsive'tr>" +
            "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p> >",
        buttons: {
            buttons: [
                {
                    extend: 'csv',
                    className: 'btn btn-sm'
                },
                // {
                //     extend: 'excel',
                //     className: 'btn btn-sm'
                // },
                {
                    extend: 'print',
                    className: 'btn btn-sm'
                },
                {
                    text: 'Delete',
                    className: 'btn btn-sm  ',
                    init: function (api, node, config) {
                        $(node).removeClass('dt-button');
                        $(node).addClass('btn btn-sm btn-danger ');
                    },
                    action: function () {
                        if (!selectedRow) return;
                        confirmFunction().then((confirmed) => {
                            if (confirmed) {
                                deleteCall(selectedRow.data('id'));
                            }
                        })
                    }
                },
                {
                    text: "Edit",
                    className: 'btn btn-sm ',
                    init: function (api, node, config) {
                        $(node).removeClass('dt-button');
                        $(node).addClass('btn btn-sm btn-warning ');
                    },
                    action: function () {
                        if (!selectedRow) return;
                        editCall(selectedRow.data('id'), selectedRow.data('index'));
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
            "sLengthMenu": "Results :  _MENU_",
        },
        "lengthMenu": [7, 13, 21, 51 , 100],
        "pageLength": 13,
        "ordering": false
    });
    if(data && data.length == 0 ){
        $('#newContractDatatable  tbody').html(` <tr> <td colspan='100%' > <br><br> 
        <center> <h1>  No data here.</h1>
        </center>
        <br><br> </td> </tr> `)
    }
}