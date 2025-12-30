
<table id="datatable" class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
    </thead>
</table>
<script>
    $(document).ready(function () {
        // Initialize DataTable
        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('users.data') }}',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'asc']]
        });

        // Configure Toastr
        toastr.options = {
            closeButton: true,
            debug: false,
            newestOnTop: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            preventDuplicates: false,
            onclick: null,
            showDuration: '300',
            hideDuration: '1000',
            timeOut: '5000',
            extendedTimeOut: '1000',
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut'
        };

        // Open Edit Modal and Load Data
        $(document).on('click', '.editUser', function () {
             var userId = $(this).data('id');
                $.ajax({
                    url: '/users/' + userId,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#editUserId').val(response.data.id);
                            $('#editUserName').val(response.data.name);
                            $('#editUserEmail').val(response.data.email);
                            $('#editUserModal').modal('show');
                        }
                    },
                    error: function (xhr) {
                        var response = xhr.responseJSON;
                        toastr.error(response.message || 'Failed to load user data', 'Error');
                    }
                });
        });

        // Update User via AJAX
        $('#updateUserBtn').on('click', function () {
            var userId = $('#editUserId').val();
            var name = $('#editUserName').val();
            var email = $('#editUserEmail').val();

            // Client-side validation
            if (!name || !email) {
                toastr.error('Name and email are required', 'Validation Error');
                return;
            }

            // Email format validation
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                toastr.error('Please enter a valid email address', 'Validation Error');
                return;
            }

            $.ajax({
                url: '/users/' + userId,
                type: 'POST',
                data: {
                    name: name,
                    email: email,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    toastr.success(response.message, 'Success');
                    $('#editUserModal').modal('hide');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    var response = xhr.responseJSON;
                    toastr.error(response.message || 'Failed to update user', 'Error');
                }
            });
        });
    });
</script>