@if (Auth::user() && Auth::user()->user_type >= 1000)
	<div class="" style="font-size:20px;">
		<table><tr>
			<td style="width:40px;"><a href="#" onclick="window.history.back()"><span style="font-size: 23px" class="glyphCustom glyphicon glyphicon-circle-arrow-left"></span></a></td>
			@if (isset($record_id))
				<td style="width:40px; font-size:20px;"><a href='/photos/entries/{{$record_id}}'><span class="glyphCustom glyphicon glyphicon-picture"></span></a></td>
			@endif
		</tr></table>
	</div>
@endif
