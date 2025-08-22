<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $currentMonthTotal = Expense::getCurrentMonthTotalForUser($userId);
        $currentMonthByCategory = Expense::getCurrentMonthByCategory($userId);
        $categoryData = $this->getCategoryExpenseData($userId);
        $recentExpenses = Expense::with('category')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        $totalExpenses = Expense::where('user_id', $userId)->count();

        return view('pages.dashboard', compact(
            'currentMonthTotal',
            'currentMonthByCategory',
            'categoryData',
            'recentExpenses',
            'totalExpenses'
        ));
    }

    private function getCategoryExpenseData($userId)
    {
        return Expense::with('category')
            ->where('user_id', $userId)
            ->whereMonth('expense_date', Carbon::now()->month)
            ->whereYear('expense_date', Carbon::now()->year)
            ->get()
            ->groupBy('category.name')
            ->map(function ($expenses) {
                return $expenses->sum('amount');
            })
            ->toArray();
    }
}
