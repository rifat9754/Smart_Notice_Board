@extends('adminlte::page')
@section('title', 'Edit Course')
@section('content_header')<h1>Edit Course</h1>@stop
@section('content')

<div class="card card-primary card-outline">
    <div class="card-body">
        <form action="{{ route('courses.update', $course) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Course No</label>
                <input type="text" name="course_no" class="form-control @error('course_no') is-invalid @enderror"
                       value="{{ old('course_no', $course->course_no) }}" required>
                @error('course_no')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Course Title</label>
                <input type="text" name="course_title" class="form-control @error('course_title') is-invalid @enderror"
                       value="{{ old('course_title', $course->course_title) }}" required>
                @error('course_title')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Teachers <small class="text-muted">(select one or more)</small></label>
                <select name="teachers[]" class="form-control" multiple size="8" required>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" {{ $course->teachers->contains($t->id) ? 'selected' : '' }}>
                            {{ $t->name }} ({{ $t->email }})
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Hold Ctrl (Cmd on Mac) to select multiple teachers.</small>
                @error('teachers')<span class="text-danger d-block">{{ $message }}</span>@enderror
            </div>

            <button class="btn btn-primary">Update Course</button>
            <a href="{{ route('courses.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@stop