@extends('layouts.app')

@section('content')

<div class="page-size container">
               
@if (isset($sitemap))
<h1>Site Map ({{count($records)}})</h1>
@else
<h1>Test ({{count($records)}})</h1>
@endif

	<form method="POST" action="/test">
		<div class="form-control-big">	

			@if (isset($test_server))
			<input type="hidden" name="test_server" value="{{$test_server}}">
			@endif
			
			@if (!isset($sitemap))
			<button onclick="check(event);">Check All</button>
			<button onclick="check(event, 1)">Check First Half</button>
			<button onclick="check(event, 2)">Check Last Half</button>
			<button onclick="check(event, -1)">Clear All</button>
			
			<div style="margin:20px 0;">
				<button type="submit" name="update" class="btn btn-primary">Run Tests</button>
				&nbsp;&nbsp;&nbsp;<a href="/test">Reload Tests</a>
			</div>	

			<table class="table">
				<tr><th>Select</th><th>URL</th><th>Expected</th><th>Results</th></tr>
			@else
			<table class="table">
			@endif
					
			@for ($i = 0; $i < count($records); $i++)
				@if (!$executed || $records[$i][2] != '')
				<tr>
					@if (isset($sitemap))
						<td><a target="_blank" href="{{$records[$i]}}">{{$records[$i]}}</a></td>
						<td></td>
						<td></td>
					@else
						<td><input type="checkbox" name="test{{$i}}" id="test{{$i}}" style="margin:0;padding:0;" /></td>
						<td><a target="_blank" href="{{$test_server}}{{$records[$i][1]}}">{{$test_server}}{{$records[$i][1]}}</a></td>
						<td>{{$records[$i][0]}}</td>
						<td>{{$records[$i][2]}}</td>
					@endif
				</tr>
				@endif
			@endfor
			</table>
			
			@if (!isset($sitemap))
			<div style="margin:20px 0;">
				<button type="submit" name="update" class="btn btn-primary">Run Tests</button>
			</div>	
			@endif
			
		</div>			
			{{ csrf_field() }}
	</form>	
	
</div>

@endsection

@if (!isset($sitemap))
<script>
function check(event, checkFlag = 0)
{		
	event.preventDefault();
	
	var inputs = document.getElementsByTagName("input");
	count = 0;
	for(var i = 0; i < inputs.length; i++) {
		if (inputs[i].type == "checkbox") {
			inputs[i].checked = false;
			count++;
		} 
	}
	
	if (checkFlag == 1)
	{
		// check first half
		count /= 2;

		for (i = 0; i < count; i++) {
		  document.getElementById('test' + i).checked = true;
		}
	}
	else if (checkFlag == 2)
	{
		// check second half
		half = count / 2;

		for (i = (count - 1); i >= half; i--) {
		  var el = document.getElementById('test' + i).checked = true;
		}
	}
	else if (checkFlag == 0)
	{
		// check all
		for (i = 0; i < count; i++) {
		  document.getElementById('test' + i).checked = true;
		}
	}
	else
	{
		// clear all
	}
	
	return false; // prevent default action
}

</script>
@endif