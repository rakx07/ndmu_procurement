<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestApprovalHistory extends Model
{
    use HasFactory;

    // ✅ Explicitly set the correct table name
    protected $table = 'request_approvals_history'; 

    protected $fillable = [
        'office_req_id',
        'approver_id',
        'role',
        'status',
        'remarks',
    ];

    public function request()
    {
        return $this->belongsTo(ProcurementRequest::class, 'office_req_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
