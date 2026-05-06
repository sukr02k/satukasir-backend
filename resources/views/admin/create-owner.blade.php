<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Owner - Satu Kasir Admin</title>
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
        <h2>Add New Owner</h2>
        
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card mt-3">
            <div class="card-body">
                <form action="{{ route('admin.owners.store') }}" method="POST">
                    @csrf
                    
                    <h5 class="mb-3">Owner Information</h5>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Owner Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Minimum 6 characters</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3">Outlet Information</h5>
                    
                    <div class="mb-3">
                        <label for="outlet_name" class="form-label">Outlet Name *</label>
                        <input type="text" class="form-control @error('outlet_name') is-invalid @enderror" id="outlet_name" name="outlet_name" value="{{ old('outlet_name') }}" required>
                        @error('outlet_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="outlet_address" class="form-label">Outlet Address *</label>
                        <textarea class="form-control @error('outlet_address') is-invalid @enderror" id="outlet_address" name="outlet_address" required>{{ old('outlet_address') }}</textarea>
                        @error('outlet_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="outlet_phone" class="form-label">Outlet Phone</label>
                        <input type="text" class="form-control @error('outlet_phone') is-invalid @enderror" id="outlet_phone" name="outlet_phone" value="{{ old('outlet_phone') }}">
                        @error('outlet_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Create Owner</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>