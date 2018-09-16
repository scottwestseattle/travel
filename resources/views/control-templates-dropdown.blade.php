
@if(isset($templates) && count($templates) > 0)
	
<script>

function onTemplateChange(id)
{		
	setTemplate('/entries/settemplate/' + id);
}

function setTemplate(url) 
{
	var xhttp = new XMLHttpRequest();
	
	xhttp.onreadystatechange = function() 
	{
		if (this.status == 200)
		{
			//alert(this.responseText);
		}
					
		if (this.readyState == 4 && this.status == 200) 
		{	
			/*
			alert(
				'call response: ' + this.responseText +
				', length: ' + this.responseText.length 
				+ ', char: ' + this.responseText.charCodeAt(0) 
				+ ' ' + this.responseText.charCodeAt(1)
			);
			*/

			window.location.reload();
		}
	};
	
	xhttp.open("GET", url, true);
	xhttp.send();
}

</script>


<div class="float-left" style="margin-top: 10px; margin-left: 20px; max-width:200px;">

	<form method="POST" action="/entries/switch">

		<?php 
			/*
				echo 'search: <br/>';
				window.location.href = parms + cat;
				this.value
				
					@if ($entry->id === Auth::user()->template_id) :
						<option value="{{ $entry->id }}">{{ $entry->title }}</option>
					@else
						<option value="{{ $entry->id }}">{{ $entry->title }}</option>
					@endif
			*/
		?>

		<div class="input-group">
		
			<select name="template" id="template" class="form-control" onchange="onTemplateChange(this.value)">
					<option value="-1">No Layout</option>
				@foreach($templates as $entry)
					<option value="{{ $entry->id }}" {{ ($entry->id === intval(Auth::user()->template_id)) ? 'selected' : '' }}>{{ $entry->title }}</option>
				@endforeach
			</select>			
		</div>
		
		{{ csrf_field() }}
	</form>		

</div>		
@endif

<div class="clear"></div>

