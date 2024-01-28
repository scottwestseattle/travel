@extends('layouts.theme1')
@section('content')
@php
	$hashed2024 = isset($hash) ? $hash['hashed2024'] : null;
@endphp
<div class="container page-size">

	<h1>Search @if (isset($records))({{count($records) + count($photos)}})@endif</h1>
	
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
	
	@if (isset($records) || isset($photos))
		<table class="table table-striped">
			<tbody>
			
			@if (isset($records))
				@foreach($records as $record)
					<tr>
						<td><a href="{{ route('entry.permalink', [$record->permalink]) }}" target="_blank">{{$record->title}}</a></td>
						<td>{{$entryTypes[$record->type_flag]}}</td>
					</tr>
				@endforeach
			@endif

			@if (isset($photos))
				@foreach($photos as $record)
					@php
						$date = isset($record->display_date) ? $record->display_date : $record->created_at;
					@endphp
					<tr>
						<td><a href="/photos/edit/{{$record->id}}" target="_blank">{{$record->alt_text}}</a><div style="font-size:11px;">{{$date}}</div>
</td>
						@if ($record->parent_id > 0)
							<td>Photo</td>
						@else
							<td>Slider</td>
						@endif
					</tr>
				@endforeach
			@endif
			
			</tbody>
		</table>
	@elseif (isset($hash))
		<div id="flash" class="form-group">
			<span id='entry2024'>{{$hashed2024}}</span>
			<a href='#' onclick="javascript:clipboardCopy(event, 'entry2024', 'entry2024')";>
				<span id="" class="glyphCustom glyphicon glyphicon-copy" style="font-size:1.3em; margin-left:5px; display:{{isset($hashed2024) && strlen($hashed2024) > 0 ? 'default' : 'none'}}"></span>
			</a>		
		</div>	
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