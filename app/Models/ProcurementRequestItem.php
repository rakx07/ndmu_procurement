<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_office_req_id',
        'item_name',
        'quantity',
        'unit_price',
        'total_price',
    ];

    public function procurementRequest()
    {
        return $this->belongsTo(ProcurementRequest::class, 'procurement_office_req_id');
    }
}
