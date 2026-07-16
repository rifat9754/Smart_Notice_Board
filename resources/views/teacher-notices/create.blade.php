@extends('adminlte::page')
@section('title', 'Notice for Teachers')
@section('content_header')<h1>Send Notice to Teachers</h1>@stop
@section('content')

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="alert alert-info">
    <i class="fas fa-info-circle"></i>
    This notice will be shown to <b>all teachers</b> — in the "Notice for Teachers" panel on the display board
    and in the teachers' mobile app. Students will not see it.
</div>

<div class="card card-primary card-outline">
    <div class="card-body">
        <form action="{{ route('teacher-notices.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" maxlength="255"
                       value="{{ old('title') }}" placeholder="e.g. Faculty meeting on 20 July" required>
            </div>

            <div class="form-group">
                <label>Message</label>
                <textarea name="body" class="form-control" rows="4" required
                          placeholder="Write the notice for the teachers…">{{ old('body') }}</textarea>
            </div>

<div class="form-group">
                <label>Priority</label>
                <select name="priority" class="form-control" required>
                    <option value="high"   {{ old('priority')=='high'?'selected':'' }}>High</option>
                    <option value="medium" {{ old('priority','medium')=='medium'?'selected':'' }}>Medium</option>
                    <option value="low"    {{ old('priority')=='low'?'selected':'' }}>Low</option>
                </select>
            </div>

            <div class="form-group">
                <label>Display Board Summary <small class="text-muted">(optional, one line)</small></label>
                <input type="text" name="display_line" class="form-control @error('display_line') is-invalid @enderror"
                       value="{{ old('display_line') }}" maxlength="120"
                       placeholder="Short one-line summary for the display board">
                @error('display_line')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>

            <button class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Send to Teachers
            </button>
            <a href="{{ route('teacher-notices.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@stop