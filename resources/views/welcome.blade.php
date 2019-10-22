@extends('layouts.app')

@section('content')
    <div class="jumbotron">
        <div class="container text-left">
            <h1 class="display-3">Welcome to {{ config('app.name', 'Smartlist') }}!</h1>
            <p>A better way to keep track of groups of tasks with a deadline. Create an account where you can make projects with associated tasks. Full viewing, editing and deleting functionality included.</p>
            @auth
                <p><a class="btn btn-primary btn-lg" href="{{ route('project.index') }}" role="button">Go to My Projects &raquo;</a></p>
            @endauth
            @guest
                <p><a class="btn btn-primary btn-lg" href="{{ route('login') }}" role="button">Have an account? Login &raquo;</a></p>
                <p><a class="btn btn-primary btn-lg" href="{{ route('register') }}" role="button">No account? Register &raquo;</a></p>
            @endguest
        </div>
    </div>
    
    <div class="container text-left">
        <div class="row">
            <div class="col-md-4">
                <h2><i class="fas fa-clock"></i> Real-Time Countdown</h2>
                <p>See at a glance how much time remains on a task. Values are dynamic and continually update without refreshing the page.</p>
            </div>
            <div class="col-md-4">
                <h2><i class="fas fa-pencil-alt"></i> Automatic Recording of Interactions</h2>
                <p>Any creation, updating or deletion of projects or tasks are simultaneously recorded as updates which can be seen in their own column. These updates may be deleted if desired.</p>
            </div>
            <div class="col-md-4">
                <h2><i class="fas fa-trash-alt"></i> Bulk Deleting Available</h2>
                <p>Besides being able to delete individual projects or tasks, there is also the option to delete en masse according to their completion status.</p>
            </div>
        </div>
    </div>

@endsection