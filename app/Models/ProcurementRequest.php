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
        'date_requested', 
        'status', 
        'remarks', 
        'approved_by'
    ];

    /**
     * Relationship: A Procurement Request belongs to a User (the requestor).
     */
    public function requestor()
    {
        return $this->belongsTo(User::class, 'requestor_id');
    }

    /**
     * Relationship: A Procurement Request has many Procurement Items.
     */
    public function items()
    {
        return $this->hasMany(ProcurementItem::class, 'request_id');
    }

    /**
     * Relationship: A Procurement Request has many Approvals.
     */
    public function approvals()
    {
        return $this->hasMany(Approval::class, 'request_id');
    }

    /**
     * Relationship: A Procurement Request has one Purchase record.
     */
    public function purchases()
    {
        return $this->hasOne(Purchase::class, 'request_id');
    }

    /**
     * Relationship: A Procurement Request is approved by a User (Supervisor/Admin).
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope to filter only pending procurement requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter only approved procurement requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to filter procurement requests by a specific requestor.
     */
    public function scopeByRequestor($query, $userId)
    {
        return $query->where('requestor_id', $userId);
    }
}
