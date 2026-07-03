@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('auth_header','')

@section('css')
<style>

html,
body{
    height:100%;
}

/* Gradient forced on both body and the .register-page wrapper,
   because AdminLTE sometimes puts .register-page on a wrapper div
   instead of the body. This guarantees the blue gradient shows. */
body,
.register-page{
    background:
        radial-gradient(circle at 78% 78%, rgba(90,130,255,.35) 0%, transparent 45%),
        linear-gradient(135deg, #14213f 0%, #1e3a8a 50%, #3454d4 100%) !important;
    background-attachment:fixed !important;
    background-size:cover !important;
}

.register-page{
    width:100%;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:24px 0;
}

/* Hide AdminLTE's default logo/brand block above the card */
.register-logo,
.login-logo{
    display:none !important;
}

.register-box{
    width:960px !important;
    max-width:96% !important;
}

/* AdminLTE er nijer .card width override kore box er full width nite bolchi */
.register-box > .card,
.register-box .card{
    width:100% !important;
    max-width:100% !important;
}

.register-card-body{
    border-radius:18px;
    background:#fff;
    padding:22px 34px;   /* vertical padding komano holo -> card chapta */
    border-top:4px solid #2563eb;
    box-shadow:0 18px 55px rgba(0,0,0,.22);
}

.brand-circle{
    width:66px;
    height:66px;
    border-radius:50%;
    margin:auto;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:#fff;
    font-size:28px;
    box-shadow:0 10px 26px rgba(37,99,235,.35);
}

.main-title{
    font-size:30px;
    font-weight:800;
    color:#1e40af;
    text-align:center;
    margin-top:10px;
}

.sub-title{
    font-size:16px;
    text-align:center;
    color:#334155;
    font-weight:700;
}

.sub-text{
    text-align:center;
    color:#64748b;
    margin-bottom:16px;
}

/* protita field er niche gap komano -> card aro chapta */
.form-group{
    margin-bottom:12px;
}

.form-group label{
    font-weight:700;
    color:#334155;
    margin-bottom:3px;
}

.form-control,
.custom-select{
    height:44px;
    border-radius:10px;
    font-size:15px;
    background:#f8fafc;
    border:1px solid #e2e8f0;
    transition:border-color .2s, box-shadow .2s, background .2s;
}

.form-control::placeholder{
    color:#94a3b8;
}

.form-control:focus,
.custom-select:focus{
    background:#fff;
    border-color:#2563eb;
    box-shadow:0 0 0 3px rgba(37,99,235,.15);
}

/* Email hint ke compact + ek line e rakhi -> boro gap chole jabe */
.form-group small.text-muted,
.form-group small{
    display:block;
    margin-top:5px;
    font-size:12px;
    line-height:1.3;
    color:#94a3b8 !important;
}

.btn-register{
    height:50px;
    border:none;
    border-radius:10px;
    color:#fff;
    font-weight:700;
    font-size:17px;
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    transition:.3s;
}

.btn-register:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 25px rgba(37,99,235,.35);
}

.divider{
    display:flex;
    align-items:center;
    margin:16px 0;
}

.divider::before,
.divider::after{
    content:"";
    flex:1;
    height:1px;
    background:#dbeafe;
}

.divider span{
    margin:0 15px;
    color:#64748b;
}

.bottom-link{
    text-align:center;
    margin-bottom:12px;
}

.bottom-link a{
    font-weight:700;
}

.home-link{
    text-align:center;
}

.home-link a{
    color:#64748b;
}

@media(max-width:768px){

    .register-box{
        width:96%!important;
    }

    .register-card-body{
        padding:22px;
    }

    .main-title{
        font-size:28px;
    }

    body.register-page{
        overflow:auto;
    }

}

</style>
@stop

@section('auth_body')

<div class="brand-circle">
    <i class="fas fa-user-plus"></i>
</div>

<div class="main-title">
    KUET CSE
</div>

<div class="sub-title">
    Digital Notice Board
</div>

<div class="sub-text">
    Create your account
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="row">

        <div class="col-md-6">
            <div class="form-group">
                <label>Full Name</label>
                <input
                    type="text"
                    name="name"
                    class="form-control"
                    placeholder="Enter your full name"
                    value="{{ old('name') }}"
                    required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Email Address</label>
                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="example@stud.kuet.ac.bd"
                    value="{{ old('email') }}"
                    required>
                <small class="text-muted">
                    Students must use <strong>@stud.kuet.ac.bd</strong>
                </small>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Register As</label>
                <select
                    name="role"
                    class="custom-select"
                    required>
                    <option value="student">🎓 Student</option>
                    <option value="teacher">👨‍🏫 Teacher</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Password</label>
                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Enter password"
                    required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label>Confirm Password</label>
                <input
                    type="password"
                    name="password_confirmation"
                    class="form-control"
                    placeholder="Confirm password"
                    required>
            </div>
        </div>

        <div class="col-md-12">
            <button class="btn btn-register btn-block">
                <i class="fas fa-user-plus mr-2"></i>
                Create Account
            </button>
        </div>

    </div>
</form>

<div class="divider">
    <span>OR</span>
</div>

<div class="bottom-link">
    Already have an account?
    <a href="{{ route('login') }}">Sign In</a>
</div>

<div class="home-link">
    <a href="{{ url('/') }}">← Back to Home</a>
</div>

@stop