<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

     // Define role constants
     const ROLE_STAFF = 0;
     const ROLE_PURCHASING_OFFICER = 1;
     const ROLE_SUPERVISOR = 2;
     const ROLE_ADMINISTRATOR = 3;
     const ROLE_COMPTROLLER = 4;
     const ROLE_IT_ADMIN = 5;
     const ROLE_BOOK_ROOM = 6;
     const ROLE_PHYSICAL_PLANT_INVENTORY_OFFICER = 7;

     
    protected $fillable = [
        'employee_id',
        'lastname',
        'firstname',
        'middlename',
        'email',
        'password',
        'role',
        'office_id',
        'status',
        'password_changed_at',
        'must_change_password',
        'supervisor_id',
        'administrator_id',
    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];

    protected $casts = [
        'password_changed_at' => 'datetime',
        'must_change_password' => 'boolean',
    ];

    /**
     * Check if the user must change their password (first login or admin-created)
     */
    public function mustChangePassword()
    {
        return $this->must_change_password;
    }

    /**
     * Define user roles
     */
    public function isRole($role)
    {
        $roles = [
            'staff' => self::ROLE_STAFF,
            'purchasing_officer' => self::ROLE_PURCHASING_OFFICER,
            'supervisor' => self::ROLE_SUPERVISOR,
            'admin' => self::ROLE_ADMINISTRATOR,
            'comptroller' => self::ROLE_COMPTROLLER,
            'it_admin' => self::ROLE_IT_ADMIN,
            'book_room' => self::ROLE_BOOK_ROOM,
            'physical_plant_inventory_officer' => self::ROLE_PHYSICAL_PLANT_INVENTORY_OFFICER,
        ];

        return isset($roles[$role]) && $this->role === $roles[$role];
    }

    /**
     * Hierarchical Relationships
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function administrator()
    {
        return $this->belongsTo(User::class, 'administrator_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    /**
     * Check if the user can approve a procurement request
     */
    public function canApprove(ProcurementRequest $request)
    {
        // If the user is a Supervisor, they can approve only their direct Staff
        if ($this->isRole('supervisor') && $request->user->supervisor_id == $this->id) {
            return true;
        }

        // If the user is an Administrator, they can approve Supervisors & Staff under them
        if ($this->isRole('admin') && 
            ($request->user->administrator_id == $this->id || $request->user->supervisor_id == $this->id)) {
            return true;
        }

        return false;
    }

    /**
     * Procurement-related Relationships
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
            self::ROLE_STAFF => 'Staff',
            self::ROLE_PURCHASING_OFFICER => 'Purchasing Officer',
            self::ROLE_SUPERVISOR => 'Supervisor',
            self::ROLE_ADMINISTRATOR => 'Administrator',
            self::ROLE_COMPTROLLER => 'Comptroller',
            self::ROLE_IT_ADMIN => 'IT Admin',
            self::ROLE_BOOK_ROOM => 'Book Room',
            self::ROLE_PHYSICAL_PLANT_INVENTORY_OFFICER => 'Physical Plant Inventory Officer',
        ];

        return $roles[$this->role] ?? 'Unknown';
    }

    /**
     * Force password change after first login if required
     */
    public function markPasswordAsChanged()
    {
        $this->must_change_password = false;
        $this->password_changed_at = now();
        $this->save();
    }
    public function getFullNameAttribute()
    {
        $middleInitial = $this->middlename ? strtoupper(substr($this->middlename, 0, 1)) . '.' : '';
        return "{$this->firstname} {$middleInitial} {$this->lastname}";
    }
    
//Added Constant

}
