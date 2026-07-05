@extends('adminlte::page')
@section('title', 'Events')
@section('content_header')<h1>Department Events</h1>@stop
@section('content')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="card"><div class="card-body">
        <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Event Title (optional)</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. CSE Fest 2026">
            </div>
            <div class="form-group">
                <label>Event Image</label>
                <input type="file" name="image" class="form-control-file" accept="image/*" required>
            </div>
            <button class="btn btn-primary">Upload Event</button>
        </form>
    </div></div>

    <div class="row">
        @forelse($events as $e)
            <div class="col-md-4">
                <div class="card">
                    <img src="{{ asset('storage/'.$e->image_path) }}" class="card-img-top" style="height:180px;object-fit:cover;">
                    <div class="card-body">
                        <p class="mb-2"><b>{{ $e->title ?? 'Untitled' }}</b></p>
                        <form action="{{ route('events.destroy', $e) }}" method="POST" onsubmit="return confirm('Delete this event image?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="alert alert-info">No event images yet.</div></div>
        @endforelse
    </div>
@stop