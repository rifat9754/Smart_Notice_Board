@extends('adminlte::master')
@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')
@inject('preloaderHelper', 'JeroenNoten\LaravelAdminLte\Helpers\PreloaderHelper')
@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop
@section('classes_body', $layoutHelper->makeBodyClasses())
@section('body_data', $layoutHelper->makeBodyData())
@section('body')
    <div class="wrapper">
        {{-- Preloader Animation (fullscreen mode) --}}
        @if($preloaderHelper->isPreloaderEnabled())
            @include('adminlte::partials.common.preloader')
        @endif
        {{-- Top Navbar --}}
        @if($layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.navbar.navbar-layout-topnav')
        @else
            @include('adminlte::partials.navbar.navbar')
        @endif
        {{-- Left Main Sidebar --}}
        @if(!$layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.sidebar.left-sidebar')
        @endif
        {{-- Content Wrapper --}}
        @empty($iFrameEnabled)
            @include('adminlte::partials.cwrapper.cwrapper-default')
        @else
            @include('adminlte::partials.cwrapper.cwrapper-iframe')
        @endempty
        {{-- Footer (custom — always shown) --}}
        <footer class="main-footer">
            <strong>&copy; {{ date('Y') }} Department of CSE, KUET.</strong>
            Innovation in Every Notice &bull; Developed by Ahanaf Tahmid Rifat
            <a href="https://github.com/rifat9754" target="_blank" rel="noopener" class="ml-2">
                <i class="fab fa-github"></i> GitHub
            </a>
        </footer>
        {{-- Right Control Sidebar --}}
        @if($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.sidebar.right-sidebar')
        @endif
    </div>
@stop
@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop