@extends('adminlte::page')
@section('title', 'Notices for Me')
@section('content_header')<h1>Notices for Me</h1>@stop
@section('content')

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div class="card">
    <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="pill" href="#fromcr">
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

            {{-- From CR — reply দেওয়া যায় --}}
            <div class="tab-pane fade show active" id="fromcr">
                @forelse($fromCr as $n)
                    <div class="card card-outline card-success mb-3">
                        <div class="card-body">
                            <div class="mb-2">
                                <span class="badge badge-{{ $n->priority=='high'?'danger':($n->priority=='medium'?'warning':'info') }}">
                                    {{ strtoupper($n->priority) }}
                                </span>
                                @if($n->year && $n->section)
                                    <span class="badge badge-success">{{ $n->year }}-{{ $n->section }}</span>
                                @endif
                                @if($n->course)
                                    <span class="badge badge-primary">{{ $n->course->course_no }} — {{ $n->course->course_title }}</span>
                                @endif
                            </div>

                            <h5>{{ $n->title }}</h5>
                            <p class="mb-2">{{ $n->body }}</p>
                            <small class="text-muted">
                                From: <b>{{ $n->author->name ?? 'CR' }}</b> · {{ $n->created_at->format('d M Y, h:i A') }}
                            </small>

                            @if($n->teacher_reply)
                                <div class="callout callout-success mt-3 mb-0">
                                    <b><i class="fas fa-reply"></i> Your reply:</b> {{ $n->teacher_reply }}
                                    <br><small class="text-muted">{{ $n->replied_at }}</small>
                                </div>
                            @else
                                <form action="{{ route('teacher.reply', $n) }}" method="POST" class="mt-3">
                                    @csrf
                                    <div class="input-group">
                                        <input type="text" name="reply" class="form-control"
                                               placeholder="Write your reply…" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary">
                                                <i class="fas fa-paper-plane"></i> Reply
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-4">No notices from CRs yet.</p>
                @endforelse
            </div>

{{-- For Teachers tab --}}
@forelse($forTeachers as $n)
    <div class="card card-outline card-warning mb-3">
        <div class="card-body">
            <div class="mb-2">
                <span class="badge badge-{{ $n->priority=='high'?'danger':($n->priority=='medium'?'warning':'info') }}">
                    {{ strtoupper($n->priority) }}
                </span>
                <span class="badge badge-warning">FOR TEACHERS</span>
            </div>

            <h5>{{ $n->title }}</h5>
            <p class="mb-2">{{ $n->body }}</p>
            <small class="text-muted">
                From: <b>{{ $n->author->name ?? 'Admin' }}</b> · {{ $n->created_at->format('d M Y, h:i A') }}
            </small>

            {{-- শুধু admin edit/delete দেখবে --}}
           <!-- @can('is-admin')
                <div class="mt-3">
                    <a href="{{ route('teacher-notices.edit', $n) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('teacher-notices.destroy', $n) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this notice?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
                    </form>
                </div>
            @endcan.  -->
        </div>
    </div>
@empty
    <p class="text-muted text-center py-4">No notices for teachers yet.</p>
@endforelse
            </div>

        </div>
    </div>
</div>
@stop