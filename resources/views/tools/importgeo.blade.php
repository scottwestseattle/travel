@extends('layouts.app')

@section('content')

<div class="container page-size">

	<h1>Import Geo Data</h1>
	
	@if ($status['error'])
		<h3>Import Error:</h3>
		<p>{{$status['error']}}</p>
	@else
		<h3>Geo Records:</h3>
		<h3><strong><span id="total">{{number_format($status['startCount'])}}</span></strong></h3>
		<p><span id="error"></span></p>
	@endif
	
	<h3 style="margin-top:20px;"><a type="button" id="addButton" class="btn btn-success" href="/importgeo" onclick="event.preventDefault(); importGeo();">Import</a></h3>
	
</div>

@endsection

<script>

var timerUpdateTotals = null;
var timerImportGeo = null;

function importGeo()
{
	url = '/importgeoajax/';

	$('#addButton').text('Importing...');	
	$('#error').text('');
	
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() 
	{
		if (this.status == 200)
		{
			//alert(this.responseText);
		}
					
		if (this.readyState == 4 && this.status == 200) 
		{	
			clearTimeout(timerImportGeo);
			
			// alert(this.responseText			
			var counts = this.responseText.split("|");
			if (Number(counts[1]) > 0)
			{
				timerImportGeo = setTimeout(importGeo, 100)
			}
			else
			{
				clearTimeout(timerUpdateTotals);
				
				if (counts.length > 2)
				{
					$('#error').text(counts[2]);
					$('#addButton').text('Retry');
				}
				else
				{
					$('#addButton').text('Done');
				}
			}
		}
	};
	
	xhttp.open("GET", url, true);
	xhttp.send();

	if (timerUpdateTotals == null)
		timerUpdateTotals = setTimeout(updateTotals, 1000);
}

function updateTotals()
{
	url = '/getgeocount/';

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() 
	{					
		if (this.readyState == 4 && this.status == 200) 
		{	
			var total = this.responseText;
			$('#total').html(Number(total));
			
			clearTimeout(timerUpdateTotals);
			timerUpdateTotals = setTimeout(updateTotals, 1000);
		}
	};
	
	xhttp.open("GET", url, true);
	xhttp.send();		
}





</script>