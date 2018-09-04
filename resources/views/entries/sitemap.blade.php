@extends('layouts.app')

@section('content')

<div class="page-size container">
               
<h1>Site Map ({{count($records)}})</h1>

	<form method="POST" action="/test">
		<div class="form-control-big">	

			<table class="table">
					
			@foreach ($records as $record)
				<tr>
					<td><a target="_blank" href="{{$record}}">{{$record}}</a></td>
				</tr>
			@endforeach
			
			</table>
			
		</div>			
			{{ csrf_field() }}
	</form>	
	
</div>

@endsection
