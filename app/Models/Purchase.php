<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_req_id', 'purchasing_officer_id', 'supplier_name', 'supplier_contact', 'purchased_date', 'total_amount', 'purchase_status', 'invoice_file'
    ];

    public function procurementRequest()
    {
        return $this->belongsTo(ProcurementRequest::class, 'office_req_id');
    }

    public function purchasingOfficer()
    {
        return $this->belongsTo(User::class, 'purchasing_officer_id');
    }
}
