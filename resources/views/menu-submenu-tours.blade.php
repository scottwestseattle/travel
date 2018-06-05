@if (Auth::user() && Auth::user()->user_type >= 1000)
	<div class="" style="font-size:20px;">
		<table><tr>
			<td style="width:40px;"><a href="#" onclick="window.history.back()"><span style="font-size: 23px" class="glyphCustom glyphicon glyphicon-circle-arrow-left"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/tours/indexadmin/'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>
			<td style="width:40px;"><a href='/tours/add/'><span class="glyphCustom glyphicon glyphicon-plus-sign"></span></a></td>
			@if (isset($record_id))
				@if (isset($record_permalink))
				<td style="width:40px; font-size:20px;"><a href='{{ route('tour.permalink', [$record_permalink]) }}'><span class="glyphCustom glyphicon glyphicon-eye-open"></span></a></td>
				@endif				
				<td style="width:40px;"><a href='/tours/edit/{{$record_id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
				<td style="width:40px;"><a href='/tours/confirmdelete/{{$record_id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
				<td style="width:40px;"><a href='/photos/entries/{{$record_id}}'><span class="glyphCustom glyphicon glyphicon-picture"></span></a></td>
				<td style="width:40px;"><a href='/entries/setlocation/{{$record_id}}'><span class="glyphCustom glyphicon glyphicon-map-marker"></span></a></td>
				<td style="width:40px;"><a href='/entries/publish/{{$record_id}}'><span class="glyphCustom glyphicon glyphicon-flash"></span></a></td>
			@endif
		</tr></table>
	</div>
@endif
