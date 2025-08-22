<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function expensesByUser(int $userId)
    {
        return $this->expenses()->where('user_id', $userId);
    }

    public function getTotalForUser(int $userId, ?string $month = null)
    {
        $query = $this->expensesByUser($userId);

        if ($month) {
            $query->whereMonth('expense_date', '=', date('m', strtotime($month)))
                ->whereYear('expense_date', '=', date('Y', strtotime($month)));
        }

        return $query->sum('amount');
    }

    public function getCurrentMonthTotalForUser(int $userId)
    {
        return $this->expensesByUser($userId)
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');
    }
}
