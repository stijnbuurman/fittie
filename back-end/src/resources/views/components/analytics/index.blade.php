@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{$dataset->getMetric()->getName()}} ({{$metricRequest->getStart()->format('Y-m-d H:i:s')}} - {{$metricRequest->getEnd()->format('Y-m-d H:i:s')}})</div>

                <div class="card-body">
                    <div id="date-range-selector" start="{{$metricRequest->getStart()->getTimestamp() * 1000}}" end="{{$metricRequest->getEnd()->getTimestamp() * 1000}}"></div>

                    <div style="width:100%;">
                        <canvas id="canvas"></canvas>
                    </div>
                    <script>
                        var timeFormat = 'MM/DD/YYYY HH:mm';

                        var color = Chart.helpers.color;
                        var config = {
                            type: @if (($dataset->getMetric() instanceof Fittie\Component\Analytics\MetricOverTime)) 'bar', @else 'line', @endif
                            data: {
                                datasets: [{
                                    label: '{{$dataset->getMetric()->getName()}}',
                                    backgroundColor: 'rgba(255, 99, 132, 1)',
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    borderWidth: 1,
                                    // fill: false,
                                    data: @json(array_map(fn($measurement) => ['x' => $measurement['start'] * 1000, 'y' => $measurement['value']], $dataset->toArray()))
                                }]
                            },
                            options: {
                                title: {
                                    text: 'Chart.js Time Scale'
                                },
                                scales: {
                                    xAxes: [{
                                        type: 'time',
                                        time: {
                                            parser: timeFormat,
                                            tooltipFormat: 'll HH:mm',
                                            @if ($dataset->getMetric() instanceof Fittie\Component\Analytics\MetricOverTime)
                                                @if($dataset->getMetric()->getTimeBucketSeconds() < 60)
                                                unit: 'second',
                                                stepSize: {{$dataset->getMetric()->getTimeBucketSeconds()}},
                                                @elseif($dataset->getMetric()->getTimeBucketSeconds() < 60 * 60)
                                                unit: 'minute',
                                                stepSize: {{$dataset->getMetric()->getTimeBucketSeconds() / 60}},
                                                @elseif($dataset->getMetric()->getTimeBucketSeconds() < 60 * 60 * 24)
                                                unit: 'hour',
                                                stepSize: {{$dataset->getMetric()->getTimeBucketSeconds() / 60 / 60}},
                                                @else
                                                unit: 'day',
                                                stepSize: {{$dataset->getMetric()->getTimeBucketSeconds() / 60 / 60 / 24}},
                                                @endif
                                            @endif
                                            min: {{$metricRequest->getStart()->getTimestamp() * 1000}},
                                            max: {{$metricRequest->getEnd()->getTimestamp() * 1000}}
                                        },
                                        scaleLabel: {
                                            display: true,
                                            labelString: 'Date'
                                        },
                                        ticks: {
                                            major: {
                                                enabled: true,
                                                fontStyle: 'bold'
                                            },
                                            autoSkip: true,
                                            autoSkipPadding: 20,
                                            maxRotation: 0,
                                        },
                                    }],
                                    yAxes: [{
                                        scaleLabel: {
                                            display: true,
                                            labelString: '{{$dataset->getMetric()->getUnit()}}'
                                        }
                                    }]
                                },
                            }
                        };

                        window.onload = function() {
                            var ctx = document.getElementById('canvas').getContext('2d');
                            window.myLine = new Chart(ctx, config);

                        };
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
