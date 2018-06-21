@if (Auth::user() && Auth::user()->user_type >= 1000)
	<div class="view-submenu" style="font-size:20px;">
		<table><tr>
			<td style="width:40px;"><a href="#" onclick="window.history.back()"><span style="font-size: 23px" class="glyphCustom glyphicon glyphicon-circle-arrow-left"></span></a></td>
			<td style="width:40px;"><a href='/{{$prefix}}/index/'><span class="glyphCustom glyphicon glyphicon-th"></span></a></td>
			<td style="width:40px;"><a href='/{{$prefix}}/indexadmin/'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>
			<td style="width:40px;"><a href='/{{$prefix}}/add/'><span class="glyphCustom glyphicon glyphicon-plus-sign"></span></a></td>
			@if (isset($record->id))
				<td style="width:40px;"><a href='/{{$prefix}}/view/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-eye-open"></span></a></td>
				<td style="width:40px;"><a href='/{{$prefix}}/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
				<td style="width:40px;"><a href='/{{$prefix}}/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
				<td style="width:40px;"><a href='/{{$prefix}}/publish/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-flash"></span></a></td>
			@endif
		</tr></table>
	</div>
@endif
