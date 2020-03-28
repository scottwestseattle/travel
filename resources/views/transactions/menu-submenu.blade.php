@if (Auth::user() && Auth::user()->user_type >= 1000)
	<div class="submenu-view">
		<table><tr>
			@component('menu-submenu-general')@endcomponent
			<td><a href='/{{$prefix}}/add/'><span class="glyphCustom glyphicon glyphicon-plus-sign"></span></a></td>
			@if (isset($record->id))
				<td><a href='/{{$prefix}}/view/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-eye-open"></span></a></td>
				<td><a href='/{{$prefix}}/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
				<td><a href='/photos/direct/{{$record->id}}/{{PHOTO_TYPE_RECEIPT}}'><span class="glyphCustom glyphicon glyphicon-picture"></span></a></td>
				<td><a href='/{{$prefix}}/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
			@endif
			<td style="font-size:12px; width:60px;"><a href='/{{$prefix}}/trades/'>Trades</a></td>
			<td style="font-size:12px; width:75px;"><a href='/{{$prefix}}/add-trade/'>Add Trade</a></td>
		</tr></table>
	</div>
@endif
