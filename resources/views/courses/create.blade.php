@extends('adminlte::page')
@section('title', 'Add Course')
@section('content_header')<h1>Add Course</h1>@stop
@section('content')

<div class="card card-primary card-outline">
    <div class="card-body">
        <form action="{{ route('courses.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Course No</label>
                <input type="text" name="course_no" class="form-control @error('course_no') is-invalid @enderror"
                       value="{{ old('course_no') }}" placeholder="e.g. CSE-3101" required>
                @error('course_no')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Course Title</label>
                <input type="text" name="course_title" class="form-control @error('course_title') is-invalid @enderror"
                       value="{{ old('course_title') }}" placeholder="e.g. Database Systems" required>
                @error('course_title')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Teachers <small class="text-muted">(select one or more)</small></label>
                <select name="teachers[]" class="form-control" multiple size="8" required>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->email }})</option>
                    @endforeach
                </select>
                <small class="text-muted">Hold Ctrl (Cmd on Mac) to select multiple teachers.</small>
                @error('teachers')<span class="text-danger d-block">{{ $message }}</span>@enderror
            </div>

            <button class="btn btn-primary">Save Course</button>
            <a href="{{ route('courses.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@stop