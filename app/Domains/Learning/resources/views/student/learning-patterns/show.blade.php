@extends('layouts.student-dashboard')

@section('title', $pattern->title . ' - ' . $course->title)
@section('header', $pattern->title)

@section('content')
@include('learning::student.learning-patterns.partials.pattern-content', ['embed' => false])
@endsection

@push('scripts')
@include('learning::student.learning-patterns.partials.pattern-scripts')
@endpush
