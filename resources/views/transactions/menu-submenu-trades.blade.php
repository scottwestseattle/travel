@if (Auth::user() && Auth::user()->user_type >= 1000)
<div class="submenu-view">
	<table>
		<tr>
			<td style="width:35px;"><a href="#" onclick="window.history.back()"><span class="glyphCustom glyphicon glyphicon-circle-arrow-left"></span></a></td>
			<td style="font-size:12px;"><a style="padding:1px 4px; margin:5px 5px 9px 0px;" class="btn btn-primary" href="/transactions/filter">Transactions</a></td>
			<td style="font-size:12px;"><a style="padding:1px 4px; margin:5px 5px 9px 0px;" class="btn btn-primary" href="/transactions/positions">Positions</a></td>
			<td style="font-size:12px;"><a style="padding:1px 4px; margin:5px 5px 9px 0px;" class="btn btn-primary" href="/transactions/trades">Trades</a></td>
			<td style="font-size:12px;"><a style="padding:1px 4px; margin:5px 5px 9px 0px;" class="btn btn-primary" href="/transactions/profit-loss">P&L</a></td>
		</tr>
	</table>
</div>
@endif
