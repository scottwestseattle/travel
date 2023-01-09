@extends('layouts.export')
@section('content')
@php
	$record = $parms['record'];
	$records = $parms['records'];
@endphp
<div class="page-size container">  
@guest
@else
	<h2>{{$record->title}}</h2>
	<p>{!! nl2br($record->description) !!}</p>
	@foreach($records as $record)
		<h3>{{$record->title}}</h3>
		<p style="font-size:1.2em;">{{$record->display_date}}</p>
		<p>{!! nl2br($record->description) !!}</p>
	@endforeach
@endguest
@endsection
