@extends('adminlte::page')
@section('title', 'Edit Notice for Teachers')
@section('content_header')<h1>Edit Notice for Teachers</h1>@stop
@section('content')

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="card card-primary card-outline">
    <div class="card-body">
        <form action="{{ route('teacher-notices.update', $notice) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" maxlength="255"
                       value="{{ old('title', $notice->title) }}" required>
            </div>

            <div class="form-group">
                <label>Message</label>
                <textarea name="body" class="form-control" rows="4" required>{{ old('body', $notice->body) }}</textarea>
            </div>

<div class="form-group">
                <label>Priority</label>
                <select name="priority" class="form-control" required>
                    @foreach(['high','medium','low']as $p)
                        <option value="{{ $p }}" {{ old('priority', $notice->priority) == $p ? 'selected': '' }}>
                            {{ ucfirst($p) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Display Board Summary <small class="text-muted">(optional, one line)</small></label>
                <input type="text" name="display_line" class="form-control @error('display_line') is-invalid @enderror"
                       value="{{ old('display_line', $notice->display_line) }}" maxlength="120"
                       placeholder="Short one-line summary for the display board">
                <small class="text-muted">
                    Shown on the "Notice for Teachers" panel of the display board. If empty, the title is shown.
                </small>
                @error('display_line')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>

            <button class="btn btn-primary"><i class="fas fa-save"></i> Update Notice</button>
            <a href="{{ route('teacher-notices.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@stop