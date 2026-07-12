@extends('adminlte::page')
@section('title', 'Year Promotion')
@section('content_header')<h1>Student Year Promotion</h1>@stop
@section('content')

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Bulk promotion --}}
    <div class="card card-primary card-outline">
        <div class="card-header"><h3 class="card-title">Bulk Promotion</h3></div>
        <div class="card-body">
            <p class="text-muted">Promote all students of a year to the next year at once.</p>

<div class="row">
                {{-- 1st, 2nd, 3rd — promote --}}
                @foreach(['1st' => '2nd', '2nd' => '3rd', '3rd' => '4th'] as $from => $to)
                    <div class="col-md-3 mb-3">
                        <div class="card bg-light h-100">
                            <div class="card-body text-center">
                                <h4>{{ $from }} Year</h4>
                                <p class="mb-2"><span class="badge badge-info">{{ $counts[$from] }} students</span></p>
                                <form action="{{ route('promotion.promoteAll') }}" method="POST"
                                      onsubmit="return confirm('Promote all {{ $from }} year students to {{ $to }} year?')">
                                    @csrf
                                    <input type="hidden" name="from_year" value="{{ $from }}">
                                    <button class="btn btn-primary btn-sm" {{ $counts[$from] == 0 ? 'disabled' : '' }}>
                                        Promote to {{ $to }} Year
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- 4th year — delete (graduated) --}}
                <div class="col-md-3 mb-3">
                    <div class="card bg-light h-100 border-danger">
                        <div class="card-body text-center">
                            <h4>4th Year</h4>
                            <p class="mb-2"><span class="badge badge-danger">{{ $counts['4th'] }} students</span></p>
                            <form action="{{ route('promotion.deleteFinalYear') }}" method="POST"
                                  onsubmit="return confirm('Delete ALL 4th year students? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" {{ $counts['4th'] == 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-user-graduate"></i> Remove Graduates
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning mb-0">
                <b>Important:</b> Promote in reverse order — first remove 4th year graduates,
                then 3rd → 4th, then 2nd → 3rd, then 1st → 2nd.
                This prevents students from skipping a year. Class representatives are promoted along with their year.
            </div>

            <div class="alert alert-warning mb-0">
                <b>4th Year:</b> {{ $counts['4th'] }} students.
                Promote in order — start from 3rd → 4th, then 2nd → 3rd, then 1st → 2nd,
                to avoid students skipping a year.
            </div>
        </div>
    </div>

    {{-- Individual update --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Individual Update</h3></div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th><th>Email</th><th>Role</th>
                        <th>Year</th><th>Section</th><th>Update</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($students as $s)
                    <tr>
                        <td>{{ $s->name }}</td>
                        <td>{{ $s->email }}</td>
                        <td><span class="badge badge-secondary">{{ strtoupper($s->role) }}</span></td>
                        <td>{{ $s->year ?? '—' }}</td>
                        <td>{{ $s->section ?? '—' }}</td>
                        <td>
                            <form action="{{ route('promotion.updateStudent', $s) }}" method="POST" class="form-inline">
                                @csrf @method('PUT')
                                <select name="year" class="form-control form-control-sm mr-1" required>
                                    @foreach(['1st','2nd','3rd','4th'] as $y)
                                        <option value="{{ $y }}" {{ $s->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                                <select name="section" class="form-control form-control-sm mr-1" required>
                                    @foreach(['A','B'] as $sec)
                                        <option value="{{ $sec }}" {{ $s->section == $sec ? 'selected' : '' }}>{{ $sec }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-success">Save</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No students yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@stop