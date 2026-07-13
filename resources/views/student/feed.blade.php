@extends('adminlte::page')
@section('title', 'Notices')
@section('content_header')<h1>📢 Notices</h1>@stop
@section('content')

<div class="card">
    <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="pill" href="#dept" role="tab">
                    <i class="fas fa-building"></i> Departmental Notices
                    <span class="badge badge-primary ml-1">{{ $departmental->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#class" role="tab">
                    <i class="fas fa-users"></i> Class Updates
                    <span class="badge badge-success ml-1">{{ $classUpdates->count() }}</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">

            {{-- Departmental --}}
            <div class="tab-pane fade show active" id="dept" role="tabpanel">
                @forelse($departmental as $n)
                    @php
                        $color = ['high' => 'danger', 'medium' => 'warning', 'low' => 'info'][$n->priority] ?? 'secondary';
                    @endphp
                    <div class="card card-outline card-{{ $color }} mb-3">
                        <div class="card-body">
                            <span class="badge badge-{{ $color }} float-right">{{ strtoupper($n->priority) }}</span>
                            <h4>{{ $n->title }}</h4>
                            <p class="text-muted mb-2">{{ \Illuminate\Support\Str::limit($n->body, 120) }}</p>
                            <a href="{{ route('student.show', $n) }}" class="btn btn-sm btn-primary">Read more</a>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info">No departmental notices right now.</div>
                @endforelse
            </div>

            {{-- Class Updates --}}
            <div class="tab-pane fade" id="class" role="tabpanel">
                @forelse($classUpdates as $n)
                    @php
                        $color = ['high' => 'danger', 'medium' => 'warning', 'low' => 'info'][$n->priority] ?? 'secondary';
                    @endphp
                    <div class="card card-outline card-success mb-3">
                        <div class="card-body">
                            <span class="badge badge-{{ $color }} float-right">{{ strtoupper($n->priority) }}</span>
                            @if($n->year && $n->section)
                                <span class="badge badge-success float-right mr-1">{{ $n->year }} - {{ $n->section }}</span>
                            @endif
                            <h4>{{ $n->title }}</h4>
                            <p class="text-muted mb-2">{{ \Illuminate\Support\Str::limit($n->body, 120) }}</p>
                            <a href="{{ route('student.show', $n) }}" class="btn btn-sm btn-primary">Read more</a>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info">No class updates right now.</div>
                @endforelse
            </div>

        </div>
    </div>
</div>

@stop