@extends('adminlte::page')
@section('title', 'Live Ticker')
@section('content_header')<h1>Live Board Ticker</h1>@stop
@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="alert alert-info">
    <i class="fas fa-info-circle"></i>
    Messages added here scroll along the bottom of the display board.
    <b>If no message is active, the board automatically shows the titles of all published notices.</b>
</div>

{{-- নতুন বার্তা --}}
<div class="card card-primary card-outline">
    <div class="card-header"><h3 class="card-title">Add Ticker Message</h3></div>
    <div class="card-body">
        <form action="{{ route('ticker.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <textarea name="message" class="form-control @error('message') is-invalid @enderror"
                          rows="2" maxlength="500" required
                          placeholder="e.g. Mid-term exams start from 20 July. Check the routine.">{{ old('message') }}</textarea>
                @error('message')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <button class="btn btn-primary">
                <i class="fas fa-bullhorn"></i> Add to Live Ticker
            </button>
        </form>
    </div>
</div>

{{-- বার্তার তালিকা --}}
<div class="card">
    <div class="card-header"><h3 class="card-title">Ticker Messages</h3></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Message</th><th>Status</th><th>Added by</th><th>Date</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($messages as $m)
                <tr class="{{ $m->is_active ? '' : 'text-muted' }}">
                    <td>{{ $m->message }}</td>
                    <td>
                        @if($m->is_active)
                            <span class="badge badge-success">LIVE</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $m->creator->name ?? '—' }}</td>
                    <td>{{ $m->created_at->format('d M Y') }}</td>
                    <td>
                        <form action="{{ route('ticker.toggle', $m) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-{{ $m->is_active ? 'warning' : 'success' }}">
                                {{ $m->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        <form action="{{ route('ticker.destroy', $m) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this ticker message?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        No ticker messages. The board is currently showing all notice titles.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@stop