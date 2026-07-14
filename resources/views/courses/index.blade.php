@extends('adminlte::page')
@section('title', 'Courses')
@section('content_header')<h1>Courses</h1>@stop
@section('content')

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<a href="{{ route('courses.create') }}" class="btn btn-primary mb-3">
    <i class="fas fa-plus"></i> Add Course
</a>

<div class="card">
    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Course No</th><th>Course Title</th><th>Teachers</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($courses as $c)
                <tr>
                    <td><b>{{ $c->course_no }}</b></td>
                    <td>{{ $c->course_title }}</td>
                    <td>
                        @foreach($c->teachers as $t)
                            <span class="badge badge-info">{{ $t->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('courses.edit', $c) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('courses.destroy', $c) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this course?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No courses yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop