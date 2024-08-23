<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'is_expense',
        'image',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'category_id', 'id');
    }
}