<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'category_id',
        'image',
        'note',
        'date_transaction'
    ];

    public function categories(): BelongsTo {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public static function totalIncome() {
        return self::whereHas('categories', function ($query) {
            $query->where('is_expense', false);
        })->sum('amount');
    }

    public static function totalExpense() {
        return self::whereHas('categories', function ($query) {
            $query->where('is_expense', true);
        })->sum('amount');
    }
}
