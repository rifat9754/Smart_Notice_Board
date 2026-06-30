@extends('adminlte::page')
@section('title', 'Users')
@section('content_header')<h1>User Management</h1>@stop
@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">+ Add User</a>

    <div class="card"><div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Department</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>
                            @php
                                $color = ['super_admin' => 'danger', 'teacher' => 'info', 'student' => 'success'][$u->role] ?? 'secondary';
                            @endphp
                            <span class="badge badge-{{ $color }}">{{ str_replace('_', ' ', $u->role) }}</span>
                        </td>
                        <td>{{ $u->department ?? '—' }}</td>
                        <td>
                            <form action="{{ route('users.destroy', $u) }}" method="POST" onsubmit="return confirm('Delete this user?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div></div>
@stop