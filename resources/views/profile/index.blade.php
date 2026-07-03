@extends('adminlte::page')
@section('title', 'Profile')
@section('content_header')<h1>My Profile</h1>@stop
@section('content')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="card card-primary card-outline">
        <div class="card-body box-profile">
            <div class="text-center">
                <div style="width:100px;height:100px;border-radius:50%;background:#1e3a8a;color:#fff;
                            display:flex;align-items:center;justify-content:center;font-size:40px;margin:0 auto;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            </div>
            <h3 class="profile-username text-center mt-3">{{ $user->name }}</h3>
            <p class="text-muted text-center">
                <span class="badge badge-info">{{ str_replace('_', ' ', $user->role) }}</span>
            </p>

            <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item"><b>Email</b> <span class="float-right">{{ $user->email }}</span></li>
                <li class="list-group-item"><b>Status</b> <span class="float-right">{{ ucfirst($user->status) }}</span></li>
                <li class="list-group-item"><b>Joined</b> <span class="float-right">{{ $user->created_at->format('d M Y') }}</span></li>
            </ul>

            <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-block">Edit Profile</a>
        </div>
    </div>
@stop