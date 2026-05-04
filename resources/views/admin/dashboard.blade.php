<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Satu Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-primary {
            background-color: #EA580C;
            border-color: #EA580C;
        }
        .btn-primary:hover {
            background-color: #c24d0a;
            border-color: #c24d0a;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Satu Kasir Admin</a>
            <div class="navbar-nav ml-auto">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Owners Management</h2>
            <a href="{{ route('admin.owners.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Add New Owner
            </a>
        </div>

        @if($owners->isEmpty())
            <div class="alert alert-info">No owners registered yet.</div>
        @else
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Business</th>
                            <th>Outlet</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($owners as $index => $owner)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $owner->name }}</td>
                                <td>{{ $owner->email }}</td>
                                <td>{{ $owner->phone ?? '-' }}</td>
                                <td>{{ $owner->business?->name ?? '-' }}</td>
                                <td>{{ $owner->outlet?->name ?? '-' }}</td>
                                <td>{{ $owner->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.owners.edit', $owner->id) }}" 
                                       class="btn btn-sm btn-info">Edit</a>
                                    <form action="{{ route('admin.owners.delete', $owner->id) }}" 
                                          method="POST" 
                                          style="display:inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this owner?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</body>
</html>