@extends('layouts.app')

@section('title', 'Expenses Lists')

@push('header_script')
    <link href="//cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('page-title')
    <x-page-title title="Expenses Lists" :breadcrumbs="[['title' => 'Expenses'], ['title' => 'Expenses Lists']]" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="category-filter" class="form-label">Filter by Category:</label>
                            <select id="category-filter" class="form-select">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->name }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date-from" class="form-label">From Date:</label>
                            <input type="date" id="date-from" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="date-to" class="form-label">To Date:</label>
                            <input type="date" id="date-to" class="form-control">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" id="clear-filters" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="table-responsive">
                        <label id="addBtn">
                            <a href="{{ route('expenses.create') }}" class="btn btn-sm btn-primary ms-1">
                                <i class="fas fa-plus"></i> Add Expense
                            </a>
                        </label>
                        <table class="table" id="expenses-table">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Title</th>
                                    <th width="15%">Amount</th>
                                    <th width="15%">Category</th>
                                    <th width="15%">Date</th>
                                    <th width="25%">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this expense? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer_script')
    <script src="//cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
@endpush

@push('custom_js')
    <script>
        $(document).ready(function() {
            var table = $('#expenses-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('expenses.index') }}",
                    data: function(d) {
                        d.category = $('#category-filter').val();
                        d.date_from = $('#date-from').val();
                        d.date_to = $('#date-to').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'formatted_amount',
                        name: 'amount',
                        className: 'text-end'
                    },
                    {
                        data: 'category_name',
                        name: 'category_name'
                    },
                    {
                        data: 'formatted_date',
                        name: 'expense_date',
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                ],
                pageLength: 25,
                responsive: true,
                language: {
                    processing: '<i class="fas fa-spinner fa-spin"></i> Loading...',
                    emptyTable: "No expenses found. <a href=\"{{ route('expenses.create') }}\">Add your first expense</a>!",
                    search: "Search expenses:",
                    lengthMenu: "Show _MENU_ expenses per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ expenses",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-5"i><"col-sm-7"p>>',
            });

            $('.dataTables_filter').append($('#addBtn'));

            $('#category-filter, #date-from, #date-to').change(function() {
                table.draw();
            });

            $('#clear-filters').click(function() {
                $('#category-filter').val('');
                $('#date-from').val('');
                $('#date-to').val('');
                table.draw();
            });
        });

        // Delete expense function
        function deleteExpense(id) {
            $('#deleteModal').modal('show');

            $('#confirmDelete').off('click').on('click', function() {
                $.ajax({
                    url: `/expenses/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        $('#expenses-table').DataTable().ajax.reload();
                        notyfyre.success(response.message);
                    },
                    error: function(xhr) {
                        $('#deleteModal').modal('hide');
                        notyfyre.error('Error deleting expense. Please try again.')
                    }
                });
            });
        }
    </script>
@endpush
