<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getExpensesDataTable($request);
        }

        $categories = Category::all();
        return view('pages.expenses.index', compact('categories'));
    }

    public function getExpensesDataTable(Request $request)
    {
        $query = Expense::with(['category'])
            ->where('user_id', Auth::id())
            ->select('expenses.*');

        // Apply category filter
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        // Apply date range filters
        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->orderBy('created_at', 'desc');

        return DataTables::of($expenses)
            ->addIndexColumn()
            ->addColumn('category_name', function ($expense) {
                return $expense->category->name;
            })
            ->addColumn('formatted_amount', function ($expense) {
                return $expense->getFormattedAmountAttribute();
            })
            ->addColumn('formatted_date', function ($expense) {
                return $expense->getFormattedDateAttribute();
            })
            ->addColumn('action', function ($expense) {
                $editBtn = '<a href="' . route('expenses.edit', $expense->id) . '" class="btn btn-sm btn-outline-primary me-1">
                    <i class="fas fa-edit"></i> Edit
                </a>';

                $deleteBtn = '<button type="button" class="btn btn-sm btn-outline-danger"
                    onclick="deleteExpense(' . $expense->id . ')">
                    <i class="fas fa-trash"></i> Delete
                </button>';

                return $editBtn . $deleteBtn;
            })
            ->editColumn('title', function ($expense) {
                $description = $expense->description ?
                    '<small class="text-muted d-block">' . Str::limit($expense->description, 50) . '</small>' : '';
                return '<strong>' . $expense->title . '</strong>' . $description;
            })
            ->filterColumn('category_name', function ($query, $keyword) {
                $query->whereHas('category', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('category_name', function ($query, $order) {
                $query->join('categories', 'expenses.category_id', '=', 'categories.id')
                    ->orderBy('categories.name', $order);
            })
            ->rawColumns(['formatted_amount', 'action', 'title'])
            ->make(true);
    }

    public function create()
    {
        $categories = Category::all();
        return view('pages.expenses.create_edit', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'expense_date' => 'required|date|before_or_equal:today',
            'category_id' => 'required|exists:categories,id',
        ]);

        $validated['user_id'] = Auth::id();

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense added successfully!');
    }

    public function edit(Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            abort(403);
        }

        $categories = Category::all();
        return view('pages.expenses.create_edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'expense_date' => 'required|date|before_or_equal:today',
            'category_id' => 'required|exists:categories,id',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully!');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            abort(403);
        }

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully!'
        ]);
    }
}
