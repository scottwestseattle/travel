@extends('layouts.app')

@section('content')

<div class="page-size container">
               
<h1>Records ({{count($records)}})</h1>

	<div>
		
		<div class="text-center number-box blue" style="">
			<div>Total</div>
			<p style="">21</p>
		</div>	

		<div class="text-center number-box green" style="">
			<div>Unique</div>
			<p style="">17</p>
		</div>	
			
	</div>


	@if (false)
	<form method="POST" action="/test">
		<div class="form-control-big">	

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
				
			<div style="margin:20px 0;">
				<button type="submit" name="update" class="btn btn-primary">Start</button>
			</div>	
			
		</div>			
			{{ csrf_field() }}
	</form>
	@endif	
	
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
	else
	{
		// clear all
	}
	
	return false; // prevent default action
}

</script>
