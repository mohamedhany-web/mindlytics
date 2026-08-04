@extends('layouts.employee')

@section('title', 'تقويم شيفت الفريق')
@section('header', 'تقويم الشيفت والإجازات — الفريق')

@section('content')
<div class="space-y-6">
    @include('sales._schedule_calendar_grid', [
        'grid' => $grid,
        'weekStart' => $weekStart,
        'weekEnd' => $weekEnd,
        'prevWeek' => $prevWeek,
        'nextWeek' => $nextWeek,
        'scopeLabel' => $scopeLabel,
        'routeName' => $routeName,
    ])
</div>
@endsection
