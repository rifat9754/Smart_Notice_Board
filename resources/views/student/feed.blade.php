@extends('adminlte::page')
@section('title', 'Notices')
@section('content_header')<h1>📢 Notices</h1>@stop
@section('content')
    @forelse($notices as $n)
        @php
            $color = ['high' => 'danger', 'medium' => 'warning', 'low' => 'info'][$n->priority] ?? 'secondary';
        @endphp
        <div class="card">
            <div class="card-body">
                <span class="badge badge-{{ $color }} float-right">{{ strtoupper($n->priority) }}</span>
                <h4>{{ $n->title }}</h4>
                <p class="text-muted mb-2">{{ \Illuminate\Support\Str::limit($n->body, 120) }}</p>
                <a href="{{ route('student.show', $n) }}" class="btn btn-sm btn-primary">Read more</a>
            </div>
        </div>
    @empty
        <div class="alert alert-info">No notices available right now.</div>
    @endforelse
@stop