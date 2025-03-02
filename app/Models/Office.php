<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // Define relationship: An office has many users
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
