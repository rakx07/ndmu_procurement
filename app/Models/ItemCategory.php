<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    use HasFactory;

    // ✅ Explicitly define table name (optional but useful for clarity)
    protected $table = 'item_categories';

    // ✅ Specify fillable fields for mass assignment
    protected $fillable = ['name', 'description'];

    // ✅ Define relationship with ProcurementItem
    public function procurementItems()
    {
        return $this->hasMany(ProcurementItem::class, 'item_category_id', 'id');
    }
}
