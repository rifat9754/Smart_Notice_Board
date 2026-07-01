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
<div class="form-group">
    <label>Year</label>
    <select name="year" class="form-control" required>
        <option value="1st">1st Year</option>
        <option value="2nd">2nd Year</option>
        <option value="3rd">3rd Year</option>
        <option value="4th">4th Year</option>
    </select>
</div>

<div class="form-group">
    <label>Section</label>
    <select name="section" class="form-control" required>
        <option value="A">Section A</option>
        <option value="B">Section B</option>
    </select>
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