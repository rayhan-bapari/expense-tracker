@extends('layouts.app')

@section('title', isset($expense) ? 'Edit Expense' : 'Add New Expense')

@section('page-title')
    <x-page-title :title="isset($expense) ? 'Edit Expense' : 'Add New Expense'" :breadcrumbs="[
        ['title' => 'Expenses', 'url' => route('expenses.index')],
        ['title' => isset($expense) ? 'Edit Expense' : 'Add New Expense'],
    ]" />
@endsection

@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <form action="{{ isset($expense) ? route('expenses.update', $expense) : route('expenses.store') }}"
                    method="POST" class="needs-validation" novalidate id="expenseForm">
                    @csrf
                    @if (isset($expense))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    Title <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title', $expense->title ?? '') }}"
                                    placeholder="e.g., Lunch at restaurant" required>
                                @error('title')
                                    <div>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="amount" class="form-label">
                                    Amount <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                        id="amount" name="amount" value="{{ old('amount', $expense->amount ?? '') }}"
                                        step="0.01" min="0.01" max="999999.99" placeholder="0.00" required>
                                </div>
                                @error('amount')
                                    <div>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">
                                    Category <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id" required>
                                    <option value="">Select a category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $expense->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expense_date" class="form-label">
                                    Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('expense_date') is-invalid @enderror"
                                    id="expense_date" name="expense_date"
                                    value="{{ old('expense_date', isset($expense) ? $expense->expense_date->format('Y-m-d') : date('Y-m-d')) }}"
                                    max="{{ date('Y-m-d') }}" required>
                                @error('expense_date')
                                    <div>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                            Back to Expenses
                        </a>

                        <div>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                {{ isset($expense) ? 'Update Expense' : 'Save Expense' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
