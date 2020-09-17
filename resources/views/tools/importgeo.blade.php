@extends('layouts.app')

@section('content')

<div class="container page-size">

	<h1>Import Geo Data</h1>
	
	<p>1. Download DB3LITE as csv</p>
	<p>2. move the csv file to travel\public\import and upload it to the server</p>
	<p>3. cPanel: copy the live table 'ip2locations', 'structure only', to 'ip2locationsimport'</p>
	<p>4. open the upload file in notepad to get the number of records</p>
	<p>5. import the new records</p>
	<p>6. cPanel: rename ip2locations to ip2locationsOld</p>
	<p>7. cPanel: rename ip2locationsimport to ip2locations</p>
	
	@if ($status['error'])
		<h3>Import Error:</h3>
		<p>{{$status['error']}}</p>
	@else
		<h3>Geo Records:</h3>
		<h3><strong><span id="total">{{number_format($status['startCount'])}}</span></strong></h3>
		<p><span id="error"></span></p>
	@endif
	
	<h3 style="margin-top:20px;"><a type="button" id="addButton" class="btn btn-success" href="/importgeo" onclick="event.preventDefault(); importGeo();" {{($status['error']) ? 'disabled' : ''}}>Import</a></h3>
	
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
			//console.log('ajax status: ' + this.status + ', ' + this.responseText);
		}
					
		if (this.readyState == 4)
		{	
			if (this.status == 200) 
			{
				clearTimeout(timerImportGeo);
				
				// alert(this.responseText			
				var counts = this.responseText.split("|");
				if (counts.length > 2 && counts[2].length > 0) // get error message
				{
					$('#error').text(counts[2]);
					$('#addButton').text('Retry');
					clearTimeout(timerUpdateTotals);
					//console.log(counts);
				}
				else if (Number(counts[1]) > 0)
				{
					// continue
					timerImportGeo = setTimeout(importGeo, 100)
				}
				else
				{
					clearTimeout(timerUpdateTotals);
					
					if (counts.length > 2)
					{
						$('#error').text('error parsing or saving record - check event log');
						$('#error').text(this.responseText);
						$('#addButton').text('Retry');
					}
					else
					{
						$('#addButton').text('Done');
					}
				}
			}
			else
			{
				console.log('ajax status: ' + this.status + ', ' + this.responseText);
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