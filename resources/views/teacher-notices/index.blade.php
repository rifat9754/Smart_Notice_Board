@extends('adminlte::page')
@section('title', 'Notices for Teachers')
@section('content_header')<h1>Notices for Teachers</h1>@stop
@section('content')

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<a href="{{ route('teacher-notices.create') }}" class="btn btn-primary mb-3">
    <i class="fas fa-plus"></i> New Notice for Teachers
</a>

<div class="card">
    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Title</th><th>Message</th><th>Priority</th><th>Sent by</th><th>Date</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($notices as $n)
                <tr>
                    <td><b>{{ $n->title }}</b></td>
                    <td>{{ Str::limit($n->body, 60) }}</td>
                    <td>
                        <span class="badge badge-{{ $n->priority=='high'?'danger':($n->priority=='medium'?'warning':'info') }}">
                            {{ strtoupper($n->priority) }}
                        </span>
                    </td>
                    <td>{{ $n->author->name ?? '—' }}</td>
                    <td>{{ $n->created_at->format('d M Y') }}</td>
<td>
    <a href="{{ route('teacher-notices.edit', $n) }}" class="btn btn-sm btn-warning">
        <i class="fas fa-edit"></i> Edit
    </a>
    <form action="{{ route('teacher-notices.destroy', $n) }}" method="POST" class="d-inline"
          onsubmit="return confirm('Delete this notice?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
    </form>
</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No notices sent to teachers yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop