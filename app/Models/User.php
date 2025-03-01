<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'designation'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function procurementRequests()
    {
        return $this->hasMany(ProcurementRequest::class, 'requestor_id');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'approver_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'purchasing_officer_id');
    }

    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class, 'user_id');
    }

    public function isRole($role)
    {
        $roles = [
            'staff' => 0,
            'purchasing_officer' => 1,
            'supervisor' => 2,
            'admin' => 3,
            'comptroller' => 4,
            'it_admin' => 5,
        ];
        return $this->role === $roles[$role];
    }
}
