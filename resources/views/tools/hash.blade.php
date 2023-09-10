@php
	if (isset($hashed))
	{
		$year = isset($year) ? $year : '';
	}
	else
	{
		$hash = '';
		$hashed = '';
		$year = date("Y");
	}
@endphp
@extends('layouts.app')

@section('content')

<div style="margin:0;padding:0; margin-top:20px; margin-left:50px;" class="page-size container">
               
<form method="POST" action="/hasher">

	<div class="form-group">
		<label name="" class="">Enter Text:</label>	
		<input type="text" name="hash" class="form-control" style="width: 90%; max-width:200px;" value="{{ $hash }}" autofocus />
		<input type="number" name="year" size="4" class="form-control" style="width: 90%; max-width:200px;" value="{{ $year }}" autofocus />
	</div>
	
	<div id="flash" class="form-group">
		<span id='entry'>{{ $hashed }}</span>
		<a href='#' onclick="javascript:clipboardCopy(event, 'entry', 'entry')";>
			<span id="" class="glyphCustom glyphicon glyphicon-copy" style="font-size:1.3em; margin-left:5px; display:{{isset($hashed) && strlen($hashed) > 0 ? 'default' : 'none'}}"></span>
		</a>		
	</div>	
		
	<div class="form-group">
		<button type="submit" name="submit" class="btn btn-primary">Submit</button>
	</div>
	
{{ csrf_field() }}
</form>

</div>
@endsection
