@extends('adminlte::page')

@section('title', 'Notices for Me')

@section('content_header')
    <h1>🔔 Notices for Me</h1>
@stop

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <p class="text-muted">
        Notices where a class representative (CR) has specifically notified you.
    </p>

    @forelse($notices as $n)

        @php
            $c = [
                'high' => 'danger',
                'medium' => 'warning',
                'low' => 'info'
            ][$n->priority] ?? 'secondary';
        @endphp

        <div class="card">

            <div class="card-body">

                <span class="badge badge-{{ $c }} float-right">
                    {{ strtoupper($n->priority) }}
                </span>

                <h4>{{ $n->title }}</h4>

                <p class="mb-2">
                    {{ $n->body }}
                </p>

                <small class="text-muted">
                    From:
                    {{ $n->author->name ?? 'Unknown' }}
                    ({{ $n->author->role ?? '' }})

                    @if($n->year && $n->section)
                        ·
                        <span class="badge badge-primary">
                            {{ $n->year }} Year - Section {{ $n->section }}
                        </span>
                    @endif

                    · {{ $n->created_at->diffForHumans() }}
                </small>

                <hr>

                {{-- Existing Reply --}}

                @if($n->teacher_reply)

                    <div class="callout callout-success">

                        <strong>Your reply:</strong>

                        {{ $n->teacher_reply }}

                        <br>

                        <small class="text-muted">
                            {{ $n->replied_at?->diffForHumans() }}
                        </small>

                    </div>

                @endif

                {{-- Reply Form --}}

                <form action="{{ route('teacher.reply', $n) }}" method="POST">

                    @csrf

                    <div class="input-group">

                        <input
                            type="text"
                            name="teacher_reply"
                            class="form-control"
                            required
                            value="{{ old('teacher_reply') }}"
                            placeholder="{{ $n->teacher_reply ? 'Update your reply…' : 'Write a reply to CR…' }}"
                        >

                        <div class="input-group-append">

                            <button class="btn btn-primary">

                                {{ $n->teacher_reply ? 'Update' : 'Reply' }}

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    @empty

        <div class="alert alert-info">

            No notices have been directed to you yet.

        </div>

    @endforelse

@stop