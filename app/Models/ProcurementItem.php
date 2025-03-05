<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id', 
        'item_name', 
        'quantity', 
        'unit_price', 
        'total_price', 
        'item_image', 
        'status',
        'office_id' // ✅ Added office_id to track purchases by office
    ];

    /**
     * A Procurement Item belongs to a Procurement Request.
     */
    public function procurementRequest()
    {
        return $this->belongsTo(ProcurementRequest::class, 'request_id');
    }

    /**
     * A Procurement Item belongs to an Office.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id'); // Assuming you have an Office model
    }
}
