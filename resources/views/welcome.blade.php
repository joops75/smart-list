@extends('layouts.app')

@section('content')
    <div class="jumbotron">
        <div class="container text-left">
            <h1 class="display-3">Welcome to {{ config('app.name', 'Smartlist') }}!</h1>
            <p>A better way to keep track of groups of tasks with a deadline. Create an account where you can make projects with associated tasks. Full viewing, editing and deleting functionality included.</p>
        </div>
    </div>
    
    <div class="container text-left">
        <div class="row">
            <div class="col-md-4">
                <h2>Real-Time Countdown</h2>
                <p>See at a glance how much time remains on a task. Values are dynamic and continually update without refreshing the page.</p>
            </div>
            <div class="col-md-4">
                <h2>Automatic Recording of Interactions</h2>
                <p>Any creation, updating or deletion of projects or tasks are simultaneously recorded as updates which can be seen in their own column. These updates may be deleted if desired.</p>
            </div>
            <div class="col-md-4">
                <h2>Bulk Deleting Available</h2>
                <p>Besides being able to delete individual projects or tasks, there is also the option to delete en masse according to according to their completion status.</p>
            </div>
        </div>
    </div>

@endsection