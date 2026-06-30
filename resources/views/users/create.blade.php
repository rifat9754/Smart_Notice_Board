@extends('adminlte::page')
@section('title', 'Add User')
@section('content_header')<h1>Add New User</h1>@stop
@section('content')
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card"><div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label>Department (optional)</label>
                <input type="text" name="department" class="form-control" value="{{ old('department') }}">
            </div>
            <button type="submit" class="btn btn-primary">Create User</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
@stop