<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function getCurrentMonthExpenses()
    {
        return $this->expenses()
            ->with('category')
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->orderBy('expense_date', 'desc')
            ->get();
    }

    public function getCurrentMonthTotal()
    {
        return $this->expenses()
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');
    }

    public function getCurrentMonthByCategory()
    {
        return $this->getCurrentMonthExpenses()
            ->groupBy('category.name')
            ->map(function ($expenses) {
                return $expenses->sum('amount');
            })
            ->toArray();
    }
}
