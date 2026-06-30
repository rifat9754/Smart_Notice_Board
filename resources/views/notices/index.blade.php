@extends('adminlte::page')
@section('title', 'Notices')
@section('content_header')<h1>Notices</h1>@stop
@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <a href="{{ route('notices.create') }}" class="btn btn-primary mb-3">+ New Notice</a>
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr><th>Title</th><th>Priority</th><th>Status</th><th>Emergency</th><th>Author</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($notices as $notice)
                        <tr>
                            <td>{{ $notice->title }}</td>
                            <td>{{ ucfirst($notice->priority) }}</td>
                            <td>{{ ucfirst($notice->status) }}</td>
                            <td>{{ $notice->is_emergency ? 'Yes' : 'No' }}</td>
                            <td>{{ $notice->author->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('notices.edit', $notice) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('notices.destroy', $notice) }}" method="POST" style="display:inline"
                                    onsubmit="return confirm('Delete this notice?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No notices yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop