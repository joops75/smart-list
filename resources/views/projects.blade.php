@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <vue-projects projects_json="{{ $projects }}" get_type="{{ $getType }}"></vue-projects>
    </div>
@endsection