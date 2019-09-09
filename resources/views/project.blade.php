@extends('layouts.app')

@section('content')
    <vue-project project_json="{{ $project }}" tasks_json="{{ $tasks }}"></vue-project>
@endsection