<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementRequest extends Model
{
    use HasFactory;

    protected $table = 'procurement_requests'; // ✅ Ensures Laravel uses the correct table name

    protected $fillable = [
        'requested_by', 
        'office', 
        'office_id',
        'date_requested', 
        'status', 
        'remarks', 
        'approved_by',
        'needs_admin_approval',
        'comptroller_approved',
    ];

    /**
     * Relationship: User who created the request (Requestor)
     */
    public function requestor()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
    
    /**
     * Relationship: Office that made the request
     */
    public function officeRelation()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    /**
     * Relationship: Items in the procurement request
     */
    public function items()
    {
        return $this->hasMany(ProcurementRequestItem::class, 'procurement_request_id');
    }

    /**
     * Relationship: Approvals (Tracks approvals for the request)
     */
    public function approvals()
    {
        return $this->hasMany(Approval::class, 'request_id');
    }

    /**
     * Relationship: Fetch full approval history
     */
    public function approvalHistory()
    {
        return $this->hasMany(RequestApprovalHistory::class, 'request_id');
    }

    /**
     * Scope: Fetch pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Fetch requests approved by Supervisor
     */
    public function scopeSupervisorApproved($query)
    {
        return $query->where('status', 'supervisor_approved');
    }

    /**
     * Scope: Fetch requests approved by Administrator
     */
    public function scopeAdminApproved($query)
    {
        return $query->where('status', 'admin_approved');
    }

    /**
     * Scope: Fetch requests approved by Comptroller
     */
    public function scopeComptrollerApproved($query)
    {
        return $query->where('status', 'comptroller_approved');
    }
}
