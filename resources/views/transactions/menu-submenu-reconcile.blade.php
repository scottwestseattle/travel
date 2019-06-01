@if (Auth::user() && Auth::user()->user_type >= 1000)
	<div class="submenu-view">
		<table><tr>
			<td><a href='/reconciles/add/'><span class="glyphCustom glyphicon glyphicon-plus-sign"></span></a></td>
			<td><a href='/transactions/filter'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>
		</tr></table>
	</div>
@endif
