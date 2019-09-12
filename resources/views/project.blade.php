@extends('layouts.app')

@section('content')
    <vue-project project_json="{{ $project }}" tasks_json="{{ $tasks }}" get_type="{{ $getType }}"></vue-project>
@endsection