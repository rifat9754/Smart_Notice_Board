```blade
@extends('adminlte::page')

@section('title', 'Approvals')

@section('content_header')
    <h1>User Approvals</h1>
@stop

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- Pending Requests --}}
    <div class="card card-warning">

        <div class="card-header">
            <h3 class="card-title">Pending Requests</h3>
        </div>

        <div class="card-body table-responsive p-0">

            <table class="table table-hover">

                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Requested Role</th>
                        <th>Approve As</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($pending as $u)

                    <tr>

                        <td>{{ $u->name }}</td>

                        <td>{{ $u->email }}</td>

                        <td>
                            <span class="badge badge-secondary">
                                {{ $u->role }}
                            </span>
                        </td>

                        <td>

                            <form action="{{ route('approvals.approve',$u) }}"
                                  method="POST"
                                  class="form-inline">

                                @csrf

                                <select name="role"
                                        class="form-control form-control-sm mr-2">

                                    <option value="{{ $u->role }}">
                                        {{ $u->role }}
                                    </option>

                                    <option value="teacher">teacher</option>
                                    <option value="student">student</option>
                                    <option value="super_admin">super_admin</option>

                                </select>

                                <button class="btn btn-success btn-sm">
                                    Approve
                                </button>

                            </form>

                        </td>

                        <td>

                            <form action="{{ route('approvals.reject',$u) }}"
                                  method="POST"
                                  onsubmit="return confirm('Reject this request?')">

                                @csrf

                                <button class="btn btn-danger btn-sm">
                                    Reject
                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5" class="text-center">
                            No pending requests.
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>



    {{-- All Users --}}

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">All Users</h3>
        </div>

        <div class="card-body table-responsive p-0">

            <table class="table table-hover">

                <thead>

                <tr>

                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>

                </tr>

                </thead>

                <tbody>

                @foreach($all as $u)

                    <tr>

                        <td>{{ $u->name }}</td>

                        <td>{{ $u->email }}</td>

                        <td>

                            <span class="badge badge-primary">
                                {{ $u->role }}
                            </span>

                        </td>

                        <td>

                            <span class="badge badge-{{ $u->status=='active' ? 'success' : 'secondary' }}">
                                {{ $u->status }}
                            </span>

                        </td>

                        <td>

                            {{-- Activate / Deactivate --}}

                            @if($u->status == 'active')

                                <form action="{{ route('approvals.deactivate',$u) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf

                                    <button class="btn btn-warning btn-sm">
                                        Deactivate
                                    </button>

                                </form>

                            @else

                                <form action="{{ route('approvals.activate',$u) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf

                                    <button class="btn btn-info btn-sm">
                                        Activate
                                    </button>

                                </form>

                            @endif



                            {{-- Make CR --}}

                            @if($u->role === 'student')

                                <form action="{{ route('approvals.makeCr',$u) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf

                                    <button class="btn btn-primary btn-sm">
                                        Make CR
                                    </button>

                                </form>



                            {{-- Remove CR --}}

                            @elseif($u->role === 'cr')

                                <form action="{{ route('approvals.removeCr',$u) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf

                                    <button class="btn btn-outline-secondary btn-sm">
                                        Remove CR
                                    </button>

                                </form>

                            @endif

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    </div>

@stop
```
