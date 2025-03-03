<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestModel extends Model
{
    use HasFactory;

    protected $table = 'procurement_requests'; // Updated to match the actual table name

    protected $fillable = [
        'requestor_id',
        'office',
        'date_requested',
        'status',
        'remarks',
    ];

    public function requestor()
    {
        return $this->belongsTo(User::class, 'requestor_id');
    }
}
