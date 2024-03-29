@extends('layouts.app')

@section('content')

<div class="page-size container">
               
<h1>Test ({{count($records)}})</h1>

	<form method="POST" action="/test">
		<div class="form-control-big">	

			@if (isset($test_server))
				<input type="hidden" name="test_server" value="{{$test_server}}">
			@endif
			
			<button onclick="check(event);">Check All</button>
			<button onclick="check(event, 100)">Check First 100</button>
			<button onclick="check(event, -100)">Check Last 100</button>
			<button onclick="check(event, 1)">Check First Half</button>
			<button onclick="check(event, 2)">Check Last Half</button>
			<button onclick="check(event, -1)">Clear All</button>
			
			<div style="margin:20px 0;">
			@if ($executed)
				<a class="btn btn-success" href="/test">Reload Tests</a>
			@else
				<button type="submit" name="update" class="btn btn-primary">Run Tests</button>
			@endif
			</div>	
			
			<h3>
			Tests Completed: {{$testCount}}
			</h3>

			<table class="table">
				<tr><th>Select</th><th>URL</th><th>Expected</th><th>Results</th></tr>
					
			<?php $count = 0; $errors = 0; ?>
			@foreach ($records as $record)
				@if (!isset($record[2]['success']) || $record[2]['success'] === false)
				<tr>
					<td><input type="checkbox" name="test{{$count}}" id="test{{$count}}" style="margin:0;padding:0;" /></td>
					<td><a target="_blank" href="{{$test_server}}{{$record[1]}}">{{$test_server}}{{$record[1]}}</a></td>
					<td>{{$record[0]}}</td>
					@if (isset($record[2]['results']))
						<td>{{$record[2]['results']}}</td>
						<?php $errors++; ?>
					@endif
				</tr>
				@endif
				<?php $count++; ?>
			@endforeach
			</table>
			
			@if ($executed)
				@if ($errors == 0)
					<h3>All Pages Loaded Correctly</h3>
				@else
					<h3>Errors ({{$errors}}): expected text not found on page</h3>
				@endif
			@endif
			
			<div style="margin:20px 0;">
			@if ($executed)
				<a class="btn btn-success" href="/test">Reload Tests</a>
			@else
				<button type="submit" name="update" class="btn btn-primary">Run Tests</button>
			@endif
			</div>	
			
		</div>			
			{{ csrf_field() }}
	</form>	
	
</div>

@endsection

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
		  var e = document.getElementById('test' + i);
		  e.checked = true;
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
	else if (checkFlag == 100)
	{
		// check first 100			
		for (i = 0; i < count; i++) {
		
			if (i >= 100)
				break;
				
			document.getElementById('test' + i).checked = true;
		}
	}
	else if (checkFlag == -100)
	{		
		// check last 100
		tests = 0;	
		for (i = count - 1; i >= 0 && tests < 100; i--) {
			document.getElementById('test' + i).checked = true;
			tests++;
		}
	}
	else
	{
		// clear all
	}
	
	return false; // prevent default action
}

</script>
