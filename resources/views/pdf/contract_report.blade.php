<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> {{$meta['title'] }} </title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }
        .row{
            margin-bottom: 30px;
        }
        .textRed {
            color: red;
        }
        .textGreen{
            color: green;
        }
    </style>
</head>

<body>
    <h2> {{ $meta['author'] }} - Contracts Report </h2>
    @if($daily)
        <h4> Report of {{ Carbon\Carbon::now()->format('D, d-F, Y')}} </h4>
    @endif
    @if(!empty($filters) && empty($daily))
        <div class="row">
            <div class="filters">
                <table style="width:35%;" >
                    <thead>
                        <tr>
                            <th colspan="2" class='text-center' > Filters </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filters as $k => $val)
                            <tr>
                                <th> {{strtoupper(str_replace('_', ' ', $k))}} </th>
                                <td> {{ empty($val) ? '' : $val }} </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    <div class="row">
        <div>
            <table>
                <thead>
                    <tr>
                        <th colspan='2' class='text-center'> Contracts </th>
                        <th colspan='2' class='text-center'> Freight / Driver's payment </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Pending</th>
                        <td>{{ count($data->pending_status) }}</td>
                        <th>Approved</th>
                        <td>{{ count($data->pending_purchase) }}</td>
                    </tr>
                    <tr>
                        <th>Approved</th>
                        <td>{{ count($data->approved_status) }}</td>
                        <th>Approved</th>
                        <td>{{ count($data->approved_purchase) }}</td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td>{{ count($data->total_contracts) }}</td>
                        <th>Total</th>
                        <td>{{ count($data->approved_purchase) + count($data->pending_purchase) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
    <div class="row">
        <div>
            <table>
                <thead>
                    <tr>
                        <th colspan='2' class='text-center' > Pending Contracts </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th> Tax Amount </th>
                        <td>{{ number_format($data->calculations->contracts->pending->tax_amount) }} </td>
                    </tr>
                    <tr>
                        <th> Investment </th>
                        <td> ( {{ number_format($data->calculations->contracts->pending->approved_purchase->total) }} ) + Pending (Freight/Driver pay) pay ( {{ number_format($data->calculations->contracts->pending->pending_purchase->total) }} ) <br> = {{ number_format($data->calculations->contracts->pending->total_purchase) }}  </td>
                    </tr>
                    <tr>
                        <th> Bill - Sale </th>
                        <td> {{ number_format($data->calculations->contracts->pending->sale_total) }} </td>
                    </tr>
                    <tr>
                        <th> Profit </th>
                        <td>{{ number_format($data->calculations->contracts->pending->profit) }} </td>
                    </tr>
                </tbody>
            </table>
            </div >
        </div>
        <div class="row">
            <div>
                <table>
                    <thead>
                        <tr>
                            <th colspan='2' class='text-center' > Approved Contracts </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th> Tax Amount </th>
                            <td> {{ number_format($data->calculations->contracts->approved->tax_amount) }} </td>
                        </tr>
                        <tr>
                            <th> Investment </th>
                            <td> {{ number_format($data->calculations->contracts->approved->approved_purchase->total)}} + Pending (Freight/Driver pay) ( {{ number_format($data->calculations->contracts->approved->pending_purchase->total) }}) <br> =  {{ number_format($data->calculations->contracts->approved->total_purchase)}}  </td>
                        </tr>
                        <tr>
                            <th> Bill - Sale </th>
                            <td>  {{ number_format($data->calculations->contracts->approved->sale_total)}} </td>
                        </tr>
                        <tr>
                            <th> Profit </th>
                            <td> {{number_format($data->calculations->contracts->approved->profit)}} </td>
                        </tr>
                    </tbody>
                </table>
            </div >
        </div>

        <div class="row">
            <h3>Records:</h3>
        </div>
    
        <div class="row">
            <table>
                <thead>
                    <tr>
                        <th> Company </th>
                        <th> Date </th>
                        <th> Bill </th>
                        <th> Tax </th>
                        <th> Remakrs </th>
                        <th> Status </th>
                     </tr>
                </thead>
                <tbody>
                    @foreach($contracts as $val)
                    <tr>
                        <td> 
                            <div> {{$val->company->name}} </div>
                            <div> Contact: {{$val->company->contact}}  </div>
                        </td>
                        <td> {{ Carbon\Carbon::parse($val->date)->format('D, d-F, Y')  }} </td>
                        <td> {{number_format($val->sale_total)}}  </td>
                        <td> {{$val->tax_percent}} = {{ $val->tax_amount }} </td>
                        <td> {{ empty($val->remarks) ? '' : $val->remarks  }} </td>
                        <td> 
                            <span style="text-transform: capitalize;" class=" {{ $val->status == 'approved' ? 'textGreen' : 'textRed';  }} " >
                                {{ $val->status  }} 
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    
    
    
    </body>

</html>