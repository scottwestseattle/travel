@if (Auth::user() && Auth::user()->user_type >= 1000)
<div class="submenu-view">
	<table>
		<tr>
			<td><a href="#" onclick="window.history.back()"><span style="font-size: 23px" class="glyphCustom glyphicon glyphicon-circle-arrow-left"></span></a></td>
			<td><a href='/reconciles/index'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>
			<td><a href='/reconciles/'><span class="glyphCustom glyphicon glyphicon-check"></span></a></td>
			<td><a href='/accounts/index/'><span class="glyphCustom glyphicon glyphicon-piggy-bank"></span></a></td>
			@if (isset($record->id))
				<td><a href='/{{$prefix}}/view/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-eye-open"></span></a></td>
				<td><a href='/{{$prefix}}/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
				<td><a href='/{{$prefix}}/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
			@endif
		</tr>
	</table>
</div>
@endif
