@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1 class="mt-3">Applications</h1>
            <div class="card-columns">
            @foreach($applications as $application)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{$application->getName()}}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Import data from {{$application->getName()}}</h6>
                    <a href="{{route('create-application', ['application' => $application->getSlug()])}}" class="btn btn-primary">Connect</a>
                </div>
            </div>
            @endforeach
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1 class="mt-5">My Applications</h1>

            @if(count($applicationConnections) > 0)
            <div class="card-columns">
                    @foreach($applicationConnections as $applicationConnection)
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{$applicationConnection->getApplication()->getName()}} <a href="{{url('/fittie/application/' . $applicationConnection->getID() . '/delete')}}" class="btn btn-sm btn-danger float-right"><i class="fas fa-trash"></i></a> </h5>
                            <h6 class="card-subtitle mb-2 text-muted">View data from {{$applicationConnection->getAccountName() ?: $applicationConnection->getAccountID()}}</h6>
                            @foreach($applicationConnection->getApplication()->availableMetricTypes() as $metricType)
                                <a href="{{url('/fittie/analytics/' . $metricType->getName() . '/' . $applicationConnection->getID())}}" class="btn btn-primary">{{$metricType->getName()}}</a>
                            @endforeach

                        </div>
                    </div>
                    @endforeach
            </div>
            @else
            <p>No applications connected</p>
            @endif
        </div>
    </div>
</div>
@endsection
