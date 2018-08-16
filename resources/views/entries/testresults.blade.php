@extends('layouts.app')

@section('content')

<div class="page-size container">
               
<h1>Test Results ({{count($records)}})</h1>
	
	<table class="table">
	@foreach($records as $record)
		<tr>
			<td><a target="_blank" href="{{$record['url']}}">{{$record['url']}}</a></td>
			<td>{{$record['expected']}}</td>
			<td>{{$record['results']}}</td>
		</tr>
	@endforeach
	</table>
	
</div>

@endsection
