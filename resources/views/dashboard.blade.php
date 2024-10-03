<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registration Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .error {
            color: red;
            font-size: 0.9em;
        }
        .btn-primary {
            width: 100%;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .form-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .table-container {
            margin-top: 30px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Your Website Name</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-link btn btn-link" style="text-decoration: none; color: white;">Logout</button>
                </form>
            </li>
        </ul>
    </div>
</nav>

<!-- Main Container -->
<div class="container">
    <div class="row">
        <!-- Registration Form -->
        <div class="col-md-6">
            <h2 class="form-header">Add Profile</h2>
            <form id="vendorForm" action="{{ route('InsertVendordata') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="profile_pic">Profile Pic</label>
                    <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/*" onchange="previewImage(event)">
                    @if($errors->has('profile_pic'))
                        <div class="error">{{ $errors->first('profile_pic') }}</div>
                    @endif
                </div>
                
                <div id="imagePreview" style="margin-top: 20px;">
                    <img id="preview" src="#" alt="Image Preview" style="display: none; max-width: 100%; height: 280px;">
                </div>
                
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" value="{{ old('name') }}">
                    @if($errors->has('name'))
                        <div class="error">{{ $errors->first('name') }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="text" class="form-control" id="email" name="email" min="3" max="80" placeholder="Enter email" value="{{ old('email') }}">
                    @if($errors->has('email'))
                        <div class="error">{{ $errors->first('email') }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="designation">Designation</label>
                    <input type="text" class="form-control" id="designation" name="designation" placeholder="Enter designation" value="{{ old('designation') }}">
                    @if($errors->has('designation'))
                        <div class="error">{{ $errors->first('designation') }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" class="form-control" id="address" name="address" placeholder="Enter address" value="{{ old('address') }}">
                    @if($errors->has('address'))
                        <div class="error">{{ $errors->first('address') }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="contact">Contact</label>
                    <input type="tel" class="form-control" id="contact" name="contact" placeholder="Enter contact number" value="{{ old('contact') }}">
                    @if($errors->has('contact'))
                        <div class="error">{{ $errors->first('contact') }}</div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>

        <!-- Table Container -->
        <div class="col-md-6">
            <div class="table-container">
                <h1 class="form-header">Vendor List</h1>
                <table id="vendorsTable" class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Designation</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        
        // Initialize DataTables
        const table = $('#vendorsTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '{{ route("vendors.data") }}',
                dataSrc: ''
            },
            columns: [
                { data: 'id', render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1 },
                {
                    data: 'profile_pic',
                    render: data => `<img src="/storage/${data}" alt="Profile Picture" style="width: 50px; height: 50px;">`
                },
                { data: 'name' },
                { data: 'email' },
                { data: 'designation' },
                { data: 'address' },
                { data: 'contact' },
                {
                    data: null, render: (data, type, row) => `
                        <button class="btn btn-warning btn-sm" onclick="editVendor(${row.id})">Edit</button>
                        <button class="btn btn-danger btn-sm delete" data-id="${row.id}">Delete</button>
                    `
                }
            ]
        });

        $('#vendorsTable').on('click', '.delete', function() {
            const vendorId = $(this).data('id');
            if (confirm('Are you sure you want to delete this vendor?')) {
            $.ajax({
            url: `/vendors/delete/${vendorId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('Vendor deleted successfully!');
                table.ajax.reload();
            },
            error: function(xhr) {
                alert('Error deleting vendor: ' + xhr.responseJSON.message);
            }
        });
    }
});

    });



    function editVendor(id) {
    $.ajax({
        url: `/vendor/edit/${id}`,
        method: 'GET',
        success: function(data, response) {

        if (response == "success") {
            $('#edit-id').val(data.id);
            $('#edit-name').val(data.name);
            $('#edit-email').val(data.email);
            $('#edit-designation').val(data.designation);
            $('#edit-address').val(data.address);
            $('#edit-contact').val(data.contact);

            $('#editVendorForm').attr('action', `/vendors/${data.id}`);

            // Check if the profile picture exists
            if (data.profile_pic) {
                $('#edit-preview').attr('src', `/storage/${data.profile_pic}`).show();
            } else {
                $('#edit-preview').hide();
            }

            $('#editFormModal').modal('show');
            $('#vendorsTable').DataTable().ajax.reload();  // Reload the table

        }
    },
        error: function() {
            if (xhr.status === 422) {  // Laravel validation error code
                let errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    $('#edit-' + key + '_error').text(value[0]);  // Show errors next to fields
                });
                } else {
                    alert('An unexpected error occurred.');
                }
            alert('Error fetching vendor data.');
        }
    });
}


    // Function to preview the selected image
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    }
</script>

<!-- Edit Modal -->
<div class="modal fade" id="editFormModal" tabindex="-1" role="dialog" aria-labelledby="editFormModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFormModalLabel">Edit Vendor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

        
            <div class="modal-body">
                <form id="editVendorForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit-id" name="id">
                    <div class="form-group">
                        <label for="edit-name">Name</label>
                        <input type="text" class="form-control" id="edit-name" name="name" required>
                        @if($errors->has('name'))
                        <div class="error">{{ $errors->first('name') }}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="edit-email">Email</label>
                        <input type="text" class="form-control" id="edit-email" name="email" required>
                        @if($errors->has('email'))
                        <div class="error">{{ $errors->first('email') }}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="edit-designation">Designation</label>
                        <input type="text" class="form-control" id="edit-designation" name="designation" required>
                        @if($errors->has('designation'))
                        <div class="error">{{ $errors->first('designation') }}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="edit-address">Address</label>
                        <input type="text" class="form-control" id="edit-address" name="address" required>
                        @if($errors->has('address'))
                        <div class="error">{{ $errors->first('address') }}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="edit-contact">Contact</label>
                        <input type="tel" class="form-control" id="edit-contact" name="contact" required>
                        @if($errors->has('contact'))
                        <div class="error">{{ $errors->first('contact') }}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="edit-profile_pic">Profile Pic</label>
                        <input type="file" class="form-control" id="edit-profile_pic" name="profile_pic" accept="image/*">
                        @if($errors->has('profile_pic'))
                        <div class="error">{{ $errors->first('profile_pic') }}</div>
                        @endif
                        <img id="edit-preview" src="#" alt="Image Preview" style="display: none; max-width: 100%; height: 280px;">
                       
                    </div>
                    <button type="update" class="btn btn-primary">Update Vendor</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
