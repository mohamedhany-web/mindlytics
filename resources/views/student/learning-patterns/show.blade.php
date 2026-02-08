@extends('layouts.app')

@section('title', $pattern->title . ' - ' . $course->title)
@section('header', $pattern->title)

@section('content')
@include('student.learning-patterns.partials.pattern-content', ['embed' => false])
@endsection

@push('scripts')
@include('student.learning-patterns.partials.pattern-scripts')
@endpush
