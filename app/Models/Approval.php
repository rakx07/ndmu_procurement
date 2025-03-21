<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Approval extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_req_id', 'approver_id', 'role', 'status', 'remarks'
    ];

    public $timestamps = true; // ✅ Ensure Laravel automatically updates timestamps

    public function procurement_request()
    {
        return $this->belongsTo(ProcurementRequest::class, 'office_req_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function requestor()
    {
        return $this->hasOneThrough(
            User::class,
            ProcurementRequest::class,
            'id',          // Foreign key in `procurement_requests`
            'id',          // Primary key in `users`
            'office_req_id',  // Foreign key in `approvals`
            'requestor_id' // Foreign key in `procurement_requests`
        );
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForRequest($query, $requestId)
    {
        return $query->where('office_req_id', $requestId);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }
}
