@extends('layouts.master')

@section('title', 'Workout Templates')

@section('content')
<h2>Workout Templates</h2>
<p>Preset workouts for you to browse</p>
@include('common.beta')

@if ($active_template != null)
<h2>Active Template: <a href="{{ route('viewTemplate', ['template_id' => $active_template->template_id]) }}">{{ $active_template->template_name }}</a></h2>
<a href="{{ route('buildActiveTemplate') }}" class="btn btn-success">Generate Next Template</a>
@endif

@php
	$group_name = '';
@endphp
@foreach ($templates as $template)
	@if($group_name != $template->template_type)
		@if ($group_name != '')
			</ul>
		@endif
		<h2>
			<span>{{ ucwords($template->template_type) }}</span>
			<a class='btn btn-primary btn-xs' role="button" href="{{ route('templatesTypeList', ['template_type' => $template->template_type]) }}">View All</a>
		</h2>
		<ul>
	@endif
			<li><a href="{{ route('viewTemplate', ['template_id' => $template->template_id]) }}">{{ $template->template_name }}</a></li>
	@php
		$group_name = $template->template_type;
	@endphp
@endforeach
		</ul>
@endsection
