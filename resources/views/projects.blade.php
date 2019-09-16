@extends('layouts.app')

@section('content')
    <vue-projects projects_json="{{ $projects }}" get_type="{{ $getType }}"></vue-projects>
@endsection