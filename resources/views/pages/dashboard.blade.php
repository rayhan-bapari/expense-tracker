@extends('layouts.app')

@section('title', 'Dashboard')

@push('custom_css')
    <style>
        .stats-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }

        .stats-card:hover {
            transform: translateY(-2px);
        }

        .stats-card.primary {
            border-left-color: #007bff;
        }

        .stats-card.success {
            border-left-color: #28a745;
        }

        .stats-card.warning {
            border-left-color: #ffc107;
        }

        .stats-card.info {
            border-left-color: #17a2b8;
        }

        .chart-container {
            position: relative;
            height: 400px;
        }
    </style>
@endpush

@section('page-title')
    <x-page-title title="Dashboard" :breadcrumbs="[['title' => 'Home'], ['title' => 'Dashboard']]" />
@endsection

@section('content')
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card stats-card primary shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-primary mb-1">This Month Total</h6>
                            <h4 class="mb-0">৳{{ number_format($currentMonthTotal, 2) }}</h4>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-calendar-month fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stats-card success shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-success mb-1">Total Expenses</h6>
                            <h4 class="mb-0">{{ number_format($totalExpenses) }}</h4>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-receipt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stats-card warning shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-warning mb-1">Categories Used</h6>
                            <h4 class="mb-0">{{ count($currentMonthByCategory) }}</h4>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-tags fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stats-card info shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-info mb-1">Avg Per Day</h6>
                            <h4 class="mb-0">৳{{ number_format($currentMonthTotal / now()->day, 2) }}</h4>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Expenses</h5>
                    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if ($recentExpenses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentExpenses as $expense)
                                        <tr>
                                            <td>
                                                <strong>{{ $expense->title }}</strong>
                                                @if ($expense->description)
                                                    <br><small
                                                        class="text-muted">{{ Str::limit($expense->description, 40) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-secondary">{{ $expense->category->name ?? 'N/A' }}</span>
                                            </td>
                                            <td class="text-end">
                                                <strong
                                                    class="text-danger">{{ $expense->getFormattedAmountAttribute() }}</strong>
                                            </td>
                                            <td>{{ $expense->getFormattedDateAttribute() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No expenses yet</h6>
                            <p class="text-muted">Start tracking your expenses to see them here.</p>
                            <a href="{{ route('expenses.create') }}" class="btn btn-primary">Add First Expense</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Current Month by Category</h5>
                    <i class="fas fa-chart-pie text-success"></i>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom_js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            const categoryData = @json($categoryData);

            if (Object.keys(categoryData).length > 0) {
                const colors = [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                    '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                ];

                new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(categoryData),
                        datasets: [{
                            data: Object.values(categoryData),
                            backgroundColor: colors.slice(0, Object.keys(categoryData).length),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = '৳' + context.parsed.toLocaleString();
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                categoryCtx.canvas.parentElement.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No expenses this month</p>
                </div>
            `;
            }
        });
    </script>
@endpush
