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

    public function requestor()
    {
        return $this->belongsTo(User::class, 'requestor_id');
    }

    public function items()
    {
        return $this->hasMany(ProcurementItem::class, 'request_id');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'request_id');
    }

    public function purchases()
    {
        return $this->hasOne(Purchase::class, 'request_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeByRequestor($query, $userId)
    {
        return $query->where('requestor_id', $userId);
    }
    public function office()
{
    return $this->belongsTo(Office::class, 'office_id');
}
}
