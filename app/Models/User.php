<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'employee_id',
        'lastname',
        'firstname',
        'middlename',
        'email',
        'password',
        'role',
        'office_id',  // ✅ Fixed field name
        'status',
        'password_changed_at'
    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];

    protected $casts = [
        'password_changed_at' => 'datetime',
    ];

    /**
     * Check if the user must change their password (first login)
     */
    public function mustChangePassword()
    {
        return is_null($this->password_changed_at);
    }

    /**
     * Define user roles
     */
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

    /**
     * Relationships
     */
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

    /**
     * Ensure password is only hashed once when saving
     */
    public function setPasswordAttribute($value)
    {
        if (Hash::needsRehash($value)) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * Relationship with Office
     */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }   

    /**
     * Get role text
     */
    public function roleText()
    {
        $roles = [
            0 => 'Staff',
            1 => 'Purchasing Officer',
            2 => 'Supervisor',
            3 => 'Administrator',
            4 => 'Comptroller',
            5 => 'IT Admin',
        ];

        return $roles[$this->role] ?? 'Unknown';
    }
}
