@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1>Create credentials for {{$application->getName()}}</h1>

            {{ Form::open() }}
            {{ Form::text('username') }}
            {{ Form::password('password') }}
            {{ Form::submit('submit') }}
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection
