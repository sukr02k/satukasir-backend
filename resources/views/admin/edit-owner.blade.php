<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Owner - Satu Kasir Admin</title>
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
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Satu Kasir Admin</a>
            <div class="navbar-nav ml-auto">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-sm">Back to Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Edit Owner: {{ $owner->name }}</h2>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card mt-3">
            <div class="card-body">
                <form action="{{ route('admin.owners.update', $owner->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <h5 class="mb-3">Owner Information</h5>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Owner Name *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="{{ $owner->name }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ $owner->email }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="text-muted">Leave empty to keep current password</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                               value="{{ $owner->phone }}">
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3">Outlet Information</h5>
                    
                    <div class="mb-3">
                        <label for="outlet_name" class="form-label">Outlet Name *</label>
                        <input type="text" class="form-control" id="outlet_name" name="outlet_name" 
                               value="{{ $owner->outlet?->name }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="outlet_address" class="form-label">Outlet Address *</label>
                        <textarea class="form-control" id="outlet_address" name="outlet_address" required>{{ $owner->outlet?->address }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="outlet_phone" class="form-label">Outlet Phone</label>
                        <input type="text" class="form-control" id="outlet_phone" name="outlet_phone" 
                               value="{{ $owner->outlet?->phone }}">
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update Owner</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>