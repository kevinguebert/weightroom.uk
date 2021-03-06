@extends('layouts.master')

@section('title', 'PR History: ' . $exercise_name)

@section('headerstyle')
<style>
td:first-child, td:nth-child(2) {
    white-space: nowrap;
	width: 1%;
}
td small {
	color: #909090;
	font-style: italic;
}
</style>
@endsection

@section('content')
<h2>PR History: {{ $exercise_name }}</h2>
<p><small><a href="{{ route('viewExercise', ['exercise_name' => rawurlencode($exercise_name_clean)]) }}">&larr; Back to exercise</a></small> | <small><a href="{{ route('exerciseHistory', ['exercise_name' => $exercise_name]) }}">View history</a></small></p>
<table class="table table-striped table-hover">
	<thead>
		<th>Date</th>
		<th class="small">Weight</th>
		<th>1 RM</th>
		<th>2 RM</th>
		<th>3 RM</th>
		<th>4 RM</th>
		<th>5 RM</th>
		<th>6 RM</th>
		<th>7 RM</th>
		<th>8 RM</th>
		<th>9 RM</th>
		<th>10 RM</th>
    @foreach (Auth::user()->user_showextrareps as $j)
        @if ($j != '')
        <th>{{ $j }} RM</th>
        @endif
    @endforeach
	</thead>
	<tbody>
@foreach ($prs as $date => $pr)
		<tr>
			<td>{{ $date }}</td>
			<td class="small">{{ $pr['BW'] }} {{ Auth::user()->user_unit }}</td>
		@for ($i = 1; $i <= 10; $i++)
			<td>{!! (isset($pr[$i])) ? Format::$format_func($pr[$i]) . ' ' . (($format_func == 'correct_weight') ? Auth::user()->user_unit : '') : (($format_func == 'correct_weight') ? '<small>' . Format::correct_weight(\App\Extend\PRs::generateRM($pr['highest'], $pr['highest_reps'], $i)) . ' ' . Auth::user()->user_unit . '</small>' : '') !!}</td>
		@endfor
	    @foreach (Auth::user()->user_showextrareps as $j)
	        @if ($j != '')
	        <td>{{ (isset($pr[$j])) ? Format::$format_func($pr[$j]) . ' ' . Auth::user()->user_unit : '' }}</td>
	        @endif
	    @endforeach
		</tr>
@endforeach
	</tbody>
</table>
@if (isset(Auth::user()->user_showextrareps[0]) && Auth::user()->user_showextrareps[0] != '')
<em>Only reps up to 10 are given estimate vaules as calculations for reps over this often wildly inaccurate.</em>
@endif
@endsection

@section('endjs')
@endsection
