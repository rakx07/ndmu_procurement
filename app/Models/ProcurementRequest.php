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
     * Relationship: Requestor (User who created the request)
     */
    public function requestor()
    {
        return $this->belongsTo(User::class, 'requestor_id'); // Ensure correct foreign key
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
        return $this->hasMany(Approval::class, 'request_id'); // ✅ Fixed incorrect foreign key
    }

    /**
     * Relationship: Purchase record (After procurement is complete)
     */
    public function purchases()
    {
        return $this->hasOne(Purchase::class, 'procurement_request_id');
    }

    /**
     * Relationship: User who last approved the request
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relationship: Office that made the request (Department)
     */
    public function officeRelation()
    {
        return $this->belongsTo(Office::class, 'office_id'); // ✅ Fix department relation
    }

    /**
     * Relationship: Fetch full approval history (New table)
     */
    public function approvalHistory()
    {
        return $this->hasMany(RequestApprovalHistory::class, 'request_id'); // ✅ Tracks full approval history
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

    /**
     * Scope: Fetch requests made by a specific user
     */
    public function scopeByRequestor($query, $userId)
    {
        return $query->where('requested_by', $userId);
    }
}
