<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_request_id',
        'procurement_item_id',
        'item_name',
        'unit_price',
        'quantity',
        'total_price',
    ];
}
