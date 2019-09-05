@extends('layouts.app')

@section('content')
    <vue-projects projects_json="{{ $projects }}"></vue-projects>
    {{-- <h2>My Projects</h2>

    <project-buttons></project-buttons>

    @if (count($projects))
        <projects-grid projects_json="{{ $projects }}"></projects-grid>
    @else
        <div>No projects saved.</div>
    @endif --}}

    {{-- @forelse ($projects as $project)
        <div>
            {{ $project->title }}
        </div>
    @empty
        <div>No projects saved.</div>
    @endforelse --}}
@endsection