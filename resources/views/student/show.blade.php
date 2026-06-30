@extends('adminlte::page')
@section('title', 'Notice')
@section('content_header')<h1>Notice</h1>@stop
@section('content')
    @php
        $color = ['high' => 'danger', 'medium' => 'warning', 'low' => 'info'][$notice->priority] ?? 'secondary';
    @endphp
    <div class="card">
        <div class="card-body">
            <span class="badge badge-{{ $color }}">{{ strtoupper($notice->priority) }}</span>
            <h2 class="mt-2">{{ $notice->title }}</h2>
            <p style="font-size:17px; line-height:1.6;">{{ $notice->body }}</p>

            @if($notice->ai_summary)
                <div class="callout callout-info mt-3">
                    <h5>✨ AI Summary</h5>
                    <p class="mb-0">{{ $notice->ai_summary }}</p>
                </div>
            @endif

            @if($notice->file_path)
                <a href="{{ asset('storage/' . $notice->file_path) }}" target="_blank" class="btn btn-secondary mt-2">
                    📎 View Attachment
                </a>
            @endif
        </div>
    </div>
    <a href="{{ route('student.feed') }}" class="btn btn-light">← Back to notices</a>
@stop