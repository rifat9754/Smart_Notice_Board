
@extends('adminlte::page')

@section('title', 'My Notices')

@section('content_header')
    <h1>My Class Notices</h1>
@stop

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('cr.create') }}" class="btn btn-primary mb-3">
        + Post New Notice
    </a>

    <div class="card">
        <div class="card-body table-responsive p-0">

            <table class="table table-hover">

                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Class</th>
                        <th>Priority</th>
                        <th>Notified Teacher</th>
                        <th>Seen?</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($notices as $n)

                    @php
                        $c = [
                            'high' => 'danger',
                            'medium' => 'warning',
                            'low' => 'info'
                        ][$n->priority] ?? 'secondary';
                    @endphp

                    <tr>

                        <td>{{ $n->title }}</td>

                        <td>
                            {{ $n->year }} - {{ $n->section }}
                        </td>

                        <td>
                            <span class="badge badge-{{ $c }}">
                                {{ strtoupper($n->priority) }}
                            </span>
                        </td>

                        <td>
                            {{ $n->notifiedTeacher->name ?? '—' }}
                        </td>

                        <td>
                            @if($n->notified_teacher_id)
                                <span class="badge badge-{{ $n->notified_seen ? 'success' : 'secondary' }}">
                                    {{ $n->notified_seen ? 'Seen' : 'Pending' }}
                                </span>
                            @else
                                —
                            @endif
                        </td>

                        <td>
                            <form action="{{ route('cr.destroy', $n) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this notice?')">

                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-danger">
                                    Delete
                                </button>

                            </form>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="text-center">
                            You haven't posted any notice yet.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>
    </div>

@stop

