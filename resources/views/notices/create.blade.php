@extends('adminlte::page')
@section('title', 'New Notice')
@section('content_header')<h1>Create Notice</h1>@stop
@section('content')
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <div class="card"><div class="card-body">
        <form action="{{ route('notices.store') }}" method="POST" enctype="multipart/form-data">
            @include('notices._form')
            <button type="submit" class="btn btn-primary">Save Notice</button>
            <a href="{{ route('notices.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
@stop