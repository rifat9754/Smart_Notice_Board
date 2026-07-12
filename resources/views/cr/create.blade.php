@extends('adminlte::page')
@section('title', 'Post Notice')
@section('content_header')<h1>Post a Class Notice</h1>@stop
@section('content')
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="card"><div class="card-body">
        <form action="{{ route('cr.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea name="body" class="form-control" rows="4" required>{{ old('body') }}</textarea>
            </div>
            <div class="form-group">
                <label>Priority</label>
                <select name="priority" class="form-control" required>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
<div class="alert alert-info">
<i class="fas fa-info-circle"></i>
This notice will be posted for <b>{{ auth()->user()->year ?? '—' }} Year, Section {{ auth()->user()->section ?? '—' }}</b> automatically.
</div>          
            <div class="form-group">
                <label>Notify a Teacher (optional)</label>
                <select name="notified_teacher_id" class="form-control">
                    <option value="">— No specific teacher —</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Selected teacher will be notified about this notice.</small>
            </div>
            <button type="submit" class="btn btn-primary">Post Notice</button>
            <a href="{{ route('cr.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
@stop