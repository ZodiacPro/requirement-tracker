@extends('layouts.app', ['pageSlug' => 'dashboard'])

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card card-chart">
                <div class="card-header">
                    <h5 class="card-category">Pending</h5>
                    <h3 class="card-title"><i class="tim-icons icon-bell-55 text-primary"></i>{{$total_pending}}</h3>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="chartLinePurple"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-chart">
                <div class="card-header">
                    <h5 class="card-category">Approved</h5>
                    <h3 class="card-title"><i class="tim-icons icon-delivery-fast text-success"></i>{{$total_approved}}</h3>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="activechart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-chart">
                <div class="card-header">
                    <h5 class="card-category">Rejected</h5>
                    <h3 class="card-title"><i class="tim-icons icon-send text-danger"></i>{{$total_rejected}}</h3>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="chartLineGreen"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script src="{{ asset('black') }}/js/plugins/chartjs.min.js"></script>
    <script>
        $(document).ready(function() {
            graph.initDashboardPageCharts({!!json_encode($chart)!!});
        });
    </script>
@endpush
