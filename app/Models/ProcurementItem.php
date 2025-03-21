<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_req_id', 
        'item_name', 
        'quantity', 
        'unit_price', 
        'total_price', 
        'item_image', 
        'status',
        'office_id',
        'supplier_name', // ✅ Add this
        'item_category_id', // ✅ Add this
    ];

    /**
     * A Procurement Item belongs to a Procurement Request.
     */
    public function procurementRequest()
    {
        return $this->belongsTo(ProcurementRequest::class, 'office_req_id');
    }

    /**
     * A Procurement Item belongs to an Office.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id'); // Assuming you have an Office model
    }
    public function category()
{
    return $this->belongsTo(ItemCategory::class, 'item_category_id');
}
}
