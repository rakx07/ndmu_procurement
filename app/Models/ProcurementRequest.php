<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requestor_id', 'office', 'date_requested', 'status', 'remarks'
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
}
