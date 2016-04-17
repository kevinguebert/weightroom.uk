@extends('layouts.master')

@section('title', 'Dashboard')

@section('headerstyle')
<style>
blockquote.small {
	font-size: 95%;
	padding: 5px 10px;
    margin: 10px;
    max-height: 300px;
    overflow: hidden;;
}
</style>
@endsection

@section('content')
<div class="padding-sb">
<div class="btn-group btn-group-sm" role="group" aria-label="type">
  <a href="{{ route('dashboard') }}" class="btn btn-default">Default</a>
  <a href="{{ route('dashboardAll') }}" class="btn btn-default">View all</a>
</div>
</div>
<table class="table">
<tbody>
@forelse ($logs as $log)
	<tr>
		<td class="logrow">
			<a href="{{ route('viewUser', ['user_name' => $log->user->user_name]) }}">{{ $log->user->user_name }}</a> posted a log {{ $log->log_date->diffForHumans() }}. <a href="{{ route('viewLog', ['date' => $log->log_date->toDateString(), 'user_name' => $log->user->user_name]) }}">View log</a>
            <blockquote class="small">{!! nl2br($log->log_text) !!}</blockquote>
		</td>
	</tr>
@empty
    <tr>
        <td class="logrow">
            There has been no logs posted by people you follow
        </td>
    </tr>
@endforelse
</tbody>
</table>

{!! $logs->render() !!}
@endsection
