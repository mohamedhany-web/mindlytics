@extends('layouts.embed')

@section('title', $pattern->title)

@section('content')
@include('student.learning-patterns.partials.pattern-content', ['embed' => true])
@endsection

@push('scripts')
@include('student.learning-patterns.partials.pattern-scripts')
@endpush
