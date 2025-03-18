<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requestor_id', 
        'office', 
        'office_id',
        'date_requested', 
        'status', 
        'remarks', 
        'approved_by',
        'needs_admin_approval', // ✅ Added field
        'comptroller_approved', // ✅ Added field
    ];

    /**
     * Relationship: Requestor (User who created the request)
     */
    public function requestor()
    {
        return $this->belongsTo(User::class, 'requestor_id');
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
        return $this->hasMany(Approval::class, 'procurement_request_id'); // ✅ Fixed foreign key
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
     * Relationship: Office that made the request
     */
    public function officeRelation()
    {
        return $this->belongsTo(Office::class, 'office_id');
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
        return $query->where('requestor_id', $userId);
    }
}
