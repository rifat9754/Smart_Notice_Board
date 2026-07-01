@extends('adminlte::page')
@section('title', 'Notices for Me')
@section('content_header')<h1>🔔 Notices for Me</h1>@stop
@section('content')
    <p class="text-muted">Notices where a class representative (CR) has specifically notified you.</p>

    @forelse($notices as $n)
        @php $c = ['high'=>'danger','medium'=>'warning','low'=>'info'][$n->priority] ?? 'secondary'; @endphp
        <div class="card">
            <div class="card-body">
                <span class="badge badge-{{ $c }} float-right">{{ strtoupper($n->priority) }}</span>
                <h4>{{ $n->title }}</h4>
                <p class="mb-2">{{ $n->body }}</p>
<small class="text-muted">
    From: {{ $n->author->name ?? 'Unknown' }} ({{ $n->author->role ?? '' }})
    @if($n->year && $n->section)
        · <span class="badge badge-primary">{{ $n->year }} Year - Section {{ $n->section }}</span>
    @endif
    · {{ $n->created_at->diffForHumans() }}
</small>
            </div>
        </div>
    @empty
        <div class="alert alert-info">No notices have been directed to you yet.</div>
    @endforelse
@stop