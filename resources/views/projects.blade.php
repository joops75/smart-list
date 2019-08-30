@extends('layouts.app')

@section('content')
    @forelse ($projects as $project)
        <div>
            {{ $project->title }}
        </div>
    @empty
        <div>No projects saved</div>
    @endforelse
@endsection