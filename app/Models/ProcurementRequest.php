<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementRequest extends Model
{
    use HasFactory;

    protected $table = 'procurement_requests';

    protected $fillable = [
        'requestor_id',
        'office', 
        'office_id',
        'date_requested', 
        'status', 
        'remarks', 
        'approved_by',
        'needs_admin_approval',
        'comptroller_approved',
        'administrator_approved',
    ];

    /**
     * Requestor (User who created the request)
     */
    public function requestor()
    {
        return $this->belongsTo(User::class, 'requestor_id');
    }

    /**
     * Items in the procurement request
     */
    public function items()
    {
        return $this->hasMany(ProcurementRequestItem::class, 'procurement_office_req_id');
    }

    /**
     * Approval records related to this request
     */
    public function approvals()
    {
        return $this->hasMany(Approval::class, 'office_req_id');
    }

    /**
     * Purchase record associated with the request
     */
    public function purchases()
    {
        return $this->hasOne(Purchase::class, 'procurement_office_req_id');
    }

    /**
     * Final approver of the request (if applicable)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Department or office that made the request
     */
    public function officeRelation()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    /**
     * Full approval history (optional extended relation)
     */
    public function approvalHistory()
    {
        return $this->hasMany(RequestApprovalHistory::class, 'office_req_id');
    }

    // === Query Scopes ===

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSupervisorApproved($query)
    {
        return $query->where('status', 'supervisor_approved');
    }

    public function scopeAdminApproved($query)
    {
        return $query->where('status', 'admin_approved');
    }

    public function scopeComptrollerApproved($query)
    {
        return $query->where('status', 'comptroller_approved');
    }

    public function scopeByRequestor($query, $userId)
    {
        return $query->where('requestor_id', $userId); // ✅ updated to match the real DB field
    }
}
