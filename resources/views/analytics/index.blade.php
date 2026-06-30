@extends('adminlte::page')
@section('title', 'Analytics')
@section('content_header')<h1>Analytics</h1>@stop
@section('content')
    <div class="row">
        <div class="col-md-3"><div class="small-box bg-info"><div class="inner"><h3>{{ $totalViews }}</h3><p>Total Views</p></div></div></div>
        <div class="col-md-3"><div class="small-box bg-success"><div class="inner"><h3>{{ $activeCount }}</h3><p>Active Notices</p></div></div></div>
        <div class="col-md-3"><div class="small-box bg-secondary"><div class="inner"><h3>{{ $expiredCount }}</h3><p>Expired Notices</p></div></div></div>
        <div class="col-md-3"><div class="small-box bg-warning"><div class="inner"><h3>{{ $totalNotices }}</h3><p>Total Notices</p></div></div></div>
    </div>
    <div class="row">
        <div class="col-md-6"><div class="card"><div class="card-header">Most Viewed Notices</div>
            <div class="card-body"><canvas id="mostViewedChart" height="200"></canvas></div></div></div>
        <div class="col-md-6"><div class="card"><div class="card-header">Views Over Time</div>
            <div class="card-body"><canvas id="viewsByDayChart" height="200"></canvas></div></div></div>
    </div>
@stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        new Chart(document.getElementById('mostViewedChart'), {
            type: 'bar',
            data: {
                labels: @json($mostViewed->pluck('title')),
                datasets: [{ label: 'Views', data: @json($mostViewed->pluck('views_count')) }]
            },
            options: { indexAxis: 'y' }
        });
        new Chart(document.getElementById('viewsByDayChart'), {
            type: 'line',
            data: {
                labels: @json($viewsByDay->pluck('day')),
                datasets: [{ label: 'Views', data: @json($viewsByDay->pluck('total')) }]
            }
        });
    </script>
@stop