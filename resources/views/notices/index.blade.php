@extends('adminlte::page')
@section('title', 'Notices')
@section('content_header')<h1>Notices</h1>@stop
@section('content')

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<a href="{{ route('notices.create') }}" class="btn btn-primary mb-3">
    <i class="fas fa-plus"></i> New Notice
</a>

<div class="card">
    <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="pill" href="#main">
                    <i class="fas fa-bullhorn"></i> Main Notices
                    <span class="badge badge-primary">{{ $main->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#fromcr">
                    <i class="fas fa-users"></i> Notices from CR
                    <span class="badge badge-success">{{ $fromCr->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#forteachers">
                    <i class="fas fa-chalkboard-teacher"></i> Notices for Teachers
                    <span class="badge badge-warning">{{ $forTeachers->count() }}</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content">

            {{-- Main Notices --}}
            <div class="tab-pane fade show active" id="main">
                <table class="table table-hover">
                    <thead><tr><th>Title</th><th>Priority</th><th>Status</th><th>Author</th><th>Date</th><th>Actions</th></tr></thead>
                    <tbody>
                    @forelse($main as $n)
                        <tr>
                            <td><b>{{ $n->title }}</b></td>
                            <td><span class="badge badge-{{ $n->priority=='high'?'danger':($n->priority=='medium'?'warning':'info') }}">{{ strtoupper($n->priority) }}</span></td>
                            <td><span class="badge badge-{{ $n->status=='published'?'success':'secondary' }}">{{ ucfirst($n->status) }}</span></td>
                            <td>{{ $n->author->name ?? '—' }}</td>
                            <td>{{ $n->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('notices.edit', $n) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('notices.destroy', $n) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No main notices.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Notices from CR --}}
            <div class="tab-pane fade" id="fromcr">
                <table class="table table-hover">
                    <thead><tr><th>Title</th><th>Class</th><th>Course</th><th>CR</th><th>Reply</th><th>Date</th></tr></thead>
                    <tbody>
                    @forelse($fromCr as $n)
                        <tr>
                            <td><b>{{ $n->title }}</b><br><small class="text-muted">{{ Str::limit($n->body, 60) }}</small></td>
                            <td>
                                @if($n->year && $n->section)
                                    <span class="badge badge-success">{{ $n->year }}-{{ $n->section }}</span>
                                @endif
                            </td>
                            <td>{{ $n->course->course_no ?? '—' }}</td>
                            <td>{{ $n->author->name ?? '—' }}</td>
                            <td>
                                @if($n->teacher_reply)
                                    <span class="badge badge-success">Replied</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>{{ $n->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No CR notices.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Notices for Teachers --}}
            <div class="tab-pane fade" id="forteachers">
                <table class="table table-hover">
                    <thead><tr><th>Title</th><th>Message</th><th>Priority</th><th>Sent by</th><th>Date</th></tr></thead>
                    <tbody>
                    @forelse($forTeachers as $n)
                        <tr>
                            <td><b>{{ $n->title }}</b></td>
                            <td>{{ Str::limit($n->body, 70) }}</td>
                            <td><span class="badge badge-{{ $n->priority=='high'?'danger':($n->priority=='medium'?'warning':'info') }}">{{ strtoupper($n->priority) }}</span></td>
                            <td>{{ $n->author->name ?? 'Admin' }}</td>
                            <td>{{ $n->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No notices for teachers.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@stop