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
			@else
			<td style="font-size:12px; width:60px;">
				<a style="font-size:12px; padding:1px 4px; margin:5px 5px 9px 0px;" class="btn btn-primary" href="/transactions/trades">Trades</a>
			</td>
			@endif
		</tr></table>
	</div>
@endif
