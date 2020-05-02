@if (Auth::user() && Auth::user()->user_type >= 1000)
	<div class="submenu-view">
		<table><tr>
			<td><a href="#" onclick="window.history.back()"><span style="font-size: 23px" class="glyphCustom glyphicon glyphicon-circle-arrow-left"></span></a></td>
			<td><a href='/transactions/summary/'><span class="glyphCustom glyphicon glyphicon-home"></span></a></td>
			@if (null !== session('transactionFilter'))
			<td><a href='/transactions/filter/'><span class="glyphCustom glyphicon glyphicon-filter"></span></a></td>
			@endif			
			<td><a href='/transactions/filter/'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>
			<td><a href='/{{$prefix}}/add/'><span class="glyphCustom glyphicon glyphicon-plus-sign"></span></a></td>			
			<td style="font-size:12px;"><a style="padding:1px 4px; margin:5px 5px 9px 0px;" class="btn btn-primary" href="/{{$prefix}}/index">Visible</a></td>
			<td style="font-size:12px;"><a style="padding:1px 4px; margin:5px 5px 9px 0px;" class="btn btn-primary" href="/{{$prefix}}/index/nonzero">Active</a></td>
			<td style="font-size:12px;"><a style="padding:1px 4px; margin:5px 5px 9px 0px;" class="btn btn-primary" href="/{{$prefix}}/index/all">All</a></td>

			@if (isset($record->id))
				<td><a href='/{{$prefix}}/view/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-eye-open"></span></a></td>
				<td><a href='/{{$prefix}}/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
				<td><a href='/{{$prefix}}/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
			@endif
		</tr></table>
	</div>
@endif
