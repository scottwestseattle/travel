@extends('layouts.app')

@section('content')

<div class="page-size container">
  
	<div>
		
		<div class="text-center drop-box stats-box blue" style="min-width:350px;">
			<h3>Server</h3>
			<p>{{date("F d, Y - H:i:s")}}</p>
			<p>{{'localhost'}} (id={{3}})</p>
			<p class="font-10">{{base_path()}}</p>
			<div class="">
			@if (isset($_COOKIE['debug']) && $_COOKIE['debug'])
				<ul>
					<li><a class="btn btn-danger" href="/d-e-b-u-g" role="button">TURN DEBUG OFF</a></li>
					<li><a class="btn btn-primary" href="/debugtest" role="button">Test</a></li>
					<li><a class="btn btn-primary" href="/about" role="button">About</a></li>
				</ul>
			@else
				<ul>
					<li><a class="btn btn-primary" href="/d-e-b-u-g" role="button">Debug</a></li>
					<li><a class="btn btn-primary" href="/debugtest" role="button">Test</a></li>
					<li><a class="btn btn-primary" href="/about" role="button">About</a></li>
				</ul>
			@endif
			</div>
		</div>	

		<div class="text-center drop-box stats-box green" style="">
			<h3>Client</h3>
			<p>{{'192.0.0.100'}} ({{'Boston, United States'}})</p>
			<img height="60" src="/img/flags/us.png" />
			<ul>
				<li><a href="/expedia">Expedia</a></li>
				<li><a href="/travelocity">Travelocity</a></li>
				<li><a href="/eunoticereset">EU Notice</a></li>
				<li><a href="/hash">Hasher</a></li>
			</ul>
		</div>	
			
	</div>

	<div style="clear:both;"></div>
	
	<h1>Today's Visitors</h1>
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
	
	<h1>Records ({{count($records)}})</h1>

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
