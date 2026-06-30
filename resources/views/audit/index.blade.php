@extends('adminlte::page')
@section('title', 'Audit Log')
@section('content_header')<h1>Audit Log</h1>@stop
@section('content')
    <div class="card"><div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <thead><tr><th>User</th><th>Action</th><th>Target</th><th>Time</th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->user->name ?? 'Unknown' }}</td>
                        <td><span class="badge badge-info">{{ $log->action }}</span></td>
                        <td>{{ $log->target_type }} #{{ $log->target_id }}</td>
                        <td>{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No activity yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div></div>
    {{ $logs->links() }}
@stop