@extends('layouts.app')

@section('content')

<div class="page-size container">
               
<h1>Site Map ({{count($records)}})</h1>

	<div class="form-control-big">	

		<p>Site Map written to file: {{$filename}}</p>
	
		<table class="table">
				
			@foreach ($records as $record)
			<tr>
				<td><a target="_blank" href="{{$record}}">{{$record}}</a></td>
			</tr>
			@endforeach
		
		</table>
		
	</div>			
	
</div>

@endsection
