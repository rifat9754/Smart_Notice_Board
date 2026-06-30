@extends('adminlte::page')
@section('title', 'Edit Notice')
@section('content_header')<h1>Edit Notice</h1>@stop
@section('content')
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <div class="card"><div class="card-body">
        <form action="{{ route('notices.update', $notice) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @include('notices._form')
            <button type="submit" class="btn btn-primary">Update Notice</button>
            <a href="{{ route('notices.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
@stop