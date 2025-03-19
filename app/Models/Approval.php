<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id', 'approver_id', 'role', 'status', 'remarks'
    ];

    /**
     * Get the Procurement Request linked to this approval.
     */
    public function procurementRequest()
    {
        return $this->belongsTo(ProcurementRequest::class, 'request_id');
    }

    /**
     * Get the Approver (Supervisor/Admin/Comptroller) who approved this request.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Get the Staff (Requestor) who created the Procurement Request.
     */
    public function requestor()
    {
        return $this->hasOneThrough(User::class, ProcurementRequest::class, 'id', 'id', 'request_id', 'requestor_id');
    }

    /**
     * Scope to filter approvals by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get approvals for a specific request.
     */
    public function scopeForRequest($query, $requestId)
    {
        return $query->where('request_id', $requestId);
    }

    /**
     * Scope to get approvals by role.
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }
}
