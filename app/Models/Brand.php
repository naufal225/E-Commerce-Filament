<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'slug',
        'is_active'
    ];

    public function products() {
        return $this->hasMany(Product::class, 'category_id');
    }
}
