@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Department Notice Board — Dashboard</h1>
@stop

@section('content')
    <p>
        Welcome,
        {{ auth()->user()->name }}
        ({{ auth()->user()->role }})
    </p>
@stop