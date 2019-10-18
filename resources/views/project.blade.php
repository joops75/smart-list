@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <vue-project project_json="{{ $project }}" tasks_json="{{ $tasks }}" get_type="{{ $getType }}"></vue-project>
    </div>
@endsection