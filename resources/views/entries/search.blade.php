@extends('layouts.app')

@section('content')

<div class="container page-size">

	<h1>Search @if (isset($records))({{count($records)}})@endif</h1>
	
	<form method="POST" action="/search">
		<div class="form-group form-control-big">
			
			<input type="text" id="searchText" name="searchText" class="form-control" value="{{$search}}"/>
			
			<div style="clear:both;">				
				<div class="">
					<button type="submit" name="submit" class="btn btn-primary">Search</button>
				</div>
			</div>
			
			{{ csrf_field() }}
		</div>
	</form>
	
	@if (isset($records))
		<table class="table table-striped">
			<tbody>
			@foreach($records as $record)
				<tr>
					<td><a href="{{ route('entry.permalink', [$record->permalink]) }}" target="_blank">{{$record->title}}</a></td>
					<td>{{$entryTypes[$record->type_flag]}}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	@endif
	
</div>

@endsection

<script>
	
window.onload = function(){
	var input = document.getElementById("searchText");
	
	if (!input)
		return;
		
    input.focus();
    input.select();
};

</script>