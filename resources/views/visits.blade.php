@extends('layouts.app')

@section('content')

<?php 

$header = 'Entries';
if (isset($title))
{
	$header = $title;
}

?>

<div class="page-size container">
	<h1 style="font-size:1.3em;">{{ $header }} ({{ count($entries) }})</h1>
	@if (Auth::check())
		<table class="table table-striped">
			<tbody>
				<tr><th>Created</th><th>IP</th><th>Host</th><th>Referrer</th><th></th></tr>
			@foreach($entries as $entry)
				<tr>
					<td>{{$entry->created_at}}</td>
					<td><a target="_blank" href="https://whatismyipaddress.com/ip/{{$entry->title}}">{{$entry->title}}</a></td>
					<td>{{$entry->description}}</td>
					<td>{{$entry->description_language1}}</td>
					<td><a href='/entries/confirmdelete/{{$entry->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
				</tr>
			@endforeach
			</tbody>
		</table>
	@else
		<h3>You need to log in. <a href="/login">Click here to login</a></h3>
	@endif       
</div>
@endsection
