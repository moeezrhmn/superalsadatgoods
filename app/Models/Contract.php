<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;


    protected $fillable =[
        'company_id',
        'date',
        "vehicle_number",
        "vehicle_name",
        'bility',
        'quantity', 
        'item',
        'freight',
        'purchase_status',
        'charge_per_day',
        'stop_charges',  
        'labour_charges', 
        'purchase_total', 
        'sale_total', 
        'tax_percent',
        'tax_amount',
        'remarks',
        'img',
        'status'
    ];


    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }

}
