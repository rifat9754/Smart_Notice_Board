@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_header','')

@section('auth_body')

<div class="text-center mb-4">

    <div class="hero-icon">
        <i class="fas fa-bullhorn"></i>
    </div>

    <h2 class="title">
        KUET CSE
    </h2>

    <h5 class="subtitle">
        Digital Notice Board
    </h5>

    <p class="description">
        Welcome back! Please sign in.
    </p>

</div>

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif


<form method="POST" action="{{ route('login') }}">
@csrf

<div class="input-group mb-3">

<input
type="email"
name="email"
class="form-control @error('email') is-invalid @enderror"
placeholder="Email Address"
value="{{ old('email') }}"
required
autofocus>

<div class="input-group-append">
<div class="input-group-text">
<i class="fas fa-envelope"></i>
</div>
</div>

@error('email')
<span class="invalid-feedback d-block">
{{ $message }}
</span>
@enderror

</div>

<div class="input-group mb-4">

<input
type="password"
name="password"
class="form-control @error('password') is-invalid @enderror"
placeholder="Password"
required>

<div class="input-group-append">
<div class="input-group-text">
<i class="fas fa-lock"></i>
</div>
</div>

@error('password')
<span class="invalid-feedback d-block">
{{ $message }}
</span>
@enderror

</div>

<div class="row align-items-center mb-3">

<div class="col-6">

<div class="icheck-primary">

<input
type="checkbox"
id="remember"
name="remember">

<label for="remember">
Remember Me
</label>

</div>

</div>

<div class="col-6">

<button
type="submit"
class="btn btn-primary btn-block login-btn">

Sign In

</button>

</div>

</div>

@if (Route::has('password.request'))
<p class="text-center mb-4">
    <a class="forgot-link" href="{{ route('password.request') }}">
        Forgot your password?
    </a>
</p>
@endif

</form>

<div class="text-center">

<p>

<a href="{{ route('register') }}">
Don't have an account? Register
</a>

</p>

<p>

<a class="back-home" href="{{ url('/') }}">
← Back to Home
</a>

</p>

</div>

@stop


@section('css')

<style>

body.login-page{

background:
linear-gradient(135deg,#0f172a,#1e3a8a,#2563eb);

overflow:hidden;

position:relative;

}

/* Remove AdminLTE logo */

.login-logo{

display:none!important;

}

/* Floating circles */

body.login-page::before{

content:'';

position:absolute;

width:520px;

height:520px;

background:rgba(255,255,255,.07);

border-radius:50%;

left:-180px;

top:-180px;

filter:blur(25px);

animation:float1 10s ease-in-out infinite;

}

body.login-page::after{

content:'';

position:absolute;

width:420px;

height:420px;

background:rgba(255,255,255,.05);

border-radius:50%;

right:-120px;

bottom:-120px;

filter:blur(30px);

animation:float2 12s ease-in-out infinite;

}

@keyframes float1{

0%,100%{

transform:translateY(0);

}

50%{

transform:translateY(40px);

}

}

@keyframes float2{

0%,100%{

transform:translateX(0);

}

50%{

transform:translateX(-35px);

}

}

.login-box{

width:430px;

}

.login-card-body{

background:rgba(255,255,255,.95);

backdrop-filter:blur(18px);

border-radius:24px;

padding:40px;

border:none;

box-shadow:

0 25px 60px rgba(0,0,0,.35);

}

.hero-icon{

width:90px;

height:90px;

border-radius:50%;

margin:auto;

display:flex;

align-items:center;

justify-content:center;

font-size:38px;

color:white;

background:

linear-gradient(135deg,#2563eb,#3b82f6);

box-shadow:

0 15px 35px rgba(37,99,235,.35);

margin-bottom:18px;

}

.title{

font-weight:800;

color:#1e3a8a;

margin-bottom:5px;

}

.subtitle{

font-weight:600;

color:#334155;

margin-bottom:6px;

}

.description{

color:#64748b;

font-size:14px;

margin-bottom:0;

}

.form-control{

height:48px;

border-radius:12px;

border:1px solid #dbeafe;

}

.form-control:focus{

border-color:#2563eb;

box-shadow:0 0 0 .15rem rgba(37,99,235,.2);

}

.input-group-text{

background:#f8fafc;

border-radius:0 12px 12px 0;

}

.login-btn{

height:46px;

border:none;

font-weight:700;

border-radius:12px;

background:

linear-gradient(135deg,#2563eb,#1d4ed8);

transition:.35s;

}

.login-btn:hover{

transform:translateY(-3px);

box-shadow:

0 15px 30px rgba(37,99,235,.35);

}

.login-btn:active{

transform:scale(.98);

}

/* Forgot password link */

.forgot-link{

font-size:13px;

font-weight:600;

color:#2563eb;

}

.forgot-link:hover{

color:#1d4ed8;

text-decoration:underline!important;

}

.back-home{

color:#64748b;

font-size:13px;

}

.alert{

border-radius:12px;

}

a{

transition:.3s;

}

a:hover{

text-decoration:none;

}

</style>

@stop