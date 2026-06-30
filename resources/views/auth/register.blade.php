@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('auth_header', 'Create an Account')

@section('auth_body')
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            <small class="form-text text-muted">Students must use a <b>@stud.kuet.ac.bd</b> email.</small>
        </div>

        <div class="form-group">
            <label>Register as</label>
            <select name="role" class="form-control" required>
                <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
            </select>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Register</button>
    </form>
@stop

@section('auth_footer')
    <p class="my-0 text-center">
        <a href="{{ route('login') }}">Already have an account? Sign in</a>
    </p>
@stop