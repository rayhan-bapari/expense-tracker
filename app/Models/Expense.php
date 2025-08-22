<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Expense extends Model
{
    protected $fillable = [
        'title',
        'amount',
        'expense_date',
        'user_id',
        'category_id',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeCurrentMonth(Builder $query): void
    {
        $query->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year);
    }

    public function scopeForMonth(Builder $query, string $month): void
    {
        $date = Carbon::parse($month);
        $query->whereMonth('expense_date', $date->month)
            ->whereYear('expense_date', $date->year);
    }

    public function scopeForUser(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    public function scopeLatestFirst(Builder $query): void
    {
        $query->orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc');
    }

    public function getFormattedAmountAttribute()
    {
        return '৳' . number_format($this->amount, 2);
    }

    public function getFormattedDateAttribute()
    {
        return $this->expense_date->format('M d, Y');
    }

    public static function getCurrentMonthTotalForUser(int $userId): float
    {
        return static::forUser($userId)
            ->currentMonth()
            ->sum('amount');
    }

    public static function getCurrentMonthByCategory(int $userId): array
    {
        return static::forUser($userId)
            ->currentMonth()
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(function ($expenses) {
                return $expenses->sum('amount');
            })
            ->toArray();
    }
}
